@extends('adminlayout.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Tạo vai trò admin</h1>
    <p class="text-gray-600 mt-2">Chọn những quyền mà vai trò này được phép sử dụng.</p>
</div>

@include('admin.admin-roles._form')
@endsection
