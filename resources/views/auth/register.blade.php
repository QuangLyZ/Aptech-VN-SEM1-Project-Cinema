@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-900 py-12 px-4 sm:px-6 lg:px-8 relative">
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-red-600/10 blur-[100px]"></div>
    </div>

    <div class="max-w-md w-full space-y-8 bg-gray-800 p-10 rounded-2xl shadow-2xl border border-gray-700 relative z-10 text-white">
        <div>
            <h2 class="text-center text-3xl font-extrabold text-white">
                Đăng Ký Tài Khoản
            </h2>
            <p class="mt-2 text-center text-sm text-gray-400">
                Đã có tài khoản?
                <a href="/login" class="font-medium text-red-500 hover:text-red-400 transition-colors">
                    Đăng nhập ngay
                </a>
            </p>
        </div>
        <form class="mt-8 space-y-6" action="#" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300">Họ và Tên</label>
                    <input id="name" name="name" type="text" required class="mt-1 block w-full px-3 py-3 border border-gray-600 bg-gray-700 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 sm:text-sm flex items-center" placeholder="Nguyễn Văn A">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300">Email</label>
                    <input id="email" name="email" type="email" autocomplete="email" required class="mt-1 block w-full px-3 py-3 border border-gray-600 bg-gray-700 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 sm:text-sm" placeholder="example@email.com">
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-300">Số Điện Thoại</label>
                    <input id="phone" name="phone" type="tel" required class="mt-1 block w-full px-3 py-3 border border-gray-600 bg-gray-700 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 sm:text-sm" placeholder="090 123 4567">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300">Mật khẩu</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required class="mt-1 block w-full px-3 py-3 border border-gray-600 bg-gray-700 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 sm:text-sm" placeholder="••••••••">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-300">Xác nhận mật khẩu</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required class="mt-1 block w-full px-3 py-3 border border-gray-600 bg-gray-700 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 sm:text-sm" placeholder="••••••••">
                </div>
            </div>

            <div>
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 focus:ring-offset-gray-900 transition-colors">
                    ĐĂNG KÝ
                </button>
            </div>
            
            <p class="text-xs text-center text-gray-400 mt-4">
                Bằng việc đăng ký, bạn đã đồng ý với <a href="#" class="text-red-500 hover:underline">Điều khoản</a> và <a href="#" class="text-red-500 hover:underline">Chính sách bảo mật</a> của chúng tôi.
            </p>
        </form>
    </div>
</div>
@endsection
