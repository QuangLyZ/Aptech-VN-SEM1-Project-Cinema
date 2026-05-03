@extends('layouts.admin')

@section('title', 'System Owner Workspace')
@section('page-title', 'System Owner Workspace')

@section('content')
<div class="space-y-8 animate-[fadeIn_0.5s_ease-in-out]">
    <!-- Welcome Header -->
    <div class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-red-600/20 via-slate-900 to-slate-950 p-8 border border-red-500/20 shadow-2xl">
        <div class="relative z-10">
            <h2 class="text-3xl font-black text-white tracking-tighter">Chào mừng bạn quay trở lại!</h2>
            <p class="text-gray-400 mt-2 max-w-2xl">Đây là không gian dành riêng cho Quản trị viên cấp cao. Bạn có toàn quyền kiểm soát hệ thống, phân quyền nhân sự và theo dõi các chỉ số quan trọng nhất của CineBook.</p>
        </div>
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-red-600/10 blur-[100px]"></div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
        <div class="rounded-3xl border border-gray-800 bg-gray-900/50 p-5 backdrop-blur-xl hover:border-blue-500/30 transition-colors">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Tổng User</div>
            <div class="text-2xl font-black text-white">{{ number_format($stats['total_users']) }}</div>
            <div class="mt-2 text-[10px] text-blue-400"><i class="fa-solid fa-users mr-1"></i> Data chính</div>
        </div>
        <div class="rounded-3xl border border-gray-800 bg-gray-900/50 p-5 backdrop-blur-xl hover:border-purple-500/30 transition-colors">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Đội ngũ Admin</div>
            <div class="text-2xl font-black text-white">{{ number_format($stats['total_admins']) }}</div>
            <div class="mt-2 text-[10px] text-purple-400"><i class="fa-solid fa-user-shield mr-1"></i> Personnel</div>
        </div>
        <div class="rounded-3xl border border-red-500/20 bg-red-950/20 p-5 backdrop-blur-xl hover:border-red-500/50 transition-colors">
            <div class="text-xs font-bold text-red-500/60 uppercase tracking-widest mb-1">Doanh thu tổng</div>
            <div class="text-2xl font-black text-red-500">{{ number_format($stats['total_revenue'], 0, ',', '.') }} ₫</div>
            <div class="mt-2 text-[10px] text-red-400"><i class="fa-solid fa-coins mr-1"></i> Cashflow</div>
        </div>
        <div class="rounded-3xl border border-gray-800 bg-gray-900/50 p-5 backdrop-blur-xl hover:border-emerald-500/30 transition-colors">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Đăng ký mới</div>
            <div class="text-2xl font-black text-white">{{ number_format($stats['new_users_today']) }}</div>
            <div class="mt-2 text-[10px] text-emerald-400"><i class="fa-solid fa-user-plus mr-1"></i> Today</div>
        </div>
        <div class="rounded-3xl border border-gray-800 bg-gray-900/50 p-5 backdrop-blur-xl hover:border-orange-500/30 transition-colors">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Vé bán hôm nay</div>
            <div class="text-2xl font-black text-white">{{ number_format($stats['tickets_today']) }}</div>
            <div class="mt-2 text-[10px] text-orange-400"><i class="fa-solid fa-ticket mr-1"></i> Operations</div>
        </div>
        <div class="rounded-3xl border border-gray-800 bg-gray-900/50 p-5 backdrop-blur-xl hover:border-cyan-500/30 transition-colors">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Feedback</div>
            <div class="text-2xl font-black text-white">{{ number_format($stats['pending_feedback']) }}</div>
            <div class="mt-2 text-[10px] text-cyan-400"><i class="fa-solid fa-comment-dots mr-1"></i> Support</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @if(auth()->user()->isRootOwner())
        <!-- Sub-Owner Management (Only for Root Owner) -->
        <div class="lg:col-span-3 rounded-[2.5rem] border border-amber-500/20 bg-amber-950/5 p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute -right-20 -bottom-20 h-64 w-64 rounded-full bg-amber-500/5 blur-[100px]"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-4 mb-8">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-500 text-white shadow-lg shadow-amber-900/20 text-2xl">
                        <i class="fa-solid fa-shuttle-space"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-white">Hội Đồng Cố Vấn Tối Cao</h3>
                        <p class="text-sm text-amber-500/60 font-bold uppercase tracking-widest">Đặc quyền Root Owner</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                    <!-- Add Sub-Owner Form -->
                    <div class="bg-black/40 rounded-3xl p-6 border border-amber-500/10">
                        <h4 class="text-white font-bold mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-user-plus text-amber-500 text-xs"></i>
                            Thăng chức Cố vấn
                        </h4>
                        <form action="{{ route('admin.system-owner.sub-owners.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="email" name="email" required placeholder="Nhập email nhân sự..." class="w-full rounded-xl border border-gray-800 bg-gray-900/50 px-4 py-3 text-sm text-white focus:border-amber-500 focus:outline-none transition">
                                <input type="text" name="note" placeholder="Vai trò/Ghi chú (Ví dụ: Tech Lead)" class="w-full rounded-xl border border-gray-800 bg-gray-900/50 px-4 py-3 text-sm text-white focus:border-amber-500 focus:outline-none transition">
                            </div>
                            <button type="submit" class="w-full md:w-auto px-8 py-3 bg-amber-600 hover:bg-amber-500 text-white font-black rounded-xl shadow-lg shadow-amber-900/20 transition-all active:scale-95 text-xs uppercase tracking-widest">
                                Khởi tạo đặc quyền
                            </button>
                        </form>
                    </div>

                    <!-- Sub-Owner List -->
                    <div class="space-y-4">
                        <h4 class="text-white font-bold mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-id-card-clip text-amber-500 text-xs"></i>
                            Danh sách Cố vấn hiện tại
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @forelse($subOwners as $sub)
                                <div class="flex items-center justify-between gap-4 p-4 rounded-2xl bg-amber-500/5 border border-amber-500/10 group hover:bg-amber-500/10 transition-colors">
                                    <div class="truncate">
                                        <div class="text-sm font-bold text-white truncate">{{ $sub->email }}</div>
                                        <div class="text-[10px] text-amber-500/60 font-black uppercase tracking-tighter">{{ $sub->note ?: 'Cố vấn tối cao' }}</div>
                                    </div>
                                    <form action="{{ route('admin.system-owner.sub-owners.destroy', $sub->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="h-8 w-8 flex items-center justify-center rounded-lg bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition-all">
                                            <i class="fa-solid fa-xmark text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <div class="col-span-2 py-4 text-center text-xs text-gray-600 italic">Chưa có cố vấn nào được thăng chức.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- User Role Management Table -->
        <div class="lg:col-span-2 rounded-[2.5rem] border border-gray-800 bg-gray-950/50 overflow-hidden shadow-2xl">
        <div class="px-8 py-6 border-b border-gray-800 flex items-center justify-between bg-gray-900/30">
            <div>
                <h3 class="text-xl font-black text-white">Quản lý Phân Quyền Nhân Sự</h3>
                <p class="text-sm text-gray-500">Chỉ System Owner mới có quyền thay đổi vai trò của nhân viên tại đây.</p>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-600 text-white shadow-lg shadow-red-900/20">
                <i class="fa-solid fa-key"></i>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-950/50 uppercase text-[10px] font-black tracking-[0.2em] text-gray-500">
                    <tr>
                        <th class="px-8 py-5">Thành viên</th>
                        <th class="px-8 py-5">Email & Liên hệ</th>
                        <th class="px-8 py-5 text-center">Vai trò hiện tại</th>
                        <th class="px-8 py-5 text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/50">
                    @foreach ($users as $user)
                        <tr class="transition-colors hover:bg-white/[0.02]">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-slate-800 to-slate-900 border border-white/5 text-lg font-black text-white shadow-xl">
                                        {{ strtoupper(substr($user->name ?? $user->username ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-white text-base">{{ $user->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500 mt-1">@ {{ $user->username }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-gray-300 font-medium">{{ $user->email }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $user->phone ?? 'Không có SĐT' }}</div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                @if($user->isSystemOwner())
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-600/10 px-4 py-1.5 text-xs font-black text-red-500 border border-red-500/20 shadow-[0_0_15px_rgba(220,38,38,0.1)] uppercase">
                                        <i class="fa-solid fa-crown text-[10px]"></i>
                                        System Owner
                                    </span>
                                @elseif($user->admin_role)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-purple-600/10 px-4 py-1.5 text-xs font-black text-purple-500 border border-purple-500/20 uppercase">
                                        <i class="fa-solid fa-user-shield text-[10px]"></i>
                                        Quản trị viên
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-500/10 px-4 py-1.5 text-xs font-black text-gray-500 border border-gray-500/20 uppercase">
                                        <i class="fa-solid fa-user text-[10px]"></i>
                                        Khách hàng
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-right">
                                @if(!$user->isSystemOwner())
                                    <form action="{{ route('admin.system-owner.update-role', $user->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="admin_role" value="{{ $user->admin_role ? '0' : '1' }}">
                                        <button type="submit" class="group flex items-center gap-2 rounded-xl border border-white/5 bg-white/5 px-4 py-2 text-xs font-bold text-white transition-all hover:bg-white/10 active:scale-95">
                                            @if($user->admin_role)
                                                <i class="fa-solid fa-user-minus text-red-500"></i> Hạ quyền khách
                                            @else
                                                <i class="fa-solid fa-user-plus text-emerald-500"></i> Lên Quản trị
                                            @endif
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs font-bold text-gray-700 italic">Cấp độ tối cao</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-8 py-5 bg-gray-900/20 border-t border-gray-800">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Right Sidebar Widgets -->
    <div class="space-y-8">
        <!-- Top Movies -->
        <div class="rounded-[2.5rem] border border-gray-800 bg-gray-950/50 p-8 shadow-2xl">
            <h3 class="text-lg font-black text-white mb-6 uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-fire text-red-500"></i> Siêu phẩm hái ra tiền
            </h3>
            <div class="space-y-6">
                @foreach($topMovies as $movie)
                <div class="relative group">
                    <div class="flex justify-between items-end mb-2">
                        <div class="text-sm font-bold text-gray-300 group-hover:text-white transition-colors truncate max-w-[150px]">{{ $movie->name }}</div>
                        <div class="text-xs font-black text-red-500">{{ number_format($movie->revenue, 0, ',', '.') }} ₫</div>
                    </div>
                    <div class="h-1.5 w-full bg-gray-900 rounded-full overflow-hidden">
                        @php 
                            $maxRevenue = $topMovies->first()->revenue ?? 1;
                            $width = ($movie->revenue / $maxRevenue) * 100;
                        @endphp
                        <div class="h-full bg-gradient-to-r from-red-600 to-red-400 rounded-full shadow-[0_0_10px_rgba(220,38,38,0.5)] transition-all duration-1000" style="width: {{ $width }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- System Status -->
        <div class="rounded-[2.5rem] border border-red-500/10 bg-gradient-to-br from-red-600/5 to-transparent p-8 shadow-2xl">
            <h3 class="text-lg font-black text-white mb-4 uppercase tracking-wider">Trạng thái hạt nhân</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-500">Database Engine</span>
                    <span class="text-emerald-500 font-bold uppercase tracking-tighter">Connected</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-500">Security Layer</span>
                    <span class="text-emerald-500 font-bold uppercase tracking-tighter">Active</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-500">Master Key Access</span>
                    <span class="text-red-500 font-bold uppercase tracking-tighter animate-pulse">Enabled</span>
                </div>
                <div class="pt-4 mt-4 border-t border-gray-800">
                    <div class="flex items-center gap-3">
                        <div class="h-2 w-2 rounded-full bg-red-600 animate-ping"></div>
                        <span class="text-[10px] text-gray-400 italic">V2.6.0 - Jarvis System Online</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
