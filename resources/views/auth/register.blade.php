<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <div class="min-h-screen flex items-center justify-center px-4 pt-28 pb-12">
        <div class="w-full max-w-5xl bg-white rounded-lg p-8 border border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900 mb-1 text-center">Đăng ký</h1>
            <p class="text-sm text-gray-500 mb-6 text-center">Tạo tài khoản để lưu tiến độ học</p>

            @if ($errors->any())
                <div class="mb-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded px-3 py-2">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Họ tên</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Mật khẩu</label>
                    <input id="password" name="password" type="password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Nhập lại mật khẩu</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm">
                </div>

                @include('auth._onboarding-fields', [
                    'user' => null,
                    'levelOptions' => $levelOptions ?? [],
                    'goalOptions' => $goalOptions ?? [],
                    'dailyMinuteOptions' => $dailyMinuteOptions ?? [10, 20, 30, 45, 60],
                    'learningReasonOptions' => $learningReasonOptions ?? [],
                    'placementQuestions' => $placementQuestions ?? [],
                ])

                <button type="submit"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-medium rounded py-2.5 mt-2 transition">
                    Đăng ký và bắt đầu bài đầu tiên
                </button>
            </form>

            <p class="mt-6 text-sm text-gray-500 text-center">
                Đã có tài khoản?
                <a href="{{ route('login') }}" class="text-red-600 hover:text-red-700 font-medium">
                    Đăng nhập
                </a>
            </p>
        </div>
    </div>

    @include('layouts.footer')
</body>
</html>
