@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-10 text-white">


    <!-- Title -->
    <div class=" mb-10">
            <div class="mt-10">
        <a href="{{ route('home') }}" class="text-blue-500 hover:text-blue-400 font-medium">
            <i class="fa-solid fa-arrow-left mr-2"></i> Quay lại trang chủ
        </a>
        </div>        
        <h1 class="text-4xl font-extrabold tracking-tight">
            Tin tức
        </h1>
    </div>
    <!-- GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

        @foreach($posts as $post)
            <a href="{{ route('posts.show', $post->id) }}"
               class="group relative bg-gray-900/70 border border-gray-800 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl hover:-translate-y-1 transition duration-300">

                <!-- IMAGE -->
                <div class="relative h-52 overflow-hidden">
                    <img 
                        src="{{ $post->thumbnail }}"
                        class="w-full h-full object-cover group-hover:scale-110 transition duration-500"
                    alt="{{ $post->title }}">

                    <!-- overlay gradient -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>

                    <!-- badge -->
                    <span class="absolute top-3 left-3 bg-blue-500/90 text-xs px-3 py-1 rounded-full">
                        News
                    </span>
                </div>

                <!-- CONTENT -->
                <div class="p-5">

                    <h2 class="font-bold text-lg mb-2 line-clamp-2 group-hover:text-blue-400 transition">
                        {{ $post->title }}
                    </h2>

                    @php
    $cleanContent = preg_replace('/<img[^>]*>/i', '', $post->content);
    $cleanContent = preg_replace('/<figure.*?>.*?<\/figure>/is', '', $cleanContent);
    $cleanContent = preg_replace('/<iframe.*?>.*?<\/iframe>/is', '', $cleanContent);
    $cleanContent = preg_replace('/<oembed.*?>.*?<\/oembed>/is', '', $cleanContent);
    $cleanContent = strip_tags($cleanContent);
@endphp

<p class="text-sm text-gray-400 line-clamp-3 mb-4">
    {{ \Illuminate\Support\Str::limit($cleanContent, 100) }}
</p>

                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span>
                            {{ \Carbon\Carbon::parse($post->publish_at)->format('d/m/Y') }}
                        </span>
                    </div>

                </div>

            </a>
        @endforeach

    </div>

    <!-- Pagination -->
    <div class="mt-12">
        {{ $posts->links() }}
    </div>

</div>

@endsection