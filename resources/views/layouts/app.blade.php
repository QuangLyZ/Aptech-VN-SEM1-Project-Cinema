<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'CineBook - Đặt vé xem phim nhanh chóng')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif !important; }
    </style>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 cho Popup -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="font-sans antialiased bg-slate-950 text-gray-100 min-h-screen">

    <nav class="bg-slate-900/80 backdrop-blur-xl border-b border-white/5 sticky top-0 z-50 w-full">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-24 items-center">
                <!-- Left Side: Logo & Desktop Menu -->
                <div class="flex items-center gap-8">
                    <a href="/" class="flex items-center gap-2 group shrink-0">
                        <div class="bg-red-600 p-2 rounded-lg shadow-lg shadow-red-600/20 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-film text-white text-xl"></i>
                        </div>
                        <span class="text-2xl font-black tracking-tighter text-white">Cine<span class="text-red-600">Book</span></span>
                    </a>

                    <!-- Desktop Menu: Chỉ hiện trên màn hình lớn (xl) để tránh chật chội -->
                    <div class="hidden xl:flex items-center space-x-8">
                        <a href="/" class="text-gray-400 hover:text-white text-sm font-bold transition-colors">Trang Chủ</a>
                        <a href="/movies" class="text-gray-400 hover:text-white text-sm font-bold transition-colors">Phim & Lịch Chiếu</a>
                        <a href="{{ route('cinemas.index') }}" class="text-gray-400 hover:text-white text-sm font-bold transition-colors">Danh Sách Rạp</a>
                        <a href="/feedback" class="text-gray-400 hover:text-white text-sm font-bold transition-colors">Góp Ý & Hỗ Trợ</a>
                    </div>
                </div>

                <!-- Right Side: Search & Auth & Hamburger -->
                <div class="flex items-center gap-4 md:gap-6">
                    <!-- Search Bar: Ẩn trên mobile, hiện từ md -->
                    <div class="hidden md:block relative group">
                        <form action="{{ route('movies.index') }}" method="GET" class="relative">
                            <input type="text" name="q" id="globalSearchInput" placeholder="Tìm kiếm phim, rạp..." 
                                class="bg-white/5 border border-white/10 rounded-full pl-5 pr-12 py-2.5 text-sm text-white focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 w-48 lg:w-64 transition-all placeholder:text-gray-500 group-hover:bg-white/10">
                            <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition-colors">
                                <i class="fa-solid fa-search"></i>
                            </button>
                            <!-- Search Suggestions -->
                            <div id="searchSuggestions" class="absolute top-full left-0 right-0 mt-2 bg-gray-900/95 backdrop-blur-xl border border-gray-800 rounded-2xl shadow-2xl hidden z-50 overflow-hidden"></div>
                        </form>
                    </div>

                    <div class="flex items-center gap-3">
                        @guest
                            <div class="flex items-center gap-1 sm:gap-3">
                                <a href="/login" class="text-gray-400 hover:text-white text-sm font-bold px-3 py-2 transition-colors">Đăng Nhập</a>
                                <a href="/register" class="bg-red-600 hover:bg-red-700 text-white px-4 sm:px-6 py-2 sm:py-2.5 rounded-xl text-xs sm:text-sm font-black shadow-lg shadow-red-600/20 transition-all active:scale-95">Đăng Ký</a>
                            </div>
                        @else
                            <!-- Notifications & User Dropdown (Original code kept but refined) -->
                            <div class="flex items-center gap-4 sm:gap-5">
                                <!-- Notification -->
                                <div class="relative">
                                    <button id="notificationButton" type="button" class="relative text-gray-400 hover:text-white transition-colors">
                                        <i class="fa-regular fa-bell text-[1.1rem]"></i>
                                        <span id="notificationBadge" class="hidden absolute -top-1 -right-1 min-w-[1rem] rounded-full bg-red-500 px-1.5 text-[0.65rem] font-semibold text-white leading-none"></span>
                                    </button>
                                    <div id="notificationDropdown" class="hidden absolute right-0 mt-3 w-80 sm:w-96 max-h-[420px] overflow-hidden rounded-3xl border border-gray-800 bg-gray-900 shadow-2xl z-50">
                                        <div class="flex items-center justify-between border-b border-gray-800 px-4 py-3 text-sm text-gray-300">
                                            <div class="font-semibold text-white">Thông báo</div>
                                            <button id="notificationMarkAllRead" class="text-xs text-gray-400 hover:text-white">Đánh dấu đã đọc</button>
                                        </div>
                                        <div id="notificationList" class="max-h-[340px] overflow-y-auto"></div>
                                        <div id="notificationEmpty" class="hidden px-4 py-6 text-center text-sm text-gray-500">Chưa có thông báo mới.</div>
                                    </div>
                                </div>

                                <!-- User Dropdown -->
                                <div class="relative group h-full flex items-center">
                                    <button class="flex items-center gap-2.5 text-sm font-medium text-gray-200 hover:text-white transition-colors py-2">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-red-500 to-red-700 text-white font-bold text-xs shadow-md">
                                            {{ mb_strtoupper(mb_substr(Auth::user()->fullname ?? Auth::user()->name ?? 'U', 0, 1, 'UTF-8'), 'UTF-8') }}
                                        </div>
                                        <span class="hidden sm:inline-block max-w-[120px] truncate">{{ Auth::user()->fullname ?? Auth::user()->name ?? 'User' }}</span>
                                        <i class="fa-solid fa-chevron-down text-[10px] text-gray-400 group-hover:text-white transition-transform duration-300 group-hover:rotate-180"></i>
                                    </button>
                                    <div class="absolute top-[100%] right-0 h-4 w-full"></div>
                                    <div class="absolute right-0 top-[calc(100%+0.5rem)] w-56 origin-top-right rounded-2xl border border-gray-800 bg-gray-900/95 backdrop-blur-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 transform group-hover:translate-y-0 translate-y-2">
                                        <div class="p-2">
                                            <div class="px-3 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Quản lý</div>
                                            <a href="{{ route('account.index', ['tab' => 'profile']) }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white transition-colors">
                                                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-gray-800 text-gray-400"><i class="fa-solid fa-gear"></i></div> Cài đặt tài khoản
                                            </a>
                                            <a href="{{ route('account.index', ['tab' => 'tickets']) }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white transition-colors">
                                                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-gray-800 text-gray-400"><i class="fa-solid fa-ticket"></i></div> Vé của tôi
                                            </a>
                                            @if(Auth::user()->isSystemOwner())
                                                <a href="{{ route('admin.system-owner.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-black text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-colors border border-red-500/20 shadow-[0_0_15px_rgba(239,68,68,0.1)] mb-2">
                                                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-red-500/20 text-red-500 animate-pulse"><i class="fa-solid fa-crown"></i></div> QUẢN TRỊ TỐI CAO
                                                </a>
                                            @endif
                                            @if(Auth::user()->admin_role)
                                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-emerald-400 transition-colors">
                                                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-gray-800 text-gray-400"><i class="fa-solid fa-chart-line"></i></div> Trang Quản trị
                                                </a>
                                            @endif
                                            <div class="my-1.5 border-t border-gray-800"></div>
                                            <form action="{{ route('logout') }}" method="POST" class="block m-0">
                                                @csrf
                                                <button type="submit" class="w-full flex items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-medium text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-colors">
                                                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-red-500/10"><i class="fa-solid fa-right-from-bracket"></i></div> Đăng xuất
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endguest

                        <!-- Hamburger Button: Chỉ hiện dưới xl -->
                        <button type="button" onclick="toggleMobileMenu()" class="xl:hidden flex items-center justify-center w-10 h-10 rounded-xl bg-white/5 border border-white/10 text-gray-400 hover:text-white hover:bg-white/10 transition-all">
                            <i class="fa-solid fa-bars-staggered text-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Overlay -->
        <div id="mobileMenu" class="fixed inset-0 z-[100] hidden">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/60 backdrop-blur-md transition-opacity" onclick="toggleMobileMenu()"></div>
            <!-- Drawer -->
            <div class="absolute right-0 top-0 bottom-0 w-[280px] bg-[#0f172a] shadow-2xl border-l border-white/5 flex flex-col p-6 transform translate-x-full transition-transform duration-300" id="mobileMenuContent">
                <div class="flex justify-between items-center mb-8">
                    <div class="text-sm font-black tracking-widest text-gray-500 uppercase">Menu</div>
                    <button onclick="toggleMobileMenu()" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <nav class="flex flex-col gap-2">
                    <a href="/" class="flex items-center gap-4 px-4 py-3 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition-all">
                        <i class="fa-solid fa-house w-5"></i> <span class="font-bold">Trang Chủ</span>
                    </a>
                    <a href="/movies" class="flex items-center gap-4 px-4 py-3 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition-all">
                        <i class="fa-solid fa-film w-5"></i> <span class="font-bold">Phim & Lịch Chiếu</span>
                    </a>
                    <a href="{{ route('cinemas.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition-all">
                        <i class="fa-solid fa-location-dot w-5"></i> <span class="font-bold">Danh Sách Rạp</span>
                    </a>
                    <a href="/feedback" class="flex items-center gap-4 px-4 py-3 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition-all">
                        <i class="fa-solid fa-headset w-5"></i> <span class="font-bold">Góp Ý & Hỗ Trợ</span>
                    </a>
                </nav>

                <!-- Mobile Search -->
                <div class="mt-8 pt-8 border-t border-white/5">
                    <form action="{{ route('movies.index') }}" method="GET" class="relative">
                        <input type="text" name="q" placeholder="Tìm phim..." 
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-red-500">
                        <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">
                            <i class="fa-solid fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Review Modal Global -->
    <div id="reviewModal"
        class="fixed inset-0 z-[110] hidden flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
        <div
            class="bg-gray-900 border border-gray-700 w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-gray-800 flex justify-between items-center bg-gray-900/50">
                <div>
                    <h3 class="text-xl font-bold text-white" id="modalMovieName">Đánh giá phim</h3>
                    <p class="text-sm text-gray-400">Chia sẻ cảm nhận của sếp về bộ phim này</p>
                </div>
                <button onclick="closeReviewModal()" class="text-gray-400 hover:text-white transition-colors">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <div class="flex-grow overflow-y-auto p-6 space-y-8">
                @auth
                    <form id="reviewForm" class="bg-gray-800/50 p-6 rounded-xl border border-gray-700">
                        <input type="hidden" id="modalMovieId">
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-400 mb-3">Sếp cho mấy sao?</label>
                            <div class="flex space-x-3" id="starRating">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button" onclick="setRating({{ $i }})"
                                        class="text-3xl transition-all duration-200 hover:scale-110 focus:outline-none">
                                        <i class="fa-star star-icon fa-regular text-gray-600" data-index="{{ $i }}"></i>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" id="ratingValue" name="rating" value="0">
                        </div>
                        <div class="mb-6">
                            <label for="comment" class="block text-sm font-medium text-gray-400 mb-2">Lời bình của
                                sếp</label>
                            <textarea id="comment" name="comment" rows="3"
                                class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all"
                                placeholder="Phim hay không sếp?"></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-8 rounded-lg transition-all shadow-lg shadow-red-600/20 active:scale-95">
                                GỬI ĐÁNH GIÁ
                            </button>
                        </div>
                    </form>
                @else
                    <div class="bg-gray-800/50 p-8 rounded-xl border border-dashed border-gray-700 text-center">
                        <i class="fa-solid fa-user-lock text-4xl text-gray-600 mb-4"></i>
                        <p class="text-gray-300 mb-4">Sếp cần đăng nhập để viết đánh giá!</p>
                        <a href="{{ route('login') }}"
                            class="inline-block bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">Đăng
                            nhập ngay</a>
                    </div>
                @endauth
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-lg font-bold text-white flex items-center">
                            <i class="fa-solid fa-comments mr-2 text-red-500"></i>
                            Đánh giá từ cộng đồng
                        </h4>
                        <div class="text-sm text-gray-400">
                            <span id="avgRatingText">0</span>/5 <i class="fa-solid fa-star text-yellow-500"></i> (<span
                                id="countText">0</span> đánh giá)
                        </div>
                    </div>
                    <div id="reviewsList" class="space-y-4">
                        <div class="text-center py-10 text-gray-500">
                            <p>Đang tải đánh giá...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            const content = document.getElementById('mobileMenuContent');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                setTimeout(() => {
                    content.classList.remove('translate-x-full');
                }, 10);
                document.body.style.overflow = 'hidden';
            } else {
                content.classList.add('translate-x-full');
                setTimeout(() => {
                    menu.classList.add('hidden');
                }, 300);
                document.body.style.overflow = 'auto';
            }
        }

        let currentRating = 0;
        function openReviewModal(movieId, movieName) {
            document.getElementById('modalMovieId').value = movieId;
            document.getElementById('modalMovieName').innerText = movieName;
            document.getElementById('reviewModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            if (document.getElementById('reviewForm')) { document.getElementById('reviewForm').reset(); setRating(0); }
            loadReviews(movieId);
        }
        function closeReviewModal() {
            document.getElementById('reviewModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        function setRating(rating) {
            currentRating = rating;
            const input = document.getElementById('ratingValue');
            if (input) input.value = rating;
            const stars = document.querySelectorAll('.star-icon');
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.remove('fa-regular', 'text-gray-600');
                    star.classList.add('fa-solid', 'text-yellow-500');
                } else {
                    star.classList.remove('fa-solid', 'text-yellow-500');
                    star.classList.add('fa-regular', 'text-gray-600');
                }
            });
        }
        function loadReviews(movieId) {
            const list = document.getElementById('reviewsList');
            fetch(`/movies/${movieId}/reviews`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('avgRatingText').innerText = data.average_rating;
                    document.getElementById('countText').innerText = data.review_count;
                    if (data.reviews.length === 0) {
                        list.innerHTML = `<div class="text-center py-10 text-gray-500 italic text-sm">Chưa có đánh giá nào.</div>`;
                        return;
                    }
                    list.innerHTML = data.reviews.map(review => `
                    <div class="bg-gray-800/40 p-4 rounded-xl border border-gray-800">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-red-600 flex items-center justify-center text-white font-bold text-xs mr-3">
                                    ${(review.user.fullname || 'U').charAt(0).toUpperCase()}
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-white">${review.user.fullname}</div>
                                    <div class="text-[10px] text-gray-500">${new Date(review.created_at).toLocaleDateString('vi-VN')}</div>
                                </div>
                            </div>
                            <div class="flex text-yellow-500 text-[10px]">
                                ${Array(5).fill(0).map((_, i) => `<i class="fa-${i < review.rating ? 'solid' : 'regular'} fa-star"></i>`).join('')}
                            </div>
                        </div>
                        <p class="text-sm text-gray-300">${review.comment || ''}</p>
                    </div>
                `).join('');
                });
        }
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('reviewForm');
            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const movieId = document.getElementById('modalMovieId').value;
                    const rating = document.getElementById('ratingValue').value;
                    const comment = document.getElementById('comment').value;
                    if (rating == 0) {
                        Swal.fire({ icon: 'warning', title: 'Opps!', text: 'Vui lòng chọn số sao!', background: '#1f2937', color: '#fff' });
                        return;
                    }
                    fetch(`/movies/${movieId}/reviews`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({ rating, comment })
                    })
                        .then(res => {
                            return res.json().then(data => {
                                if (!res.ok) {
                                    throw new Error(data.message || 'Đã có lỗi xảy ra!');
                                }
                                return data;
                            });
                        })
                        .then(data => {
                            Swal.fire({ icon: 'success', title: 'Thành công!', text: data.message, background: '#1f2937', color: '#fff' });
                            loadReviews(movieId);
                        })
                        .catch(err => {
                            Swal.fire({ icon: 'error', title: 'Thất bại!', text: err.message, background: '#1f2937', color: '#fff' });
                        });
                });
            }
        });
    </script>

    <!-- Footer -->
    <footer class="bg-gray-950 border-t border-gray-800 pt-10 pb-6 mt-auto">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <a href="/" class="flex items-center text-red-500 font-bold text-2xl tracking-tighter mb-4">
                        <i class="fa-solid fa-film mr-2 text-red-600"></i> Cine<span class="text-white">Book</span>
                    </a>
                    <p class="text-sm text-gray-400 leading-relaxed mb-4">
                        Hệ thống đặt vé xem phim trực tuyến hàng đầu, mang đến trải nghiệm tuyệt vời và tiện lợi nhất
                        cho người dùng.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><i
                                class="fa-brands fa-facebook"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><i
                                class="fa-brands fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><i
                                class="fa-brands fa-instagram"></i></a>
                    </div>
                </div>

                <div>
                    <h3 class="text-white font-semibold mb-4 uppercase text-sm tracking-wider">CineBook</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-red-500 transition-colors">Về chúng tôi</a></li>
                        <li><a href="#" class="hover:text-red-500 transition-colors">Tin tức</a></li>
                        <li><a href="#" class="hover:text-red-500 transition-colors">Tuyển dụng</a></li>
                        <li><a href="#" class="hover:text-red-500 transition-colors">Liên hệ</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white font-semibold mb-4 uppercase text-sm tracking-wider">Hỗ Trợ</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-red-500 transition-colors">Điều khoản sử dụng</a></li>
                        <li><a href="#" class="hover:text-red-500 transition-colors">Chính sách bảo mật</a></li>
                        <li><a href="#" class="hover:text-red-500 transition-colors">Giải đáp câu hỏi (FAQs)</a></li>
                        <li><a href="/feedback" class="hover:text-red-500 transition-colors">Góp ý</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white font-semibold mb-4 uppercase text-sm tracking-wider">Tải Ứng Dụng</h3>
                    <div class="space-y-3">
                        <a href="#"
                            class="flex flex-col items-start bg-gray-800 hover:bg-gray-700 border border-gray-700 rounded-lg px-3 py-2 transition-colors">
                            <div class="text-xs text-gray-400">Download on the</div>
                            <div class="text-sm font-semibold text-white"><i class="fa-brands fa-apple mr-2"></i>App
                                Store</div>
                        </a>
                        <a href="#"
                            class="flex flex-col items-start bg-gray-800 hover:bg-gray-700 border border-gray-700 rounded-lg px-3 py-2 transition-colors">
                            <div class="text-xs text-gray-400">GET IT ON</div>
                            <div class="text-sm font-semibold text-white"><i
                                    class="fa-brands fa-google-play mr-2"></i>Google Play</div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-6 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} CineBook. All rights reserved.
            </div>
        </div>
    </footer>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchInput = document.getElementById('globalSearchInput');
            const searchSuggestions = document.getElementById('searchSuggestions');
            let searchTimeout;

            if (searchInput && searchSuggestions) {
                searchInput.addEventListener('input', function () {
                    clearTimeout(searchTimeout);
                    const q = this.value.trim();

                    if (q.length < 1) {
                        searchSuggestions.classList.add('hidden');
                        return;
                    }

                    searchTimeout = setTimeout(() => {
                        fetch(`/movies/suggestions?q=${encodeURIComponent(q)}`)
                            .then(res => res.json())
                            .then(data => {
                                if (data.length > 0) {
                                    searchSuggestions.innerHTML = data.map(movie => `
                                <a href="/movies?q=${encodeURIComponent(movie.name)}" class="block px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors border-b border-gray-700/50 last:border-0 truncate" title="${movie.name}">
                                    <i class="fa-solid fa-film mr-2 text-gray-500"></i> ${movie.name}
                                </a>
                            `).join('');
                                    searchSuggestions.classList.remove('hidden');
                                } else {
                                    searchSuggestions.innerHTML = `<div class="px-4 py-3 text-sm text-gray-500 italic text-center">Không tìm thấy phim phù hợp</div>`;
                                    searchSuggestions.classList.remove('hidden');
                                }
                            })
                            .catch(err => console.error("Search error:", err));
                    }, 300);
                });

                // Hide when clicked outside
                document.addEventListener('click', function (e) {
                    if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
                        searchSuggestions.classList.add('hidden');
                    }
                });

                // Reopen when focused
                searchInput.addEventListener('focus', function () {
                    if (this.value.trim().length >= 1 && searchSuggestions.innerHTML.trim() !== '') {
                        searchSuggestions.classList.remove('hidden');
                    }
                });
            }
        });
    </script>

    @auth
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const notificationButton = document.getElementById('notificationButton');
                const notificationDropdown = document.getElementById('notificationDropdown');
                const notificationBadge = document.getElementById('notificationBadge');
                const notificationList = document.getElementById('notificationList');
                const notificationEmpty = document.getElementById('notificationEmpty');
                const markAllButton = document.getElementById('notificationMarkAllRead');
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                async function fetchNotifications() {
                    try {
                        const response = await fetch('{{ route('notifications.index') }}', {
                            headers: {
                                'Accept': 'application/json',
                            },
                        });

                        if (!response.ok) {
                            return;
                        }

                        const data = await response.json();
                        notificationBadge.textContent = data.unread_count > 0 ? data.unread_count : '';
                        notificationBadge.classList.toggle('hidden', data.unread_count === 0);

                        if (data.notifications.length === 0) {
                            notificationList.innerHTML = '';
                            notificationEmpty.classList.remove('hidden');
                            return;
                        }

                        notificationEmpty.classList.add('hidden');
                        notificationList.innerHTML = data.notifications.map(notification => {
                            const isRead = notification.read_at !== null;
                            return `
                            <a href="${notification.action_url || '#'}" data-id="${notification.id}" class="block border-b border-gray-800 px-4 py-3 text-sm transition-colors ${isRead ? 'bg-gray-900' : 'bg-gray-800 hover:bg-gray-700'}">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-white font-semibold">${notification.title}</div>
                                    <div class="text-[0.72rem] text-gray-400">${notification.created_at ?? ''}</div>
                                </div>
                                <p class="mt-1 text-gray-300 leading-relaxed">${notification.message}</p>
                            </a>
                        `;
                        }).join('');
                    } catch (error) {
                        console.error('Không thể tải thông báo:', error);
                    }
                }

                async function markAllAsRead() {
                    try {
                        await fetch('{{ route('notifications.readAll') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                            },
                        });
                        fetchNotifications();
                    } catch (error) {
                        console.error('Không thể đánh dấu tất cả thông báo đã đọc:', error);
                    }
                }

                async function markNotificationRead(notificationId) {
                    try {
                        await fetch(`/notifications/read/${notificationId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                            },
                        });
                    } catch (error) {
                        console.error('Không thể đánh dấu thông báo đã đọc:', error);
                    }
                }

                notificationList.addEventListener('click', function (event) {
                    const notificationItem = event.target.closest('a[data-id]');
                    if (!notificationItem) {
                        return;
                    }

                    event.preventDefault();
                    const notificationId = notificationItem.dataset.id;
                    const targetUrl = notificationItem.getAttribute('href');

                    if (notificationId) {
                        markNotificationRead(notificationId).then(() => {
                            if (targetUrl && targetUrl !== '#') {
                                window.location.href = targetUrl;
                            } else {
                                fetchNotifications();
                            }
                        });
                    }
                });

                notificationButton.addEventListener('click', function (event) {
                    event.stopPropagation();
                    notificationDropdown.classList.toggle('hidden');
                    if (!notificationDropdown.classList.contains('hidden')) {
                        fetchNotifications();
                    }
                });

                markAllButton.addEventListener('click', function (event) {
                    event.preventDefault();
                    markAllAsRead();
                });

                document.addEventListener('click', function (event) {
                    if (!notificationDropdown.contains(event.target) && event.target !== notificationButton) {
                        notificationDropdown.classList.add('hidden');
                    }
                });
            });
        </script>
    @endauth

    <!-- Toast Notification cho các thông báo Thành công & Lỗi thông thường (Đăng nhập, thêm phim...) -->
    @if(session('success') || session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    background: '#1f2937',
                    color: '#fff',
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                @if(session('success'))
                    Toast.fire({
                        icon: 'success',
                        title: `{!! session('success') !!}`
                    });
                @endif

                @if(session('error'))
                    Toast.fire({
                        icon: 'error',
                        title: `{!! session('error') !!}`
                    });
                @endif
        });
        </script>
    @endif

    @if(session('payment_success') || session('payment_error'))
        <!-- Custom Payment Popup -->
        <div id="paymentPopup"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity duration-300">
            <div
                class="bg-[#1a1d24] rounded-2xl shadow-2xl border border-gray-700/50 w-full max-w-sm p-8 text-center relative transform scale-100 transition-transform duration-300">
                <!-- Close Button -->
                <button onclick="document.getElementById('paymentPopup').remove()"
                    class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-800 text-gray-400 hover:text-white hover:bg-gray-700 transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>

                @if(session('payment_success'))
                    <!-- Success Icon -->
                    <div
                        class="mx-auto w-16 h-16 bg-[#10b981] rounded-full flex items-center justify-center mb-6 shadow-[0_0_20px_rgba(16,185,129,0.4)]">
                        <i class="fa-solid fa-check text-white text-3xl"></i>
                    </div>

                    <p class="text-[#10b981] text-xs font-bold tracking-[0.2em] uppercase mb-2">Payment Success</p>
                    <h3 class="text-white text-2xl font-bold mb-3">Thanh toán thành công</h3>
                    <p class="text-gray-400 text-sm mb-8 leading-relaxed px-4">{!! session('payment_success') !!}</p>

                    <a href="{{ route('account.index', ['tab' => 'tickets']) }}"
                        onclick="document.getElementById('paymentPopup').remove()"
                        class="inline-block bg-[#10b981] hover:bg-[#059669] text-white font-medium px-8 py-2.5 rounded-full transition-colors shadow-[0_4px_14px_0_rgba(16,185,129,0.39)]">
                        Xem vé của tôi
                    </a>
                @else
                    <!-- Error Icon -->
                    <div
                        class="mx-auto w-16 h-16 bg-red-500 rounded-full flex items-center justify-center mb-6 shadow-[0_0_20px_rgba(239,68,68,0.4)]">
                        <i class="fa-solid fa-xmark text-white text-3xl"></i>
                    </div>

                    <p class="text-red-500 text-xs font-bold tracking-[0.2em] uppercase mb-2">Payment Failed</p>
                    <h3 class="text-white text-2xl font-bold mb-3">Thanh toán thất bại</h3>
                    <p class="text-gray-400 text-sm mb-8 leading-relaxed px-4">{!! session('payment_error') !!}</p>

                    <button onclick="document.getElementById('paymentPopup').remove()"
                        class="bg-red-500 hover:bg-red-600 text-white font-medium px-8 py-2.5 rounded-full transition-colors shadow-[0_4px_14px_0_rgba(239,68,68,0.39)]">
                        Thử lại
                    </button>
                @endif
            </div>
        </div>

        <script>
            // Click outside to close
            document.getElementById('paymentPopup').addEventListener('click', function (e) {
                if (e.target === this) {
                    this.remove();
                }
            });
        </script>
    @endif

    @stack('scripts')
</body>

</html>
