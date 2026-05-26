@extends('adminlayout.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Sửa vai trò admin</h1>
        <p class="text-gray-600 mt-2">{{ $role->name }} - {{ $role->slug }}</p>
    </div>
    <a href="{{ route('admin.admin-roles.index') }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300">Quay lại</a>
</div>

@if(session('success'))
    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
@endif

@include('admin.admin-roles._form')
@endsection
