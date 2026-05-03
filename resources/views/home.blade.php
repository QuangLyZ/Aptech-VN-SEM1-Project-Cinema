@extends('layouts.app')
@section('content')
@php
    $heroMovie = $featuredMovie ?? null;
    $heroPoster = filled($heroMovie?->poster)
        ? $heroMovie->poster
        : 'https://images.unsplash.com/photo-1626814026160-2237a95fc5a0?q=100&w=3840&auto=format&fit=crop';
    $heroBookingUrl = $heroMovie
        ? route('movies.show', $heroMovie->id)
        : route('movies.index');
@endphp
<!-- Hero Section -->
<div class="relative w-full group/hero overflow-hidden bg-[#0f172a]">
    <!-- Background Layer (Full Width) -->
    <div class="absolute inset-0 h-[600px] md:h-[750px] lg:h-[850px] xl:h-[950px]">
        <!-- Ultra High Quality Background -->
        <img class="w-full h-full object-cover object-right transform transition-transform duration-[10000ms] group-hover/hero:scale-110" 
             src="{{ asset('images/backgroundironman.jpg') }}" 
             alt="{{ $heroMovie?->name ?? 'Hero Movie' }}">
        
        <!-- Layered Overlays for Depth -->
        <div class="absolute inset-0 bg-black/30"></div>
        <!-- Vertical Gradient -->
        <div class="absolute inset-0 bg-gradient-to-t from-[#0f172a] via-[#0f172a]/40 to-transparent"></div>
        <!-- Horizontal Gradient (Left to Right) for text readability -->
        <div class="absolute inset-0 bg-gradient-to-r from-[#0f172a] via-[#0f172a]/60 to-transparent"></div>
    </div>
    
    <!-- Content Layer (Stay in Container) -->
    <div class="relative max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 h-[600px] md:h-[750px] lg:h-[850px] xl:h-[950px] flex items-center">
        <div class="w-full md:w-[80%] lg:w-[60%] xl:w-[50%] space-y-6 md:space-y-8 z-10">
            <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-red-600/20 border border-red-600/30 text-red-500 text-[11px] font-black uppercase tracking-[0.3em] backdrop-blur-md">
                <span class="flex h-2.5 w-2.5 rounded-full bg-red-500 shadow-[0_0_15px_rgba(239,68,68,1)] animate-pulse"></span>
                {{ __('ui.featured') }}
            </div>
            
            <div class="space-y-6">
                <h1 class="text-[clamp(2.5rem,8vw,5.5rem)] font-black tracking-tighter text-white leading-[0.85] drop-shadow-[0_10px_10px_rgba(0,0,0,0.5)]">
                    {{ $heroMovie?->name ?? 'Avengers: Secret Wars' }}
                </h1>
                <p class="text-[clamp(1rem,1.8vw,1.25rem)] text-white/90 leading-relaxed font-medium max-w-xl drop-shadow-md">
                    {{ $heroMovie?->description ? \Illuminate\Support\Str::limit($heroMovie->description, 200) : 'Cuộc chiến cuối cùng định đoạt số phận của đa vũ trụ. Các siêu anh hùng vĩ đại nhất phải hợp lực để ngăn chặn sự sụp đổ của mọi thực tại.' }}
                </p>
            </div>
            
            <div class="flex flex-wrap gap-5 pt-4">
                <a href="{{ $heroBookingUrl }}" class="group bg-red-600 hover:bg-red-500 text-white font-black py-4 px-10 md:px-12 rounded-[20px] transition-all active:scale-95 shadow-[0_25px_50px_-12px_rgba(225,29,72,0.5)] flex items-center gap-4">
                    <i class="fa-solid fa-ticket text-xl group-hover:rotate-12 transition-transform"></i>
                    <span class="text-lg uppercase">{{ __('ui.book_now') }}</span>
                </a>
                <a href="{{ $heroMovie?->trailer_link ?? 'https://www.youtube.com/watch?v=kH1XlwHQv9o' }}" 
                   target="_blank" 
                   class="bg-white/10 hover:bg-white/20 backdrop-blur-xl border border-white/20 text-white font-black py-4 px-10 md:px-12 rounded-[20px] transition-all active:scale-95 flex items-center gap-4 group">
                    <i class="fa-solid fa-circle-play text-xl group-hover:scale-110 transition-transform text-white/80"></i>
                    <span class="text-lg uppercase">{{ __('ui.watch_trailer') }}</span>
                </a>
            </div>
        </div>
    </div>
</div>

@if (!empty($dbWarning))
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
    <div class="rounded-xl border border-yellow-500/40 bg-yellow-500/10 px-4 py-3 text-sm text-yellow-200">
        <i class="fa-solid fa-triangle-exclamation mr-2"></i>{{ $dbWarning }}
    </div>
</div>
@endif

<!-- Phim Đang Chiếu -->
<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="flex justify-between items-end mb-10">
        <div>
            <h2 class="text-3xl font-extrabold tracking-tight text-white mb-2 uppercase">{{ __('ui.now_showing') }}</h2>
        </div>
        <a href="/movies" class="text-red-500 hover:text-red-400 font-bold flex items-center gap-1 transition-colors group">
            {{ __('ui.view_all') }}
            <i class="fa-solid fa-chevron-right text-xs transform group-hover:translate-x-1 transition-transform"></i>
        </a>
    </div>

    <!-- Movie Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 sm:gap-8">
        @foreach ($nowShowing as $movie)
        <div class="group relative bg-slate-900 rounded-[2rem] overflow-hidden shadow-2xl transition-all duration-500 hover:shadow-red-500/10 border border-white/5">
            <div class="relative aspect-[2/3] overflow-hidden">
                <img src="{{ $movie->poster ?? 'https://images.unsplash.com/photo-1440404653325-ab127d49abc1?q=80&w=400&h=600&auto=format&fit=crop' }}" 
                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                     alt="{{ $movie->name }}">
                
                <!-- Gradient Overlay for Contrast -->
                <div class="absolute inset-0 bg-gradient-to-t from-[#0f172a] via-[#0f172a]/20 to-transparent opacity-90"></div>
                
                <!-- Top Badges -->
                <div class="absolute top-4 left-4 flex gap-2">
                    <span class="bg-red-600 text-white text-xs sm:text-sm font-black px-3 py-1.5 rounded-xl shadow-[0_0_20px_rgba(220,38,38,0.5)] border border-red-500/50">
                        {{ $movie->age_limit ? 'T' . $movie->age_limit : 'P' }}
                    </span>
                    <div class="bg-black/60 backdrop-blur-xl border border-white/20 px-3 py-1.5 rounded-xl flex items-center gap-1.5 shadow-2xl">
                        <i class="fa-solid fa-star text-yellow-400 text-xs sm:text-sm"></i>
                        <span class="text-white text-xs sm:text-sm font-black">{{ number_format($movie->average_rating, 1) }}</span>
                    </div>
                </div>

                <!-- Floating Feedback Button -->
                <button onclick="openReviewModal({{ $movie->id }}, '{{ addslashes($movie->name) }}')" 
                        class="absolute top-4 right-4 bg-black/60 backdrop-blur-xl border border-white/20 w-10 h-10 rounded-full flex items-center justify-center text-white/90 hover:text-yellow-400 hover:bg-red-600/40 transition-all shadow-2xl">
                    <i class="fa-regular fa-comment-dots text-lg"></i>
                </button>
            </div>

            <!-- Info Section (Mica/Glassmorphism Box) -->
            <div class="absolute bottom-3 left-3 right-3 sm:bottom-4 sm:left-4 sm:right-4 p-4 sm:p-5 rounded-[1.5rem] bg-black/40 backdrop-blur-xl border border-white/10 shadow-2xl space-y-3 sm:space-y-4 transform transition-transform duration-500 group-hover:-translate-y-1">
                <div>
                    <h3 class="text-base sm:text-lg font-extrabold text-white leading-tight group-hover:text-red-500 transition-colors line-clamp-1">{{ $movie->name }}</h3>
                    <p class="text-slate-300/80 text-[9px] sm:text-[10px] mt-1 font-bold uppercase tracking-widest">{{ $movie->genre }}</p>
                </div>
                
                <div class="flex gap-2 pt-0.5">
                    <a href="{{ route('movies.show', $movie->id) }}" 
                       class="flex-1 bg-red-600 hover:bg-red-700 text-white text-[9px] sm:text-[10px] font-black py-2.5 sm:py-3 rounded-xl transition-all active:scale-95 shadow-lg shadow-red-900/40 text-center">
                        CHI TIẾT
                    </a>
                    <button onclick="openReviewModal({{ $movie->id }}, '{{ addslashes($movie->name) }}')" 
                            class="flex-1 bg-white/10 hover:bg-white/20 border border-white/10 text-white text-[9px] sm:text-[10px] font-black py-2.5 sm:py-3 rounded-xl transition-all active:scale-95">
                        ĐÁNH GIÁ
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Phim Sắp Chiếu -->
<div class="bg-gray-900 py-16">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-8">
            <h2 class="text-3xl font-bold text-white border-l-4 border-yellow-500 pl-3">{{ __('ui.coming_soon') }}</h2>
            <a href="/movies" class="text-yellow-500 hover:text-yellow-400 font-medium text-sm">{{ __('ui.view_all') }}</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
           @forelse ($comingSoon as $movie)
            <div class="group relative rounded-xl overflow-hidden bg-gray-800 transition-transform duration-300 hover:-translate-y-2">
               <img alt="{{ $movie->name }}" class="w-full h-96 object-cover opacity-80 group-hover:opacity-100 transition-opacity" src="{{ $movie->poster ?? 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=400&h=600&auto=format&fit=crop' }}">
                
                <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-60"></div>
                
                <div class="absolute bottom-0 w-full p-6">
                    <a href="{{ route('movies.show', $movie->id) }}">    
                        <h3 class="text-xl font-bold text-white mb-1 group-hover:text-yellow-500 transition-colors">{{ $movie->name }}</h3>
                    </a>
                    <p class="text-gray-400 text-sm flex items-center">
                        <i class="fa-regular fa-calendar-days mr-2 text-yellow-500"></i>
                        Khởi chiếu: {{ $movie->release_date ? \Carbon\Carbon::parse($movie->release_date)->format('d/m/Y') : 'Đang cập nhật' }}
                    </p>
                </div>
                
                <!-- Age Label -->
                <div class="absolute top-4 left-4">
                    <span class="bg-yellow-500 text-black text-xs font-black px-2 py-1 rounded shadow-lg">
                        {{ $movie->age_limit ? 'T' . $movie->age_limit : 'P' }}
                    </span>
                </div>
            </div>
            @empty
            @for ($i = 1; $i <= 4; $i++)
            <div class="group relative rounded-xl overflow-hidden bg-gray-800">
               <img alt="Coming soon" class="w-full h-96 object-cover opacity-50" src="https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=400&h=600&auto=format&fit=crop">
                <div class="absolute bottom-0 w-full p-6 bg-gradient-to-t from-gray-900 to-transparent">
                    <h3 class="text-xl font-bold text-white mb-1">Phim Sắp Ra Mắt</h3>
                    <p class="text-gray-400 text-sm">Đang cập nhật lịch chiếu</p>
                </div>
            </div>
            @endfor
            @endforelse
        </div>
    </div>
</div>

<!-- Tin Tức & Khuyến Mãi -->
<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h2 class="text-3xl font-bold text-white mb-8 border-l-4 border-blue-500 pl-3">{{ __('ui.news_promotions') }}</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @foreach ($newsPosts as $post)
        <div class="bg-gray-800 rounded-xl overflow-hidden border border-gray-700 hover:border-blue-500/50 transition-colors">
            <img src="{{ $post->thumbnail ?? 'https://via.placeholder.com/600x300?text=No+Image' }}"
     class="w-full h-48 object-cover"
     alt="{{ $post->title }}">
            <div class="p-6">
                <div class="text-blue-400 text-xs font-bold uppercase mb-2">
                    {{ $post->keywords ? \Illuminate\Support\Str::limit($post->keywords, 24) : 'Khuyến Mãi' }}
                </div>
                <a href="{{ route('posts.show', $post->id) }}">
    <h3 class="text-xl font-bold text-white mb-3 hover:text-blue-400 cursor-pointer">
        {{ $post->title }}
    </h3>
</a>
                <p class="text-gray-400 text-sm line-clamp-2 mt-1">
                    {{ \Illuminate\Support\Str::limit(strip_tags($post->content), 100) }}
                </p>
                <div class="mt-4 text-gray-500 text-sm flex items-center">
                    <i class="fa-regular fa-calendar mr-2"></i> {{ optional($post->publish_at ?? $post->created_at)->format('d/m/Y') }}
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
// Logic openReviewModal đã được chuyển vào layouts/app.blade.php
</script>
@endpush
