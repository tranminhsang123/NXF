<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\SecuritySetting;
use App\Models\SystemLog;
use App\Models\User;
use App\Services\OnboardingQuickWinService;
use App\Support\OnboardingOptions;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $lockMessage = trim((string) SecuritySetting::get('devtools_lock_message', '')) ?: 'Tài khoản đã bị khóa. Vui lòng liên hệ quản trị viên.';

        $user = User::where('email', $credentials['email'])->first();
        if ($user && $user->isLocked()) {
            return back()->withErrors(['email' => $lockMessage])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            if ($user->isLocked()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors(['email' => $lockMessage])->onlyInput('email');
            }

            $request->session()->regenerate();

            // Nếu là admin thì vào admin dashboard (có flash để hiển thị intro)
            if ($user->role === 'admin') {
                $request->session()->flash('show_admin_intro', true);
                return redirect()->route('admin.dashboard');
            }

            if (! $user->onboarding_completed_at) {
                return redirect()->route('onboarding.edit');
            }

            return redirect()->intended(route('user.dashboard'));
        }

        SystemLog::add(null, 'failed_login', 'Đăng nhập thất bại: ' . $credentials['email'], [
            'email' => $credentials['email'],
            'ip' => $request->ip(),
        ]);

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register', [
            'levelOptions' => OnboardingOptions::levels(),
            'goalOptions' => OnboardingOptions::goals(),
            'dailyMinuteOptions' => OnboardingOptions::dailyMinuteOptions(),
            'learningReasonOptions' => OnboardingOptions::learningReasons(),
            'placementQuestions' => OnboardingOptions::placementQuestions(),
        ]);
    }

    public function register(Request $request, OnboardingQuickWinService $quickWinService)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'onboarding_level' => ['required', Rule::in(OnboardingOptions::levelKeys())],
            'jlpt_goal' => ['required', Rule::in(OnboardingOptions::goalKeys())],
            'daily_study_minutes' => ['required', 'integer', 'min:'.OnboardingOptions::MIN_DAILY_MINUTES, 'max:'.OnboardingOptions::MAX_DAILY_MINUTES],
            'learning_reasons' => ['nullable', 'array'],
            'learning_reasons.*' => ['string', Rule::in(OnboardingOptions::learningReasonKeys())],
            'placement_answers' => ['nullable', 'array'],
            'placement_answers.*' => ['nullable', 'string', 'max:100'],
            'email_reminders_enabled' => ['nullable', 'boolean'],
        ]);

        $placement = OnboardingOptions::evaluatePlacement($data['placement_answers'] ?? []);
        $level = $placement['answered'] ? $placement['level'] : $data['onboarding_level'];

        $user = User::create(array_merge([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user',
        ], OnboardingOptions::preferencesForCreate([
            'onboarding_level' => $level,
            'jlpt_goal' => $data['jlpt_goal'],
            'daily_study_minutes' => $data['daily_study_minutes'],
            'learning_reasons' => $data['learning_reasons'] ?? [],
            'placement_test_score' => $placement['score'],
            'placement_test_level' => $placement['level'],
            'placement_answers' => $placement['answers'],
            'email_reminders_enabled' => $request->has('email_reminders_enabled'),
        ])));

        Notification::createForAdmins('new_user', 'User mới đăng ký', $user->name . ' (' . $user->email . ') vừa đăng ký tài khoản.', ['user_id' => $user->id]);
        SystemLog::add($user, 'user_registered', $user->name . ' (' . $user->email . ') đăng ký tài khoản.', ['email' => $user->email]);

        Auth::login($user);

        $quickWinService->markStarted($user);

        return redirect()
            ->route('onboarding.result')
            ->with('status', 'Tài khoản đã sẵn sàng. Bắt đầu bằng một bài ngắn để có quick win đầu tiên.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function redirectToGoogle(Request $request)
    {
        $clientId = trim((string) config('services.google.client_id'));
        $redirectUri = trim((string) config('services.google.redirect'));

        if ($clientId === '' || $redirectUri === '') {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Google login chưa được cấu hình đầy đủ trên server.']);
        }

        $state = Str::random(40);
        $request->session()->put('google_oauth_state', $state);

        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'prompt' => 'select_account',
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?'.$query);
    }

    public function handleGoogleCallback(Request $request)
    {
        $expectedState = (string) $request->session()->pull('google_oauth_state', '');
        $state = (string) $request->query('state', '');

        if ($expectedState === '' || ! hash_equals($expectedState, $state)) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Phiên đăng nhập Google không hợp lệ. Vui lòng thử lại.']);
        }

        if ($request->filled('error')) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Đăng nhập Google bị hủy hoặc bị từ chối.']);
        }

        $code = (string) $request->query('code', '');
        if ($code === '') {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Không nhận được mã xác thực từ Google.']);
        }

        $tokenResponse = $this->googleHttpClient()
            ->asForm()
            ->timeout(15)
            ->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => (string) config('services.google.client_id'),
                'client_secret' => (string) config('services.google.client_secret'),
                'redirect_uri' => (string) config('services.google.redirect'),
                'grant_type' => 'authorization_code',
            ]);

        if (! $tokenResponse->ok()) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Không thể xác thực Google. Vui lòng thử lại.']);
        }

        $idToken = (string) ($tokenResponse->json('id_token') ?? '');
        if ($idToken === '') {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Google không trả về ID token hợp lệ.']);
        }

        $tokenInfo = $this->googleHttpClient()
            ->timeout(10)
            ->get('https://oauth2.googleapis.com/tokeninfo', [
                'id_token' => $idToken,
            ]);

        if (! $tokenInfo->ok()) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Google token không hợp lệ hoặc đã hết hạn.']);
        }

        $tokenPayload = $tokenInfo->json();
        $issuer = (string) ($tokenPayload['iss'] ?? '');
        if (! in_array($issuer, ['accounts.google.com', 'https://accounts.google.com'], true)) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Nguồn token Google không hợp lệ.']);
        }

        if (($tokenPayload['email_verified'] ?? 'false') !== 'true') {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Email Google chưa được xác minh.']);
        }

        $email = (string) ($tokenPayload['email'] ?? '');
        if ($email === '') {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Google token thiếu email.']);
        }

        $audience = (string) ($tokenPayload['aud'] ?? '');
        $configuredClientId = trim((string) config('services.google.client_id'));
        if ($configuredClientId !== '' && $audience !== $configuredClientId) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Google client không khớp cấu hình.']);
        }

        $user = User::where('email', $email)->first();
        $createdFromGoogle = false;
        if (! $user) {
            $name = trim((string) ($tokenPayload['name'] ?? 'Google User'));
            $user = User::create(array_merge([
                'name' => $name !== '' ? $name : 'Google User',
                'email' => $email,
                'password' => Str::password(40),
                'role' => 'user',
            ], OnboardingOptions::preferencesForCreate([], false)));
            $createdFromGoogle = true;
        }

        $lockMessage = trim((string) SecuritySetting::get('devtools_lock_message', '')) ?: 'Tài khoản đã bị khóa. Vui lòng liên hệ quản trị viên.';
        if ($user->isLocked()) {
            return redirect()->route('login')->withErrors(['email' => $lockMessage]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        if ($user->role === 'admin') {
            $request->session()->flash('show_admin_intro', true);

            return redirect()->route('admin.dashboard');
        }

        if ($createdFromGoogle || ! $user->onboarding_completed_at) {
            return redirect()->route('onboarding.edit');
        }

        return redirect()->intended(route('user.dashboard'));
    }

    private function googleHttpClient(): PendingRequest
    {
        $request = Http::acceptJson();

        if (! (bool) config('services.google.verify_ssl', true)) {
            $request = $request->withoutVerifying();
        }

        return $request;
    }
}
