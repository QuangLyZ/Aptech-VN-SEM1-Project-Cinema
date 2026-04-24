@extends('layouts.app')

@section('content')
    
<div class="max-w-7xl mx-auto px-4 py-10 text-white">
    <div class="mt-10">
        <a href="{{ route('home') }}" class="text-blue-500 hover:text-blue-400 font-medium">
            <i class="fa-solid fa-arrow-left mr-2"></i> Quay lại trang chủ
        </a>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- LEFT: CONTENT -->
        <div class="lg:col-span-2">

            <!-- Title -->
            <h1 class="text-4xl font-bold mb-4 leading-tight">
                {{ $post->title }}
            </h1>

            <!-- Meta -->
            <div class="flex items-center justify-between text-gray-400 mb-6">
                <span>{{ \Carbon\Carbon::parse($post->created_at)->format('d/m/Y') }}</span>
                <span class="bg-blue-500/10 text-blue-400 px-3 py-1 rounded-full text-sm">
                    Bài viết
                </span>
            </div>

            <!-- CONTENT -->
            <div class="post-content">
                @php
                    $content = $post->content;

                    // youtube
                    $content = preg_replace(
                        '/<oembed url="https:\/\/youtu\.be\/(.*?)"><\/oembed>/',
                        '<div class="aspect-video my-6">
                            <iframe class="w-full h-full rounded-xl"
                                src="https://www.youtube.com/embed/$1"
                                frameborder="0"
                                allowfullscreen>
                            </iframe>
                        </div>',
                        $content
                    );

                    // fix figure bị bọc p
                    $content = preg_replace(
                        '/<p>\s*(<figure.*?>.*?<\/figure>)\s*<\/p>/is',
                        '$1',
                        $content
                    );
                @endphp

                {!! $content !!}
            </div>

        </div>
@php
    $relatedPosts = \App\Models\Post::where('id', '!=', $post->id)
        ->where('status', 'visible')
        ->latest()
        ->take(5)
        ->get();
@endphp
        <!-- RIGHT: SIDEBAR -->
        <div class="space-y-6">

            <div class="bg-gray-900/70 border border-gray-800 rounded-2xl p-5">
                <h3 class="text-lg font-bold mb-4">Tin tức khác</h3>

                @foreach($relatedPosts as $item)
                    <a href="{{ route('posts.show', $item->id) }}"
                       class="flex gap-3 mb-4 group">

                
                         <!-- <img src="{{ $post->thumbnail ?? 'https://via.placeholder.com/600x300?text=No+Image' }}"
     class="w-full h-48 object-cover"
     alt="{{ $post->title }}" class="w-24 h-16 object-cover rounded-lg"> -->
                        <img src="{{ $item->thumbnail }}"
     class="w-24 h-16 object-cover rounded-lg">
                        <div>
                            <h4 class="text-sm font-semibold group-hover:text-blue-400 line-clamp-2">
                                {{ $item->title }}
                            </h4>
                            <span class="text-xs text-gray-400">
                                {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}
                            </span>
                        </div>

                    </img>
                @endforeach

            </div>

        </div>

    </div>
        <div class="mt-10">
        <a href="{{ route('home') }}" class="text-blue-500 hover:text-blue-400 font-medium">
            <i class="fa-solid fa-arrow-left mr-2"></i> Quay lại trang chủ
        </a>
    </div>
</div>
@endsection
@section('scripts')
<style>
.post-content img {
    display: block;
    margin: 20px auto;
    max-width: 100%;
    height: auto;
    border-radius: 16px;
}

/* fix video */
.post-content iframe {
    width: 100%;
    height: 100%;
}

/* fix figure CKEditor */
.post-content figure {
    margin: 20px 0;
    text-align: center;
}

.post-content figure img {
    width: 100% !important;
    height: auto !important;
    object-fit: cover;
}

/* tránh lệch */
.post-content * {
    max-width: 100%;
}
</style>
@endsection