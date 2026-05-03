@extends('layouts.app')

@section('title', 'Không có quyền truy cập - CineBook')

@section('content')
<section class="min-h-[calc(100vh-6rem)] bg-slate-950 px-4 py-10 text-white sm:px-6 lg:px-8">
    <div class="mx-auto flex min-h-[70vh] max-w-5xl flex-col items-center justify-center text-center">
        <div class="mb-6 inline-flex h-16 w-16 items-center justify-center rounded-2xl border border-amber-500/30 bg-amber-500/10 text-amber-300 shadow-lg shadow-amber-950/20 sm:h-20 sm:w-20">
            <i class="fa-solid fa-shield-halved text-2xl sm:text-3xl"></i>
        </div>

        <div class="text-sm font-black uppercase tracking-[0.35em] text-amber-300">403</div>
        <h1 class="mt-4 max-w-3xl text-[clamp(2rem,9vw,4.75rem)] font-black leading-none tracking-tight">
            Bạn chưa có quyền vào trang này
        </h1>
        <p class="mt-5 max-w-xl text-sm leading-6 text-gray-400 sm:text-base">
            Khu vực này cần tài khoản có quyền phù hợp. Nếu bạn là quản trị viên, hãy đăng nhập đúng tài khoản được cấp quyền.
        </p>

        <div class="mt-8 flex w-full max-w-md flex-col gap-3 sm:flex-row sm:justify-center">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-red-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-red-700">
                <i class="fa-solid fa-house"></i>
                <span>Trang chủ</span>
            </a>
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-gray-700 bg-gray-900 px-5 py-3 text-sm font-bold text-gray-200 transition hover:border-amber-500 hover:text-white">
                <i class="fa-solid fa-right-to-bracket"></i>
                <span>Đăng nhập</span>
            </a>
        </div>
    </div>
</section>
@endsection
