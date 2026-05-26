<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lộ trình cá nhân</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <div class="min-h-screen flex items-center justify-center px-4 pt-28 pb-12">
        <div class="w-full max-w-5xl bg-white rounded-lg p-8 border border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900 mb-1 text-center">Thiết lập lộ trình cá nhân</h1>
            <p class="text-sm text-gray-500 mb-6 text-center">Trả lời nhanh để hệ thống gợi ý bài tiếp theo đúng với mục tiêu của bạn.</p>

            @if ($errors->any())
                <div class="mb-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded px-3 py-2">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('onboarding.update') }}" class="space-y-5">
                @csrf

                @include('auth._onboarding-fields', [
                    'user' => $user,
                    'levelOptions' => $levelOptions,
                    'goalOptions' => $goalOptions,
                    'dailyMinuteOptions' => $dailyMinuteOptions,
                    'learningReasonOptions' => $learningReasonOptions,
                    'placementQuestions' => $placementQuestions,
                ])

                <button type="submit"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-medium rounded py-2.5 transition">
                    Lưu và bắt đầu bài đầu tiên
                </button>
            </form>
        </div>
    </div>

    @include('layouts.footer')
</body>
</html>
