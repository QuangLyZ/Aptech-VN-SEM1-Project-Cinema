@extends('layouts.app')

@section('content')

@php
    $today      = today()->toDateString();
    $cinemaMap  = $cinemas->mapWithKeys(fn($c) => [(string)$c->id => $c->name])->toArray();
    $cinemaMap['all'] = 'Tất Cả Rạp';
    $dateMap    = collect($dates)->mapWithKeys(fn($d) => [
        $d['value'] => $d['day'].'/'.date('m', strtotime($d['value'])).'/'.date('Y', strtotime($d['value']))
    ])->toArray();
@endphp

{{-- HEADER --}}
<div class="bg-gray-900 border-b border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-white mb-6">Đang Chiếu Tại Rạp</h1>
        <div class="flex flex-col md:flex-row gap-4 bg-gray-800 p-4 rounded-xl border border-gray-700">
            <div class="flex-1 relative">
                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"></i>
                <input type="text" id="movieSearch" placeholder="Tìm tên phim, thể loại..."
                    onkeyup="applyFilters()"
                    class="block w-full pl-10 pr-3 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-1 focus:ring-red-500 focus:border-red-500 outline-none">
            </div>
            <select id="genreFilter" onchange="applyFilters()"
                class="bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-2 focus:ring-1 focus:ring-red-500 outline-none">
                <option value="">Tất cả thể loại</option>
                @foreach(['Hành Động','Viễn Tưởng','Phiêu Lưu','Giật Gân','Khoa Học Viễn Tưởng','Kinh Dị','Hài Hước','Tình Cảm','Hoạt Hình'] as $g)
                <option value="{{ $g }}">{{ $g }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

        {{-- SIDEBAR --}}
        <div class="md:col-span-1 space-y-6">

            {{-- Chọn Rạp --}}
            <div>
                <h3 class="font-bold text-white text-lg border-b border-gray-700 pb-2 mb-3">
                    <i class="fa-solid fa-building text-red-500 mr-2"></i>Chọn Rạp
                </h3>
                <div class="space-y-2">
                    {{-- Tất cả --}}
                    <button onclick="selectCinema(this,'all')" data-cinema="all"
                        class="cinema-btn active w-full text-left px-4 py-3 rounded-lg flex items-center justify-between transition-colors">
                        <span class="text-sm font-medium"><i class="fa-solid fa-globe mr-2"></i>Tất Cả Rạp</span>
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                    {{-- Từ DB --}}
                    @foreach($cinemas as $cinema)
                    <button onclick="selectCinema(this,'{{ $cinema->id }}')" data-cinema="{{ $cinema->id }}"
                        class="cinema-btn w-full text-left px-4 py-3 rounded-lg flex items-center justify-between transition-colors bg-gray-800 border border-gray-700 text-gray-300 hover:bg-gray-700 hover:text-white">
                        <span class="text-sm font-medium">{{ $cinema->name }}</span>
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Chọn Ngày --}}
            <div>
                <h3 class="font-bold text-white text-lg border-b border-gray-700 pb-2 mb-3">
                    <i class="fa-regular fa-calendar text-red-500 mr-2"></i>Ngày Chiếu
                </h3>
                <div class="flex overflow-x-auto gap-2 pb-2 hide-scrollbar">
                    @foreach($dates as $i => $date)
                    <div onclick="selectDate(this,'{{ $date['value'] }}')" data-date="{{ $date['value'] }}"
                        class="date-btn flex-shrink-0 w-16 h-20 rounded-lg flex flex-col items-center justify-center cursor-pointer transition-colors
                            {{ $i === 0 ? 'bg-red-600 text-white' : 'bg-gray-800 border border-gray-700 text-gray-400 hover:bg-gray-700' }}">
                        <span class="text-xs uppercase font-semibold">{{ $date['label'] }}</span>
                        <span class="font-bold text-xl">{{ $date['day'] }}</span>
                        <span class="text-xs opacity-80">{{ $date['month'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <a href="{{ route('cinemas.index') }}"
                class="block w-full text-center py-2.5 border border-red-500/50 text-red-400 hover:bg-red-600/10 rounded-lg text-sm font-medium transition-colors">
                <i class="fa-solid fa-map-location-dot mr-2"></i>Xem Tất Cả Rạp
            </a>
        </div>

        {{-- DANH SÁCH PHIM --}}
        <div class="md:col-span-3 space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-white" id="pageTitle">
                    {{ collect($dates)->first()['day'] }}/{{ date('m') }}/{{ date('Y') }} — Tất Cả Rạp
                </h2>
                <span class="text-sm text-gray-500" id="movieCount">{{ $movies->count() }} phim</span>
            </div>

            <div class="space-y-6">
                @forelse($movies as $movie)
                @php
                    // Poster: nếu là đường dẫn local thì dùng asset(), không thì dùng thẳng
                    $poster = $movie->poster
                        ? (str_starts_with($movie->poster, 'http') ? $movie->poster : asset($movie->poster))
                        : 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=300&h=400&auto=format&fit=crop';

                    $ageLabel = match(true) {
                        ($movie->age_limit ?? 0) >= 18 => 'T18',
                        ($movie->age_limit ?? 0) >= 16 => 'T16',
                        ($movie->age_limit ?? 0) >= 13 => 'T13',
                        default => 'P',
                    };

                    // Suất chiếu hôm nay để render ban đầu (server-side)
                    $todayShowtimes = $movie->dates_map[$today] ?? [];
                @endphp

                <div class="movie-item flex flex-col md:flex-row bg-gray-800 rounded-xl border border-gray-700 overflow-hidden hover:border-red-500/40 transition-all"
                    data-id="{{ $movie->id }}"
                    data-name="{{ strtolower($movie->name) }}"
                    data-genre="{{ strtolower($movie->genre ?? '') }}"
                    data-cinemas="{{ implode(',', array_map('strval', $movie->cinema_ids ?? [])) }}"
                    data-showtimes='@json($movie->dates_map ?? [])'>

                    {{-- Poster --}}
                    <div class="w-full md:w-44 h-64 md:h-auto flex-shrink-0 overflow-hidden">
                        <img src="{{ $poster }}" alt="{{ $movie->name }}"
                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                            onerror="this.src='https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=300&h=400&auto=format&fit=crop'">
                    </div>

                    {{-- Info --}}
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex gap-2 flex-wrap">
                                <span class="bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded">{{ $ageLabel }}</span>
                                <span class="text-xs border border-blue-500 text-blue-400 px-2 py-0.5 rounded">2D Phụ Đề</span>
                                <span class="text-xs border border-gray-600 text-gray-400 px-2 py-0.5 rounded">{{ $movie->genre }}</span>
                            </div>
                        </div>

                        <h3 class="text-2xl font-bold text-white mb-2">{{ $movie->name }}</h3>
                        <p class="text-sm text-gray-400 mb-3 line-clamp-2">{{ $movie->description }}</p>
                        <p class="text-sm text-gray-500 mb-4">
                            <i class="fa-regular fa-clock mr-1"></i>{{ $movie->duration }} phút
                            @if($movie->director)
                            &nbsp;|&nbsp;<i class="fa-solid fa-clapperboard mr-1"></i>{{ $movie->director }}
                            @endif
                        </p>

                        {{-- Suất chiếu --}}
                        <div class="mt-auto">
                            <p class="text-sm font-semibold text-gray-300 mb-2">
                                <i class="fa-solid fa-ticket text-red-500 mr-1.5"></i>Chọn suất chiếu:
                            </p>
                            <div class="showtime-list flex flex-wrap gap-2">
                                @forelse($todayShowtimes as $s)
                                <a href="{{ route('booking.show', $s['id']) }}"
                                    class="px-4 py-2 bg-gray-900 border border-gray-600 text-gray-200 rounded-md hover:border-red-500 hover:text-red-400 transition-colors text-sm font-medium">
                                    {{ $s['time'] }}
                                </a>
                                @empty
                                <span class="text-gray-600 text-sm italic">Chưa có suất chiếu hôm nay</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-gray-800 rounded-xl border border-gray-700 p-12 text-center">
                    <i class="fa-solid fa-film text-5xl text-gray-600 mb-4"></i>
                    <p class="text-gray-400 text-lg font-semibold">Chưa có phim nào</p>
                    <p class="text-gray-600 text-sm mt-2">
                        Chạy <code class="bg-gray-900 px-2 py-0.5 rounded text-red-400">php artisan migrate:fresh --seed</code>
                    </p>
                </div>
                @endforelse
            </div>

            <div id="noMovieResult" class="hidden bg-gray-800 rounded-xl border border-gray-700 p-12 text-center">
                <i class="fa-solid fa-magnifying-glass text-5xl text-gray-600 mb-4"></i>
                <p class="text-gray-400 text-lg font-semibold">Không tìm thấy phim phù hợp</p>
                <p class="text-gray-600 text-sm mt-1">Thử chọn rạp khác hoặc ngày khác</p>
            </div>
        </div>
    </div>
</div>

<style>
.hide-scrollbar::-webkit-scrollbar{display:none}
.hide-scrollbar{-ms-overflow-style:none;scrollbar-width:none}
.line-clamp-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.cinema-btn.active{background:rgba(220,38,38,.2)!important;border:1px solid rgb(239,68,68)!important;color:rgb(248,113,113)!important}
.date-btn.active{background:#dc2626!important;color:#fff!important;border:none!important}
</style>

<script>
let activeCinema = 'all';
let activeDate   = '{{ $today }}';

const cinemaNames = @json($cinemaMap);
const dateLabels  = @json($dateMap);

// Chọn rạp
function selectCinema(btn, id) {
    document.querySelectorAll('.cinema-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    activeCinema = String(id);
    applyFilters();
}

// Chọn ngày
function selectDate(btn, date) {
    document.querySelectorAll('.date-btn').forEach(b => {
        b.classList.remove('active','bg-red-600','text-white');
        b.classList.add('bg-gray-800','border','border-gray-700','text-gray-400');
    });
    btn.classList.add('active','bg-red-600','text-white');
    btn.classList.remove('bg-gray-800','border-gray-700','text-gray-400');
    activeDate = date;
    applyFilters();
}

// Áp dụng tất cả filter
function applyFilters() {
    const search = document.getElementById('movieSearch').value.toLowerCase().trim();
    const genre  = document.getElementById('genreFilter').value.toLowerCase();
    let visible  = 0;

    document.querySelectorAll('.movie-item').forEach(item => {
        const itemCinemas = item.dataset.cinemas.split(',').map(s => s.trim());
        const showtimes   = JSON.parse(item.dataset.showtimes || '{}');
        const timesOfDay  = showtimes[activeDate] ?? [];

        const matchSearch  = !search || item.dataset.name.includes(search);
        const matchGenre   = !genre  || item.dataset.genre.includes(genre);
        // Lọc rạp: so sánh string id
        const matchCinema  = activeCinema === 'all' || itemCinemas.includes(activeCinema);
        const matchDate    = timesOfDay.length > 0;

        if (matchSearch && matchGenre && matchCinema && matchDate) {
            item.style.display = '';
            visible++;

            // Cập nhật suất chiếu theo ngày + rạp đang chọn
            item.querySelector('.showtime-list').innerHTML = timesOfDay.length
                ? timesOfDay.map(s =>
                    `<a href="/booking/${s.id}"
                        class="px-4 py-2 bg-gray-900 border border-gray-600 text-gray-200 rounded-md hover:border-red-500 hover:text-red-400 transition-colors text-sm font-medium">
                        ${s.time}
                    </a>`
                  ).join('')
                : '<span class="text-gray-600 text-sm italic">Chưa có suất chiếu</span>';
        } else {
            item.style.display = 'none';
        }
    });

    document.getElementById('pageTitle').textContent =
        (dateLabels[activeDate] ?? activeDate) + ' — ' + (cinemaNames[activeCinema] ?? 'Tất Cả Rạp');
    document.getElementById('movieCount').textContent = visible + ' phim';
    document.getElementById('noMovieResult').classList.toggle('hidden', visible > 0);
}
</script>

@endsection