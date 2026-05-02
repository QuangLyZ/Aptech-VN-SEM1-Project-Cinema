@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex justify-center">
    <div class="bg-gray-800 p-8 rounded-2xl border border-gray-700 shadow-xl w-full max-w-md">
        
        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-white mb-2">Xác Thực OTP</h2>
            <p class="text-gray-400 text-sm">Vui lòng nhập mã 6 số chúng tôi vừa gửi đến email của bạn.</p>
            
            <!-- Grace: Đồng hồ đếm ngược -->
            <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-full bg-red-500/10 border border-red-500/20">
                <i class="fa-solid fa-clock text-red-500 animate-pulse"></i>
                <span id="countdown" class="text-red-500 font-mono font-bold text-lg">05:00</span>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-600 text-white p-4 rounded-lg mb-6 text-sm text-center">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-600 text-white p-4 rounded-lg mb-6 text-sm text-center font-bold">
                {{ session('error') }}
            </div>
        @endif

        <form id="otpForm" action="{{ route('otp.verify') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="otp" class="block text-sm font-medium text-gray-300 mb-1">Mã OTP Của Bạn</label>
                <input type="text" id="otp" name="otp" required maxlength="6" autofocus
                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white text-center text-2xl tracking-[0.5em] focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500 transition-colors" 
                    placeholder="------">
            </div>

            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition-colors flex justify-center items-center text-lg">
                <i class="fa-solid fa-check-circle mr-2"></i> Xác Thực Ngay
            </button>
        </form>

        <div class="text-center mt-6">
            <p class="text-gray-400 text-sm">Chưa nhận được mã? <a href="{{ route('otp.send') }}" class="text-blue-500 hover:text-blue-400 font-medium ml-1">Gửi lại OTP</a></p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const expiresAt = {{ $expiresAt }}; // Timestamp từ PHP
        const countdownElement = document.getElementById('countdown');
        
        function updateTimer() {
            const now = Math.floor(Date.now() / 1000);
            const timeLeft = expiresAt - now;

            if (timeLeft <= 0) {
                countdownElement.textContent = "00:00";
                clearInterval(timerInterval);
                
                // Grace: Khi hết hạn, tự động thông báo và đá về trang đăng ký
                Swal.fire({
                    title: 'Mã OTP hết hạn!',
                    text: 'Phiên làm việc đã quá 5 phút. Vui lòng đăng ký lại nhé!',
                    icon: 'error',
                    confirmButtonText: 'Quay lại Đăng ký',
                    confirmButtonColor: '#dc2626',
                    background: '#1f2937',
                    color: '#fff'
                }).then(() => {
                    window.location.href = "{{ route('register') }}";
                });
                return;
            }

            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            countdownElement.textContent = 
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }

        const timerInterval = setInterval(updateTimer, 1000);
        updateTimer();
    });
</script>
@endsection
