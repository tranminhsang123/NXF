<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\SecuritySetting;
use App\Models\SystemLog;
use App\Models\User;
use App\Support\OnboardingOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'device_name' => ['nullable', 'string', 'max:255'],
            'onboarding_level' => ['nullable', Rule::in(OnboardingOptions::levelKeys())],
            'jlpt_goal' => ['nullable', Rule::in(OnboardingOptions::goalKeys())],
            'daily_study_minutes' => ['nullable', 'integer', 'min:'.OnboardingOptions::MIN_DAILY_MINUTES, 'max:'.OnboardingOptions::MAX_DAILY_MINUTES],
            'email_reminders_enabled' => ['nullable', 'boolean'],
        ]);

        $hasOnboarding = $request->filled('onboarding_level')
            || $request->filled('jlpt_goal')
            || $request->filled('daily_study_minutes');

        $user = User::create(array_merge([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user',
        ], OnboardingOptions::preferencesForCreate([
            'onboarding_level' => $data['onboarding_level'] ?? null,
            'jlpt_goal' => $data['jlpt_goal'] ?? null,
            'daily_study_minutes' => $data['daily_study_minutes'] ?? null,
            'email_reminders_enabled' => $request->has('email_reminders_enabled')
                ? $request->boolean('email_reminders_enabled')
                : true,
        ], $hasOnboarding)));

        Notification::createForAdmins(
            'new_user',
            'User mới đăng ký',
            $user->name.' ('.$user->email.') vừa đăng ký tài khoản.',
            ['user_id' => $user->id]
        );

        SystemLog::add(
            $user,
            'user_registered_api',
            $user->name.' ('.$user->email.') đăng ký tài khoản qua mobile API.',
            ['email' => $user->email]
        );

        $token = $user->createToken($data['device_name'] ?? 'mobile-app')->plainTextToken;

        return response()->json([
            'message' => 'Đăng ký thành công.',
            'token' => $token,
            'user' => $this->userPayload($user),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email hoặc mật khẩu không đúng.'],
            ]);
        }

        if ($user->isLocked()) {
            $lockMessage = trim((string) SecuritySetting::get('devtools_lock_message', ''))
                ?: 'Tài khoản đã bị khóa. Vui lòng liên hệ quản trị viên.';

            return response()->json([
                'message' => $lockMessage,
            ], 423);
        }

        $token = $user->createToken($data['device_name'] ?? 'mobile-app')->plainTextToken;

        return response()->json([
            'message' => 'Đăng nhập thành công.',
            'token' => $token,
            'user' => $this->userPayload($user),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json([
            'user' => $this->userPayload($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($request->user()?->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        } elseif ($user) {
            $user->tokens()->delete();
        }

        return response()->json([
            'message' => 'Đăng xuất thành công.',
        ]);
    }

    public function loginWithGoogle(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_token' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $tokenInfo = Http::timeout(10)
            ->acceptJson()
            ->get('https://oauth2.googleapis.com/tokeninfo', [
                'id_token' => $data['id_token'],
            ]);

        if (! $tokenInfo->ok()) {
            throw ValidationException::withMessages([
                'id_token' => ['Google token không hợp lệ hoặc đã hết hạn.'],
            ]);
        }

        $tokenPayload = $tokenInfo->json();

        $issuer = (string) ($tokenPayload['iss'] ?? '');
        if (! in_array($issuer, ['accounts.google.com', 'https://accounts.google.com'], true)) {
            throw ValidationException::withMessages([
                'id_token' => ['Nguồn token Google không hợp lệ.'],
            ]);
        }

        if (($tokenPayload['email_verified'] ?? 'false') !== 'true') {
            throw ValidationException::withMessages([
                'id_token' => ['Email Google chưa được xác minh.'],
            ]);
        }

        $email = (string) ($tokenPayload['email'] ?? '');
        if ($email === '') {
            throw ValidationException::withMessages([
                'id_token' => ['Google token thiếu email.'],
            ]);
        }

        $configuredClientId = trim((string) config('services.google.client_id'));
        $audience = (string) ($tokenPayload['aud'] ?? '');
        if ($configuredClientId !== '' && $audience !== $configuredClientId) {
            throw ValidationException::withMessages([
                'id_token' => ['Google client không khớp cấu hình backend.'],
            ]);
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            $name = trim((string) ($tokenPayload['name'] ?? 'Google User'));

            $user = User::create(array_merge([
                'name' => $name !== '' ? $name : 'Google User',
                'email' => $email,
                'password' => Str::password(40),
                'role' => 'user',
            ], OnboardingOptions::preferencesForCreate([], false)));
        }

        if ($user->isLocked()) {
            $lockMessage = trim((string) SecuritySetting::get('devtools_lock_message', ''))
                ?: 'Tài khoản đã bị khóa. Vui lòng liên hệ quản trị viên.';

            return response()->json([
                'message' => $lockMessage,
            ], 423);
        }

        $token = $user->createToken($data['device_name'] ?? 'mobile-app-google')->plainTextToken;

        return response()->json([
            'message' => 'Đăng nhập Google thành công.',
            'token' => $token,
            'user' => $this->userPayload($user),
        ]);
    }

    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'onboarding' => OnboardingOptions::summaryFor($user),
        ];
    }
}
