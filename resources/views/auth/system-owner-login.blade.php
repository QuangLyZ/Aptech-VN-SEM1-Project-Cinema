@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-black flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Hiệu ứng lưới laser nền (Cyberpunk vibe) -->
    <div class="absolute inset-0 z-0 opacity-20" style="background-image: linear-gradient(#f00 1px, transparent 1px), linear-gradient(90deg, #f00 1px, transparent 1px); background-size: 50px 50px;"></div>
    
    <!-- Các đốm sáng đỏ mờ ảo -->
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-red-600/20 rounded-full blur-[128px] animate-pulse"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-red-900/20 rounded-full blur-[128px] animate-pulse" style="animation-delay: 2s;"></div>

    <div class="max-w-md w-full relative z-10">
        <!-- Logo & Title -->
        <div class="text-center mb-12">
            <div class="inline-flex h-24 w-24 items-center justify-center rounded-3xl bg-gradient-to-br from-red-500 to-red-800 shadow-[0_0_50px_rgba(220,38,38,0.5)] mb-6 transform hover:rotate-12 transition-transform duration-500">
                <i class="fa-solid fa-crown text-4xl text-white"></i>
            </div>
            <h1 class="text-4xl font-black text-white tracking-tighter uppercase mb-2">
                QUẢN TRỊ <span class="text-red-500 italic">TỐI CAO</span>
            </h1>
            <div class="h-1 w-24 bg-red-600 mx-auto rounded-full mb-4"></div>
            <p class="text-gray-400 text-sm font-medium tracking-widest uppercase">Khu Vực Truy Cập Hạn Chế</p>
        </div>

        <!-- Form Glassmorphism -->
        <div class="bg-white/5 backdrop-blur-2xl rounded-[2.5rem] border border-red-500/30 p-10 shadow-2xl">
            <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
                @csrf
                
                @if($errors->any())
                    <div class="bg-red-500/10 border border-red-500/50 text-red-400 text-xs p-4 rounded-2xl mb-6">
                        @foreach ($errors->all() as $error)
                            <p>• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="space-y-4">
                    <div class="relative group">
                        <label class="text-[10px] uppercase font-bold text-red-500 ml-4 mb-1 block tracking-widest">Chìa Khóa Vạn Năng (Email)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-red-500/50">
                                <i class="fa-solid fa-id-badge"></i>
                            </span>
                            <input id="email" name="email" type="text" required 
                                class="w-full pl-10 pr-4 py-4 bg-black/40 border border-white/10 rounded-2xl text-white focus:outline-none focus:border-red-500/50 focus:ring-1 focus:ring-red-500/20 transition-all placeholder-gray-600"
                                placeholder="Yêu cầu định danh">
                        </div>
                    </div>

                    <div class="relative group">
                        <label class="text-[10px] uppercase font-bold text-red-500 ml-4 mb-1 block tracking-widest">Giao Thức Bí Mật (Mật Khẩu)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-red-500/50">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input id="password" name="password" type="password" required 
                                class="w-full pl-10 pr-12 py-4 bg-black/40 border border-white/10 rounded-2xl text-white focus:outline-none focus:border-red-500/50 focus:ring-1 focus:ring-red-500/20 transition-all placeholder-gray-600"
                                placeholder="Mã xác thực bí mật">
                            <button type="button" onclick="togglePassword('password', 'eye-icon')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-red-500/50 hover:text-red-500 transition-colors">
                                <i id="eye-icon" class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-4 bg-red-600 hover:bg-red-500 text-white font-black rounded-2xl shadow-[0_0_30px_rgba(220,38,38,0.3)] hover:shadow-[0_0_45px_rgba(220,38,38,0.5)] transition-all transform hover:-translate-y-1 active:scale-95 uppercase tracking-widest">
                        Khởi Tạo Phiên Truy Cập
                    </button>
                </div>

                <div class="mt-8 pt-8 border-t border-white/5 text-center">
                    <button type="button" onclick="autoFill()" class="text-[10px] font-bold text-gray-500 hover:text-red-400 transition-colors uppercase tracking-[0.2em]">
                        [ Kích Hoạt Lối Tắt Khẩn Cấp ]
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('login') }}" class="text-xs text-gray-500 hover:text-white transition-colors">
                <i class="fa-solid fa-arrow-left mr-2"></i> Quay lại Cổng Đăng Nhập Thường
            </a>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, iconId) {
    const passwordInput = document.getElementById(inputId);
    const eyeIcon = document.getElementById(iconId);
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}

    @php
        $firstOwner = array_map('trim', explode(',', config('app.system_owner_email', '')))[0] ?? '';
    @endphp
    
    function autoFill() {
        // Grace: Đây là phím tắt của System Owner. 
        // Em chỉ để Email thôi để người dùng tự nhập Pass cho an toàn nhé!
        const email = '{{ $firstOwner }}';
    const emailInput = document.getElementById('email');
    const passInput = document.getElementById('password');
    
    let i = 0;
    emailInput.value = '';
    
    function typeEffect() {
        if (i < email.length) {
            emailInput.value += email.charAt(i);
            i++;
            setTimeout(typeEffect, 50);
        } else {
            passInput.focus();
        }
    }
    
    typeEffect();
}
</script>
@endsection
