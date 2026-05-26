<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê học tập - {{ $user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <div class="pt-24 pb-12 min-h-screen">
        <div class="container mx-auto px-4 max-w-5xl">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-1">
                        Thống kê học tập
                    </h1>
                    <p class="text-gray-600 text-sm md:text-base">
                        Biểu đồ bài hoàn thành theo ngày, theo tuần và tổng từ vựng.
                    </p>
                </div>
                <a href="{{ route('user.dashboard') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700">
                    &larr; Dashboard
                </a>
            </div>

            {{-- Tóm tắt --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <p class="text-sm text-gray-600 mb-1">Tổng bài Minna đã hoàn thành</p>
                    <p class="text-3xl font-bold text-red-600">{{ $summary['completed_lessons'] }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <p class="text-sm text-gray-600 mb-1">Tổng từ vựng (ước tính)</p>
                    <p class="text-3xl font-bold text-green-600">{{ number_format($summary['total_vocab_estimate']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Từ các bài đã hoàn thành</p>
                </div>
            </div>

            {{-- Biểu đồ theo ngày (7 ngày) --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Bài hoàn thành theo ngày (7 ngày qua)</h2>
                <div class="h-64">
                    <canvas id="chart-by-day"></canvas>
                </div>
            </div>

            {{-- Biểu đồ theo tuần (8 tuần) --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Bài hoàn thành theo tuần (8 tuần qua)</h2>
                <div class="h-64">
                    <canvas id="chart-by-week"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        const byDay = @json($byDay);
        const byWeek = @json($byWeek);

        const red = 'rgb(220, 38, 38)';
        const redLight = 'rgba(220, 38, 38, 0.2)';

        if (document.getElementById('chart-by-day')) {
            new Chart(document.getElementById('chart-by-day'), {
                type: 'bar',
                data: {
                    labels: byDay.labels,
                    datasets: [{
                        label: 'Số bài hoàn thành',
                        data: byDay.data,
                        backgroundColor: redLight,
                        borderColor: red,
                        borderWidth: 1,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 },
                        },
                    },
                },
            });
        }

        if (document.getElementById('chart-by-week')) {
            new Chart(document.getElementById('chart-by-week'), {
                type: 'bar',
                data: {
                    labels: byWeek.labels,
                    datasets: [{
                        label: 'Số bài hoàn thành',
                        data: byWeek.data,
                        backgroundColor: 'rgba(34, 197, 94, 0.2)',
                        borderColor: 'rgb(34, 197, 94)',
                        borderWidth: 1,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 },
                        },
                        x: {
                            ticks: { maxRotation: 45, minRotation: 45 },
                        },
                    },
                },
            });
        }
    </script>
    @include('layouts.footer')
</body>
</html>
