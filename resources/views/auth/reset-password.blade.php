@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-900 py-12 px-4 sm:px-6 lg:px-8 relative">
    <div class="max-w-md w-full space-y-8 bg-gray-800 p-10 rounded-2xl shadow-2xl border border-gray-700 relative z-10">
        <div>
            <div class="flex justify-center mb-4">
                <i class="fa-solid fa-lock-open text-5xl text-red-500"></i>
            </div>
            <h2 class="text-center text-3xl font-extrabold text-white">
                Đặt Lại Mật Khẩu
            </h2>
            <p class="mt-2 text-center text-sm text-gray-400">
                Vui lòng nhập mật khẩu mới cho tài khoản của bạn.
            </p>
        </div>

        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500 text-red-500 px-4 py-3 rounded-lg text-sm mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="mt-8 space-y-6" action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="block text-sm font-medium text-gray-300">Email</label>
                <input id="email" name="email" type="email" value="{{ $email ?? old('email') }}" required readonly class="mt-1 block w-full px-3 py-3 border border-gray-600 bg-gray-700 text-gray-400 rounded-lg focus:outline-none sm:text-sm">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-300">Mật khẩu mới</label>
                <input id="password" name="password" type="password" required class="mt-1 block w-full px-3 py-3 border border-gray-600 bg-gray-700 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 sm:text-sm">
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-300">Xác nhận mật khẩu mới</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required class="mt-1 block w-full px-3 py-3 border border-gray-600 bg-gray-700 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 sm:text-sm">
            </div>

            <div>
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 focus:ring-offset-gray-900 transition-colors">
                    CẬP NHẬT MẬT KHẨU
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
