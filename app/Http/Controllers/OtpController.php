<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class OtpController extends Controller
{
    // B1: Gửi mã OTP vào Email
    public function sendOtp(Request $request)
    {
        // Gửi lại mã OTP khi đang ở trang xác nhận
        $email = Session::get('verify_email');

        if (!$email || !Cache::has('register_data_' . $email)) {
             return redirect()->route('register')->with('error', 'Phiên đăng ký đã hết hạn hoặc không tồn tại. Vui lòng đăng ký lại.');
        }

        $userData = Cache::get('register_data_' . $email);
        $newOtpCode = rand(100000, 999999);
        $userData['otp'] = $newOtpCode;
        $userData['expires_at'] = time() + 300; // Grace: Reset thời gian 5 phút mới

        // Cập nhật lại cache với OTP mới, reset 5 phút
        Cache::put('register_data_' . $email, $userData, now()->addMinutes(5));

        // Bắn email giao diện HTML xịn xò
        try {
            Mail::send('emails.otp', ['otp' => $newOtpCode, 'userName' => $userData['fullname']], function ($message) use ($email) {
                $message->to($email)->subject('🔒 [CINEBOOK] Mã Xác Thực OTP Của Bạn');
            });
        } catch (\Exception $e) {
            // bỏ qua lỗi mail
        }

        // Chuyển hướng người dùng qua trang nhập mã
        return redirect()->route('otp.form')->with('success', 'Đã gửi lại mã OTP mới. Vui lòng kiểm tra email của bạn!');
    }

    // B2: Hiện giao diện nhập OTP
    public function showVerifyForm()
    {
        $email = Session::get('verify_email');
        if (!$email || !Cache::has('register_data_' . $email)) {
            return redirect()->route('register')->with('error', 'Phiên xác thực đã hết hạn. Vui lòng đăng ký lại từ đầu.');
        }

        // Grace: Lấy thời gian còn lại (giả định 5 phút từ lúc tạo cache)
        // Lưu ý: Cache::put không trả về thời gian còn lại dễ dàng, 
        // nên ta sẽ lưu thêm một timestamp lúc tạo để tính toán.
        $userData = Cache::get('register_data_' . $email);
        $expiresAt = $userData['expires_at'] ?? (time() + 300);

        return view('auth.verify-otp', compact('expiresAt'));
    }

    // B3: Kiểm tra OTP user nhập vào có đúng không
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric'
        ]);

        $email = Session::get('verify_email');

        if (!$email) {
            return redirect()->route('register')->with('error', 'Không tìm thấy phiên xác thực. Vui lòng đăng ký lại.');
        }

        $userData = Cache::get('register_data_' . $email);

        // Grace: Nếu không tìm thấy userData trong Cache = Đã hết hạn 5 phút
        if (!$userData) {
            Session::forget('verify_email');
            return redirect()->route('register')->with('error', 'Mã OTP của bạn đã hết hạn (quá 5 phút). Vui lòng đăng ký lại.');
        }

        // Kiểm tra mã nhập vào
        if ($request->otp == $userData['otp']) {
            // Kiểm tra một lần nữa tránh trùng lặp Email trong DB (đề phòng race condition)
            if (User::where('email', $userData['email'])->exists()) {
                 Cache::forget('register_data_' . $email);
                 Session::forget('verify_email');
                 return redirect()->route('login')->with('error', 'Email này đã được đăng ký thành công trước đó.');
            }

            $user = User::create([
                'fullname' => $userData['fullname'],
                'email' => $userData['email'],
                'phone' => $userData['phone'],
                'password' => $userData['password'],
                'admin_role' => $userData['admin_role'] ?? false,
            ]);

            Cache::forget('register_data_' . $email);
            Session::forget('verify_email');

            Auth::login($user);

            return redirect()->to($user->admin_role ? route('admin.dashboard') : route('home'))
                ->with('success', 'Đăng ký và xác thực thành công! Chào mừng bạn đến với CineBook (◕‿-)v');
        }

        return back()->with('error', 'Mã OTP không chính xác. Vui lòng kiểm tra lại.');
    }
}
