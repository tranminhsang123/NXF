@extends('adminlayout.app')

@section('admin_title', 'Dashboard')

@section('content')
@php
    $totalKanjis = max((int) ($stats['total_kanjis'] ?? 0), 1);
@endphp

<div class="space-y-5 md:space-y-7">
    <div class="flex flex-col justify-between gap-3 md:flex-row md:items-end">
        <div>
            <p class="text-sm font-semibold text-red-600">Admin overview</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-950 md:text-3xl">Dashboard</h1>
            <p class="mt-1 max-w-2xl text-sm text-slate-500 md:text-base">
                Theo dõi nhanh người dùng, nội dung học và hoạt động gần đây.
            </p>
        </div>
        <div class="inline-flex w-fit items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 shadow-sm">
            <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M8 2v4"></path>
                <path d="M16 2v4"></path>
                <path d="M3 10h18"></path>
                <path d="M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"></path>
            </svg>
            {{ now()->format('d/m/Y') }}
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4 lg:gap-5">
        <div class="admin-card admin-card-hover rounded-lg p-4 sm:p-5">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-xs font-semibold text-slate-500 sm:text-sm">Users</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">{{ $stats['total_users'] }}</p>
                </div>
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-700 sm:h-12 sm:w-12">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="admin-card admin-card-hover rounded-lg p-4 sm:p-5">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-xs font-semibold text-slate-500 sm:text-sm">Ký tự</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">{{ $stats['total_alphabets'] }}</p>
                </div>
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-rose-50 text-rose-700 sm:h-12 sm:w-12">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M4 19V5a2 2 0 0 1 2-2h9l5 5v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2Z"></path>
                        <path d="M14 3v6h6"></path>
                        <path d="M8 14h8"></path>
                        <path d="M8 17h5"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="admin-card admin-card-hover rounded-lg p-4 sm:p-5">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-xs font-semibold text-slate-500 sm:text-sm">Kanji</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">{{ $stats['total_kanjis'] }}</p>
                </div>
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-50 text-amber-700 sm:h-12 sm:w-12">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M4 5h16"></path>
                        <path d="M12 5v14"></path>
                        <path d="M7 10h10"></path>
                        <path d="M8 19h8"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="admin-card admin-card-hover rounded-lg p-4 sm:p-5">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-xs font-semibold text-slate-500 sm:text-sm">Bài Minna</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">{{ $stats['total_minna_lessons'] }}</p>
                </div>
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-700 sm:h-12 sm:w-12">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <section class="admin-card rounded-lg p-4 sm:p-5 xl:col-span-2">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-950 sm:text-lg">Kanji theo cấp độ</h2>
                    <p class="text-sm text-slate-500">Tỷ lệ phân bổ trong toàn bộ dữ liệu Kanji.</p>
                </div>
            </div>

            <div class="space-y-4">
                @foreach(['N5', 'N4', 'N3', 'N2', 'N1'] as $level)
                    @php
                        $count = (int) ($stats['kanjis_by_level'][$level] ?? 0);
                        $percent = $totalKanjis > 0 ? round($count / $totalKanjis * 100, 1) : 0;
                    @endphp
                    <div>
                        <div class="mb-1.5 flex items-center justify-between gap-3 text-sm">
                            <span class="font-semibold text-slate-700">{{ $level }}</span>
                            <span class="font-bold text-slate-950">{{ $count }} <span class="font-medium text-slate-400">({{ $percent }}%)</span></span>
                        </div>
                        <div class="h-2.5 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-red-600" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="admin-card rounded-lg p-4 sm:p-5">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-950 sm:text-lg">Users mới nhất</h2>
                    <p class="text-sm text-slate-500">Tài khoản vừa tham gia hệ thống.</p>
                </div>
            </div>

            <div class="space-y-3">
                @forelse($stats['recent_users'] as $user)
                    <div class="flex items-center justify-between gap-3 rounded-lg border border-slate-100 bg-slate-50/80 p-3">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-slate-950">{{ $user->name }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                        </div>
                        <span class="shrink-0 rounded-full px-2 py-1 text-[11px] font-bold
                            {{ $user->role == 'admin' ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-700' }}">
                            {{ $user->role }}
                        </span>
                    </div>
                @empty
                    <div class="rounded-lg border border-dashed border-slate-200 p-4 text-center text-sm text-slate-500">
                        Chưa có user nào
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <section class="admin-card rounded-lg p-4 sm:p-5">
        <div class="mb-4">
            <h2 class="text-base font-bold text-slate-950 sm:text-lg">Thao tác nhanh</h2>
            <p class="text-sm text-slate-500">Đi thẳng tới các tác vụ nội dung thường dùng.</p>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @adminCan('alphabets.edit')
                <a href="{{ route('admin.alphabets.create') }}"
                   class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-700">
                    <span aria-hidden="true">+</span>
                    Thêm ký tự
                </a>
            @endadminCan
            @adminCan('kanjis.edit')
                <a href="{{ route('admin.kanjis.create') }}"
                   class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-amber-600 px-4 py-3 text-sm font-bold text-white shadow-sm hover:bg-amber-700">
                    <span aria-hidden="true">+</span>
                    Thêm Kanji
                </a>
            @endadminCan
            @adminCan('minna.edit')
                <a href="{{ route('admin.minna.create') }}"
                   class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-3 text-sm font-bold text-white shadow-sm hover:bg-emerald-700">
                    <span aria-hidden="true">+</span>
                    Thêm bài Minna
                </a>
            @endadminCan
            @adminCan('course_data.edit')
                <a href="{{ route('admin.course-data.create') }}"
                   class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-slate-900 px-4 py-3 text-sm font-bold text-white shadow-sm hover:bg-slate-800">
                    <span aria-hidden="true">+</span>
                    Thêm Course Data
                </a>
            @endadminCan
        </div>
    </section>
</div>
@endsection
