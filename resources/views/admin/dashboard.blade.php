@extends('adminlayout.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Dashboard</h1>
    <p class="text-gray-600 mt-2">Chào mừng trở lại, Admin!</p>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Users -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Tổng số Users</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_users'] }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-4">
                <span class="text-3xl">👥</span>
            </div>
        </div>
    </div>

    <!-- Total Alphabets -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Tổng số Ký tự</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_alphabets'] }}</p>
            </div>
            <div class="bg-red-100 rounded-full p-4">
                <span class="text-3xl">🔤</span>
            </div>
        </div>
    </div>

    <!-- Total Kanjis -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Tổng số Kanji</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_kanjis'] }}</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-4">
                <span class="text-3xl">🈶</span>
            </div>
        </div>
    </div>

    <!-- Total Minna Lessons -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Bài học Minna</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_minna_lessons'] }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-4">
                <span class="text-3xl">📚</span>
            </div>
        </div>
    </div>
</div>

<!-- Kanji by Level -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Kanji theo cấp độ</h2>
        <div class="space-y-3">
            @foreach(['N5', 'N4', 'N3', 'N2', 'N1'] as $level)
            <div class="flex items-center justify-between">
                <span class="text-gray-700 font-medium">{{ $level }}</span>
                <div class="flex items-center">
                    <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                        <div class="bg-blue-600 h-2 rounded-full"
                             style="width: {{ isset($stats['kanjis_by_level'][$level]) ? ($stats['kanjis_by_level'][$level] / max($stats['total_kanjis'], 1) * 100) : 0 }}%"></div>
                    </div>
                    <span class="text-gray-900 font-bold">{{ $stats['kanjis_by_level'][$level] ?? 0 }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Users -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Users mới nhất</h2>
        <div class="space-y-3">
            @forelse($stats['recent_users'] as $user)
            <div class="flex items-center justify-between py-2 border-b border-gray-200 last:border-0">
                <div>
                    <p class="font-medium text-gray-900">{{ $user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                </div>
                <span class="px-2 py-1 text-xs font-semibold rounded-full
                    {{ $user->role == 'admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ $user->role }}
                </span>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">Chưa có user nào</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-xl font-bold text-gray-900 mb-4">Thao tác nhanh</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @adminCan('alphabets.edit')
        <a href="{{ route('admin.alphabets.create') }}"
           class="bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 text-center">
            + Thêm Ký tự
        </a>
        @endadminCan
        @adminCan('kanjis.edit')
        <a href="{{ route('admin.kanjis.create') }}"
           class="bg-yellow-600 text-white px-4 py-3 rounded-lg hover:bg-yellow-700 text-center">
            + Thêm Kanji
        </a>
        @endadminCan
        @adminCan('minna.edit')
        <a href="{{ route('admin.minna.create') }}"
           class="bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 text-center">
            + Thêm Bài Minna
        </a>
        @endadminCan
        @adminCan('course_data.edit')
        <a href="{{ route('admin.course-data.create') }}"
           class="bg-purple-600 text-white px-4 py-3 rounded-lg hover:bg-purple-700 text-center">
            + Thêm Course Data (JLPT)
        </a>
        @endadminCan
    </div>
</div>
@endsection
