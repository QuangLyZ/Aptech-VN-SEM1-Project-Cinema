<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MovieController extends Controller
{
    public function index(): View
    {
        // GRACE: Tạm thời tắt Cache để kiểm tra lỗi 500
        $nowShowing = collect();
        $comingSoon = collect();
        $featuredMovie = null;
        $newsPosts = collect();
        $dbWarning = null;
        $moviesList = collect();

        try {
            $now = Carbon::now('Asia/Ho_Chi_Minh');
            $threeMonthsLater = $now->copy()->addMonths(3)->toDateString();

            // Tối ưu: Chỉ lấy những gì cần thiết và Eager Load Count/Sum thay vì cả Relation
            $baseQuery = Movie::query()
                ->withCount(['reviews' => fn($q) => $q->where('is_visible', true)])
                ->withSum(['reviews' => fn($q) => $q->where('is_visible', true)], 'rating');

            $nowShowing = (clone $baseQuery)
                ->where(function ($query) use ($threeMonthsLater) {
                    $query->whereNull('release_date')
                          ->orWhereDate('release_date', '<=', $threeMonthsLater);
                })
                ->orderByDesc('release_date')
                ->orderBy('name')
                ->limit(8)
                ->get();

            $comingSoon = (clone $baseQuery)
                ->whereDate('release_date', '>', $threeMonthsLater)
                ->orderBy('release_date')
                ->limit(8)
                ->get();

            //featuredMovie fallback logic
            $moviesForFeatured = (clone $baseQuery)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();

            $moviesWithSchedules = $this->attachFirstShowtimes(
                $nowShowing->concat($comingSoon)->concat($moviesForFeatured)->unique('id')->values()
            );

            $movieMap = $moviesWithSchedules->keyBy('id');
            $nowShowing = $nowShowing->map(fn ($movie) => $movieMap->get($movie->id, $movie));
            $comingSoon = $comingSoon->map(fn ($movie) => $movieMap->get($movie->id, $movie));
            $moviesList = $moviesForFeatured->map(fn ($movie) => $movieMap->get($movie->id, $movie));

            $featuredMovie = $nowShowing->firstWhere(fn ($movie) => filled($movie->booking_showtime_id))
                ?? $nowShowing->first()
                ?? $comingSoon->first()
                ?? $moviesList->first();

            $newsPosts = Post::query()
                ->published()
                ->orderByDesc('publish_at')
                ->orderByDesc('created_at')
                ->limit(3)
                ->get();
        } catch (QueryException $exception) {
            Log::warning('Home page movie query failed: ' . $exception->getMessage());
            $dbWarning = 'Không thể tải dữ liệu phim. Đang hiển thị bản lưu tạm hoặc dữ liệu mẫu.';
        }

        return view('home', [
            'movies' => $moviesList,
            'nowShowing' => $nowShowing,
            'comingSoon' => $comingSoon,
            'featuredMovie' => $featuredMovie,
            'newsPosts' => $newsPosts,
            'dbWarning' => $dbWarning
        ]);
    }

    public function show($id)
    {
        $movie = Movie::with(['reviews.user', 'showtimes' => function($query) {
            $query->where('start_time', '>=', now())
                  ->orderBy('start_time')
                  ->with(['room.cinema', 'subtitle']);
        }])->findOrFail($id);

        $averageRating = $movie->average_rating;

        // Group showtimes by date and then by cinema
        $groupedShowtimes = $movie->showtimes->groupBy(function($showtime) {
            return \Carbon\Carbon::parse($showtime->start_time)->format('Y-m-d');
        })->map(function($dayShowtimes) {
            return $dayShowtimes->groupBy(function($showtime) {
                return $showtime->room->cinema->name;
            });
        });

        return view('movies.show', compact('movie', 'averageRating', 'groupedShowtimes'));
    }

    public function suggestions(Request $request)
    {
        $q = mb_strtolower(trim($request->query('q', '')), 'UTF-8');
        
        if (!$q) {
            return response()->json([]);
        }

        $movies = Movie::where(DB::raw('LOWER(name)'), 'like', "%{$q}%")
            ->orderBy('name')
            ->limit(5)
            ->get(['id', 'name']);

        return response()->json($movies);
    }

    public function list(Request $request): View
    {
        $cinemas = collect();
        $movies = collect();
        $availableDates = collect();
        $selectedDate = Carbon::today()->toDateString();
        $selectedCinemaId = $request->integer('cinema') ?: null;
        $selectedCinema = null;
        $dbWarning = null;

        try {
            $cinemas = DB::table('cinemas')
                ->select('id', 'name', 'address')
                ->orderBy('name')
                ->get();

            if ($selectedCinemaId && ! $cinemas->contains(fn ($cinema) => (int) $cinema->id === $selectedCinemaId)) {
                $selectedCinemaId = null;
            }

            $availableDates = DB::table('showtimes')
                ->where('start_time', '>=', Carbon::today()->startOfDay())
                ->selectRaw('DATE(start_time) as show_date')
                ->distinct()
                ->orderBy('show_date')
                ->limit(5)
                ->get()
                ->pluck('show_date')
                ->map(fn ($date) => Carbon::parse($date));

            if ($availableDates->isEmpty()) {
                $availableDates = collect(range(0, 4))
                    ->map(fn ($offset) => Carbon::today()->copy()->addDays($offset));
            }

            $selectedDate = $this->resolveSelectedDate($request->query('date'), $availableDates);
            $selectedCinema = $cinemas->firstWhere('id', $selectedCinemaId);

            $showtimes = DB::table('showtimes as showtimes')
                ->join('movies as movies', 'movies.id', '=', 'showtimes.movie_id')
                ->join('rooms as rooms', 'rooms.id', '=', 'showtimes.room_id')
                ->join('cinemas as cinemas', 'cinemas.id', '=', 'rooms.cinema_id')
                ->leftJoin('subtitles as subtitles', 'subtitles.id', '=', 'showtimes.subtitle_id')
                ->leftJoin('reviews as reviews', 'reviews.movie_id', '=', 'movies.id')
                ->when($selectedDate !== 'all', fn ($query) => $query->whereDate('showtimes.start_time', $selectedDate))
                ->when($selectedCinemaId, fn ($query) => $query->where('cinemas.id', $selectedCinemaId))
                ->groupBy([
                    'movies.id',
                    'movies.name',
                    'movies.poster',
                    'movies.description',
                    'movies.genre',
                    'movies.duration',
                    'movies.release_date',
                    'movies.age_limit',
                    'showtimes.id',
                    'showtimes.start_time',
                    'cinemas.id',
                    'cinemas.name',
                    'rooms.name',
                    'subtitles.name',
                ])
                ->orderBy('showtimes.start_time')
                ->select([
                    'movies.id as movie_id',
                    'movies.name as movie_name',
                    'movies.poster',
                    'movies.description',
                    'movies.genre',
                    'movies.duration',
                    'movies.release_date',
                    'movies.age_limit',
                    'showtimes.id as showtime_id',
                    'showtimes.start_time',
                    'cinemas.id as cinema_id',
                    'cinemas.name as cinema_name',
                    'rooms.name as room_name',
                    'subtitles.name as subtitle_name',
                    DB::raw('SUM(reviews.rating) as total_rating_sum'),
                    DB::raw('COUNT(reviews.id) as total_review_count'),
                ])
                ->get();

            $movies = $showtimes
                ->groupBy('movie_id')
                ->map(function (Collection $movieShowtimes) {
                    $first = $movieShowtimes->first();
                    
                    // Công thức: (Tổng điểm + 5 điểm nền) / (Số lượt đánh giá + 1)
                    $sum = (float) $first->total_rating_sum;
                    $count = (int) $first->total_review_count;
                    $rating = round(($sum + 5) / ($count + 1), 1);

                    return (object) [
                        'id' => $first->movie_id,
                        'name' => $first->movie_name,
                        'poster' => $first->poster,
                        'description' => $first->description,
                        'genre' => $first->genre,
                        'duration' => $first->duration,
                        'release_date' => $first->release_date,
                        'age_limit' => $first->age_limit,
                        'average_rating' => $rating,
                        'showtimes' => $movieShowtimes->map(function ($showtime) {
                            return (object) [
                                'id' => $showtime->showtime_id,
                                'start_time' => $showtime->start_time,
                                'cinema_id' => $showtime->cinema_id,
                                'cinema_name' => $showtime->cinema_name,
                                'room_name' => $showtime->room_name,
                                'subtitle_name' => $showtime->subtitle_name,
                            ];
                        })->values(),
                    ];
                })
                ->values();

            // Sửa lại logic lọc tại đây
            if ($request->filled('q')) {
                $q = mb_strtolower(trim($request->query('q')), 'UTF-8');
                $movies = $movies->filter(function($movie) use ($q) {
                    $ageLabel = $movie->age_limit ? 't' . $movie->age_limit : 'p';
                    $cinemaNames = $movie->showtimes->pluck('cinema_name')->unique()->join(' ');
                    
                    return str_contains(mb_strtolower($movie->name, 'UTF-8'), $q) || 
                           str_contains(mb_strtolower($movie->genre ?? '', 'UTF-8'), $q) ||
                           str_contains(mb_strtolower($ageLabel, 'UTF-8'), $q) ||
                           str_contains(mb_strtolower($cinemaNames, 'UTF-8'), $q);
                })->values();
            }
        } catch (QueryException $exception) {
            Log::warning('Movies page schedule query failed.', [
                'message' => $exception->getMessage(),
                'selected_cinema_id' => $selectedCinemaId,
                'selected_date' => $selectedDate,
            ]);

            $dbWarning = 'Không thể tải phim và lịch chiếu từ database. Kiểm tra lại DB_HOST/DB_PORT/DB_DATABASE hoặc kết nối mạng tới Supabase.';
        }

        return view('movies.index', [
            'movies' => $movies,
            'cinemas' => $cinemas,
            'availableDates' => $availableDates,
            'selectedDate' => $selectedDate,
            'selectedCinemaId' => $selectedCinemaId,
            'selectedCinema' => $selectedCinema,
            'dbWarning' => $dbWarning,
        ]);
    }

    private function attachFirstShowtimes(Collection $movies): Collection
    {
        if ($movies->isEmpty()) {
            return $movies;
        }

        $movieIds = $movies->pluck('id')->filter()->values();

        if ($movieIds->isEmpty()) {
            return $movies;
        }

        $baseShowtimeQuery = DB::table('showtimes as showtimes')
            ->join('rooms as rooms', 'rooms.id', '=', 'showtimes.room_id')
            ->join('cinemas as cinemas', 'cinemas.id', '=', 'rooms.cinema_id')
            ->whereIn('showtimes.movie_id', $movieIds)
            ->select([
                'showtimes.id',
                'showtimes.movie_id',
                'showtimes.start_time',
                'cinemas.name as cinema_name',
            ]);

        $upcomingShowtimes = (clone $baseShowtimeQuery)
            ->where('showtimes.start_time', '>=', now())
            ->orderBy('showtimes.start_time')
            ->get();

        $fallbackShowtimes = (clone $baseShowtimeQuery)
            ->orderByDesc('showtimes.start_time')
            ->get();

        $showtimesByMovie = $upcomingShowtimes
            ->groupBy('movie_id')
            ->union(
                $fallbackShowtimes
                    ->groupBy('movie_id')
                    ->map(fn (Collection $items) => $items->sortByDesc('start_time')->values())
            );

        return $movies->map(function ($movie) use ($showtimesByMovie) {
            $movieShowtimes = $showtimesByMovie->get($movie->id);
            $firstShowtime = $movieShowtimes instanceof Collection ? $movieShowtimes->first() : null;

            $movie->booking_showtime_id = $firstShowtime->id ?? null;
            $movie->booking_start_time = $firstShowtime?->start_time;
            $movie->booking_cinema_name = $firstShowtime?->cinema_name;

            return $movie;
        });
    }

    private function resolveSelectedDate(?string $requestedDate, Collection $availableDates): string
    {
        // Support special value 'all' which means do not filter by date
        if ($requestedDate === 'all') {
            return 'all';
        }

        if ($requestedDate) {
            try {
                $parsedDate = Carbon::parse($requestedDate)->toDateString();

                if ($availableDates->contains(fn (Carbon $date) => $date->toDateString() === $parsedDate)) {
                    return $parsedDate;
                }
            } catch (\Throwable) {
                // Fall back to the first available date below.
            }
        }

        return $availableDates->first()->toDateString();
    }
}
