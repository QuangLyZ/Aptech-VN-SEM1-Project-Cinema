@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-900 py-12 px-4 sm:px-6 lg:px-8 relative">
    <!-- Background Elements -->
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-red-600/10 blur-[100px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-blue-600/10 blur-[100px]"></div>
    </div>

    <div class="max-w-md w-full space-y-8 bg-gray-800 p-10 rounded-2xl shadow-2xl border border-gray-700 relative z-10 block">
        <div>
            <div class="flex justify-center mb-4">
                <i class="fa-solid fa-user-circle text-5xl text-red-500"></i>
            </div>
            <h2 class="text-center text-3xl font-extrabold text-white">
                Đăng Nhập
            </h2>
            <p class="mt-2 text-center text-sm text-gray-400">
                Hoặc
                <a href="/register" class="font-medium text-red-500 hover:text-red-400 transition-colors">
                    tạo tài khoản mới
                </a>
            </p>
        </div>
        <form class="mt-8 space-y-6" action="#" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300">Email hoặc Số điện thoại</label>
                    <input id="email" name="email" type="text" autocomplete="email" required class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-600 bg-gray-700 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 sm:text-sm transition-colors" placeholder="Nhập email hoặc SĐT đăng nhập">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300">Mật khẩu</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-600 bg-gray-700 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 sm:text-sm transition-colors" placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-600 rounded bg-gray-700 outline-none">
                    <label for="remember-me" class="ml-2 block text-sm text-gray-300">
                        Ghi nhớ đăng nhập
                    </label>
                </div>

                <div class="text-sm">
                    <a href="/forgot-password" class="font-medium text-red-500 hover:text-red-400 transition-colors">
                        Quên mật khẩu?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 focus:ring-offset-gray-900 transition-all font-bold">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fa-solid fa-lock text-red-500 group-hover:text-red-400 transition-colors"></i>
                    </span>
                    ĐĂNG NHẬP
                </button>
            </div>
            
            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-600"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-gray-800 text-gray-400">Hoặc tiếp tục với</span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <a href="#" class="w-full inline-flex justify-center py-2 px-4 border border-gray-600 rounded-md shadow-sm bg-gray-700 text-sm font-medium text-white hover:bg-gray-600 transition-colors">
                        <i class="fa-brands fa-google text-red-400 text-lg"></i>
                    </a>
                    <a href="#" class="w-full inline-flex justify-center py-2 px-4 border border-gray-600 rounded-md shadow-sm bg-gray-700 text-sm font-medium text-white hover:bg-gray-600 transition-colors">
                        <i class="fa-brands fa-facebook text-blue-500 text-lg"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
