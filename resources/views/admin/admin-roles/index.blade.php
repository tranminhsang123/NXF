@extends('adminlayout.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Vai trò và phân quyền admin</h1>
        <p class="text-gray-600 mt-2">Quản lý nhóm quyền dùng để cấp quyền truy cập từng khu vực admin.</p>
    </div>
    @adminCan('admin_roles.edit')
        <a href="{{ route('admin.admin-roles.create') }}" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Tạo vai trò</a>
    @endadminCan
</div>

@if(session('success'))
    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[760px]">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vai trò</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quyền</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Admin đang dùng</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($roles as $role)
                    <tr>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900">{{ $role->name }}</p>
                            @if($role->slug === 'super_admin')
                                <span class="mt-1 inline-flex rounded bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700">Bảo vệ hệ thống</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-mono text-sm text-gray-700">{{ $role->slug }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $role->permissions_count }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $role->users_count }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                @adminCan('admin_roles.edit')
                                    <a href="{{ route('admin.admin-roles.edit', $role) }}" class="rounded bg-indigo-600 px-3 py-1 text-xs font-semibold text-white hover:bg-indigo-700">Sửa</a>
                                @endadminCan
                                @adminCan('admin_roles.delete')
                                    @if($role->slug !== 'super_admin' && $role->users_count === 0)
                                        <form method="POST" action="{{ route('admin.admin-roles.destroy', $role) }}" onsubmit="return confirm('Xoá vai trò này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded bg-red-600 px-3 py-1 text-xs font-semibold text-white hover:bg-red-700">Xoá</button>
                                        </form>
                                    @endif
                                @endadminCan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">Chưa có vai trò admin.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($roles->hasPages())
        <div class="border-t border-gray-200 px-6 py-4">{{ $roles->links() }}</div>
    @endif
</div>
@endsection
