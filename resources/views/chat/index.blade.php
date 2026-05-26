<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat - Nhóm chat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    @include('layouts.header')

    <div class="container mx-auto px-4 max-w-4xl py-24">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Nhóm chat</h1>
            <p class="text-sm text-gray-500 mt-1">Chọn một nhóm hoặc xin tham gia để bắt đầu nhắn tin.</p>
        </div>

        @if(session('status'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if($groups->isEmpty())
            <div class="bg-white border border-gray-200 rounded-xl p-6 text-gray-600 shadow-sm">
                Hiện tại bạn chưa tham gia nhóm chat nào.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($groups as $group)
                    @php
                        $last = $group->messages->first();
                    @endphp
                    <a href="{{ route('chat.show', ['group' => $group->id]) }}"
                       class="bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition-shadow shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h2 class="font-semibold text-gray-900">{{ $group->name }}</h2>
                                <p class="text-sm text-gray-500 mt-1 break-words">
                                    {{ $last ? ($last->content ?: 'Chưa có tin nhắn.') : 'Chưa có tin nhắn.' }}
                                </p>
                            </div>
                            <div class="text-xs text-gray-400 whitespace-nowrap">
                                {{ $last?->created_at?->format('H:i') }}
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        <div class="mt-10 mb-3">
            <h2 class="text-xl font-bold text-gray-900">Nhóm bạn có thể xin tham gia</h2>
            <p class="text-sm text-gray-500 mt-1">Admin sẽ duyệt yêu cầu trước khi bạn được nhắn trong nhóm.</p>
        </div>

        @if($availableGroups->isEmpty())
            <div class="bg-white border border-gray-200 rounded-xl p-6 text-gray-600 shadow-sm">
                Không có nhóm nào để xin tham gia.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($availableGroups as $group)
                    @php $last = $group->messages->first(); @endphp
                    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $group->name }}</h3>
                                <p class="text-sm text-gray-500 mt-1 break-words">
                                    {{ $last ? ($last->content ?: 'Chưa có tin nhắn.') : 'Chưa có tin nhắn.' }}
                                </p>
                            </div>
                            <div class="text-xs text-gray-400 whitespace-nowrap">
                                {{ $last?->created_at?->format('H:i') }}
                            </div>
                        </div>

                        <form class="mt-4" method="POST" action="{{ route('chat.groups.request-join', ['group' => $group->id]) }}">
                            @csrf
                            <button type="submit"
                                    class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm">
                                Xin vào nhóm
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</body>
</html>

