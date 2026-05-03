@extends('layouts.admin')

@section('title', $pageTitle)
@section('page-title', $pageTitle)

@section('content')
<div class="space-y-6 animate-[fadeIn_0.5s_ease-in-out]">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.management') }}" class="admin-back-link">
                <i class="fa-solid fa-chevron-left text-2xl"></i>
            </a>
            <div>
                <h2 class="admin-page-title font-extrabold tracking-tight text-white">Danh sách người dùng</h2>
                <p class="mt-1 text-sm text-gray-400">Quản lý và phân tích hành vi của người dùng hệ thống.</p>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-3xl border border-gray-800 bg-gray-900/70 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="border-b border-gray-800 bg-gray-950/50 uppercase text-gray-400">
                    <tr>
                        <th class="px-6 py-4 font-semibold tracking-wider">Khách hàng</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-center">Liên hệ</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-center">Phân quyền</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-center">Sở thích Thể loại</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-center">Khung giờ hay xem</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-right">Tổng chi tiêu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse ($users as $user)
                        <tr class="transition-colors hover:bg-gray-800/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-purple-500/20 text-lg font-bold text-purple-400">
                                        {{ strtoupper(substr($user->name ?? $user->username ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                <div class="font-bold text-white">{{ $user->name ?? 'Người dùng Thường' }}</div>
                                <div class="mt-1 text-xs text-gray-500">{{ '@' . $user->username }}</div>
                                @if(auth()->user()->isSystemOwner() && $user->isSystemOwner())
                                    <div class="mt-2 inline-flex items-center gap-1 text-[9px] font-black text-red-500 uppercase tracking-tighter bg-red-500/5 px-2 py-0.5 rounded border border-red-500/20">
                                        <i class="fa-solid fa-crown"></i>
                                        System Owner
                                    </div>
                                @endif
</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-gray-300">{{ $user->email ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $user->phone ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $displayRole = (int) $user->admin_role;
                                    $isSystemOwner = $user->isSystemOwner();
                                    
                                    $roleClass = $displayRole == 3 || $isSystemOwner ? 'bg-red-500/10 text-red-400 border-red-500/20' :
                                                ($displayRole == 2 ? 'bg-purple-500/10 text-purple-400 border-purple-500/20' :
                                                ($displayRole == 1 ? 'bg-blue-500/10 text-blue-400 border-blue-500/20' :
                                                'bg-gray-500/10 text-gray-400 border-gray-500/20');
                                    $roleIcon = $displayRole == 3 || $isSystemOwner ? 'fa-crown' :
                                               ($displayRole == 2 ? 'fa-user-shield' :
                                               ($displayRole == 1 ? 'fa-user' : 'fa-user-clock'));
                                    $roleText = $displayRole == 3 || $isSystemOwner ? 'Super Admin' :
                                               ($displayRole == 2 ? 'Admin' :
                                               ($displayRole == 1 ? 'Client' : 'Guest'));
                                @endphp
                                @if(auth()->user()->isAdmin() && (auth()->user()->isSystemOwner() || $displayRole <= 1))
                                    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PUT')
                                        <select name="admin_role" onchange="this.form.submit()" class="rounded-full border {{ $roleClass }} bg-gray-950 px-3 py-1 text-xs font-semibold focus:outline-none">
                                            <option value="0" {{ $displayRole === 0 ? 'selected' : '' }}>0 - Guest</option>
                                            <option value="1" {{ $displayRole === 1 ? 'selected' : '' }}>1 - Client</option>
                                            @if(auth()->user()->isSystemOwner())
                                                <option value="2" {{ $displayRole === 2 ? 'selected' : '' }}>2 - Admin</option>
                                                <option value="3" {{ $displayRole === 3 ? 'selected' : '' }}>3 - Super Admin</option>
                                            @endif
                                        </select>
                                    </form>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full {{ $roleClass }} px-3 py-1 text-xs font-semibold border">
                                        <i class="fa-solid {{ $roleIcon }} text-[10px]"></i>
                                        {{ $roleText }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($user->favorite_genre !== 'Chưa có dữ liệu')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-500 border border-amber-500/20">
                                        <i class="fa-solid fa-star text-[10px]"></i>
                                        {{ $user->favorite_genre }}
                                    </span>
                                @else
                                    <span class="text-gray-600 text-xs italic">{{ $user->favorite_genre }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($user->favorite_time !== 'Chưa có dữ liệu')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-500/10 px-3 py-1 text-xs font-semibold text-sky-400 border border-sky-500/20">
                                        <i class="fa-regular fa-clock text-[10px]"></i>
                                        {{ $user->favorite_time }}
                                    </span>
                                @else
                                    <span class="text-gray-600 text-xs italic">{{ $user->favorite_time }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-bold text-emerald-400">
                                    {{ number_format($user->total_spent, 0, ',', '.') }} ₫
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fa-solid fa-users text-4xl mb-3 text-gray-700"></i>
                                    <p>Chưa có dữ liệu người dùng.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if ($users->hasPages())
            <div class="border-t border-gray-800 px-6 py-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
