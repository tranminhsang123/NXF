<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chủ đề thực tế</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <main class="min-h-screen pt-24 pb-12">
        <div class="container mx-auto max-w-7xl px-4">
            <section class="mb-8 rounded-2xl border border-slate-800 bg-slate-950 p-6 text-white shadow-sm md:p-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-wide text-red-200">Nội dung học theo tình huống</p>
                        <h1 class="mt-2 text-3xl font-extrabold tracking-tight md:text-4xl">Chủ đề thực tế</h1>
                        <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-300">
                            Luyện các tình huống người học thường cần ngoài giáo trình: du lịch, nhà hàng, khách sạn, công việc, anime, du học và hội thoại đời sống.
                        </p>
                    </div>
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="rounded-xl bg-white/10 px-4 py-3">
                            <p class="text-2xl font-black">{{ $topics->count() }}</p>
                            <p class="text-xs font-semibold text-slate-300">Chủ đề</p>
                        </div>
                        <div class="rounded-xl bg-white/10 px-4 py-3">
                            <p class="text-2xl font-black">{{ $topics->sum(fn ($topic) => count($topic['vocabulary'])) }}</p>
                            <p class="text-xs font-semibold text-slate-300">Từ vựng</p>
                        </div>
                        <div class="rounded-xl bg-white/10 px-4 py-3">
                            <p class="text-2xl font-black">{{ $topics->sum(fn ($topic) => count($topic['quiz'])) }}</p>
                            <p class="text-xs font-semibold text-slate-300">Quiz</p>
                        </div>
                    </div>
                </div>
            </section>

            @if(!empty($recommendedTopics))
                <section class="mb-8">
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-extrabold text-gray-950">Gợi ý cho bạn</h2>
                            <p class="mt-1 text-sm text-gray-600">Ưu tiên theo lý do học trong onboarding.</p>
                        </div>
                    </div>
                    <div class="grid gap-4 md:grid-cols-3">
                        @foreach($recommendedTopics as $topic)
                            <a href="{{ $topic['url'] }}" class="rounded-2xl border border-red-200 bg-white p-5 shadow-sm hover:border-red-300 hover:shadow-md">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-bold text-red-600">{{ $topic['level'] }}</p>
                                        <h3 class="mt-2 text-lg font-extrabold text-gray-950">{{ $topic['title'] }}</h3>
                                    </div>
                                    <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-bold text-red-700">{{ $topic['duration_minutes'] }} phút</span>
                                </div>
                                <p class="mt-3 text-sm leading-6 text-gray-600">{{ $topic['subtitle'] }}</p>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <section>
                <div class="mb-4">
                    <h2 class="text-xl font-extrabold text-gray-950">Tất cả chủ đề</h2>
                </div>
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($topics as $topic)
                        <a href="{{ $topic['url'] }}" class="group rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-red-200 hover:shadow-md">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ $topic['level'] }}</p>
                                    <h3 class="mt-2 text-xl font-extrabold text-gray-950 group-hover:text-red-700">{{ $topic['title'] }}</h3>
                                </div>
                                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-bold text-gray-700">{{ $topic['duration_minutes'] }} phút</span>
                            </div>
                            <p class="mt-3 text-sm leading-6 text-gray-600">{{ $topic['subtitle'] }}</p>
                            <div class="mt-5 grid grid-cols-4 gap-2 text-center">
                                <div class="rounded-lg bg-gray-50 px-2 py-2">
                                    <p class="text-sm font-black text-gray-900">{{ count($topic['vocabulary']) }}</p>
                                    <p class="text-[11px] font-semibold text-gray-500">Từ</p>
                                </div>
                                <div class="rounded-lg bg-gray-50 px-2 py-2">
                                    <p class="text-sm font-black text-gray-900">{{ count($topic['patterns']) }}</p>
                                    <p class="text-[11px] font-semibold text-gray-500">Mẫu</p>
                                </div>
                                <div class="rounded-lg bg-gray-50 px-2 py-2">
                                    <p class="text-sm font-black text-gray-900">{{ count($topic['dialogue']) }}</p>
                                    <p class="text-[11px] font-semibold text-gray-500">Câu</p>
                                </div>
                                <div class="rounded-lg bg-gray-50 px-2 py-2">
                                    <p class="text-sm font-black text-gray-900">{{ count($topic['quiz']) }}</p>
                                    <p class="text-[11px] font-semibold text-gray-500">Quiz</p>
                                </div>
                            </div>
                            <div class="mt-5 flex flex-wrap gap-2">
                                @foreach($topic['tags'] as $tag)
                                    <span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-bold text-red-700">{{ str_replace('_', ' ', $tag) }}</span>
                                @endforeach
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        </div>
    </main>

    @include('layouts.footer')
</body>
</html>
