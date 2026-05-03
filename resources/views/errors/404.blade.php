@extends('layouts.app')

@section('title', 'Không tìm thấy trang - CineBook')

@section('content')
<section class="min-h-[calc(100vh-6rem)] bg-slate-950 px-4 py-10 text-white sm:px-6 lg:px-8">
    <div class="mx-auto flex min-h-[70vh] max-w-5xl flex-col items-center justify-center text-center">
        <div class="mb-6 inline-flex h-16 w-16 items-center justify-center rounded-2xl border border-red-500/30 bg-red-500/10 text-red-400 shadow-lg shadow-red-950/20 sm:h-20 sm:w-20">
            <i class="fa-solid fa-map-location-dot text-2xl sm:text-3xl"></i>
        </div>

        <div class="text-sm font-black uppercase tracking-[0.35em] text-red-400">404</div>
        <h1 class="mt-4 max-w-3xl text-[clamp(2rem,9vw,4.75rem)] font-black leading-none tracking-tight">
            Không tìm thấy trang
        </h1>
        <p class="mt-5 max-w-xl text-sm leading-6 text-gray-400 sm:text-base">
            Đường dẫn có thể đã thay đổi hoặc nội dung không còn tồn tại. Bạn có thể quay lại trang chủ hoặc xem lịch chiếu đang có.
        </p>

        <div class="mt-8 flex w-full max-w-md flex-col gap-3 sm:flex-row sm:justify-center">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-red-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-red-700">
                <i class="fa-solid fa-house"></i>
                <span>Trang chủ</span>
            </a>
            <a href="{{ route('movies.index') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-gray-700 bg-gray-900 px-5 py-3 text-sm font-bold text-gray-200 transition hover:border-red-500 hover:text-white">
                <i class="fa-solid fa-film"></i>
                <span>Lịch chiếu</span>
            </a>
        </div>
    </div>
</section>
@endsection
