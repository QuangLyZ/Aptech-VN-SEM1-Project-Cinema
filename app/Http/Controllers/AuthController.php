<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\SendOtpMail;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ], [
            'email.required' => 'Sếp ơi, nhập Email (hoặc SĐT) vào kìa!',
            'password.required' => 'Mật khẩu đâu sếp ơi?',
        ]);

        // Hỗ trợ đăng nhập bằng cả Email hoặc Số điện thoại
        $credentials = ['password' => $request->password];
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $request->email;
        } else {
            $credentials['phone'] = $request->email;
        }

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'))->with('success', 'Đăng nhập thành công! Mời sếp vào nhà! 🎉');
        }

        return back()->withErrors([
            'email' => 'Tài khoản hoặc mật khẩu trật lất rồi sếp ơi! Kiểm tra lại nha.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home')->with('success', 'Đã đăng xuất thành công! Hẹn gặp lại sếp nha! 👋');
    }

    public function register(Request $request)
    {
        // 1. Kiểm tra dữ liệu đầu vào (Validation)
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Sếp ơi, tên không được để trống nha!',
            'email.required' => 'Email cũng phải điền vào nè.',
            'email.email' => 'Email sai định dạng rồi sếp.',
            'email.unique' => 'Ối giời, email này có người xài rồi!',
            'phone.required' => 'Số điện thoại đâu sếp ơi?',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.min' => 'Mật khẩu phải dài ít nhất 8 ký tự nha.',
            'password.confirmed' => 'Hai mật khẩu sếp nhập không khớp nhau kìa.',
        ]);

        // 2. Tạo mã OTP ngẫu nhiên 6 số
        $otpCode = rand(100000, 999999);

        // 3. Gói ghém toàn bộ thông tin đăng ký của khách + mã OTP vào một cục
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'otp' => $otpCode
        ];

        // 4. Cất cục thông tin này vào Tủ Khóa (Cache), cài giờ nổ là 5 phút
        // Lưu ý: Mình CHƯA lưu vào Database để tránh rác nếu họ không nhập OTP
        Cache::put('register_data_' . $request->email, $userData, now()->addMinutes(5));

        // 5. Gửi Email
        try {
            Mail::to($request->email)->send(new SendOtpMail($otpCode, $request->name));
        } catch (\Exception $e) {
            \Log::error('Lỗi gửi mail OTP: ' . $e->getMessage());
        }

        // 6. Chuyển hướng sang trang nhập OTP, kèm theo cái email để biết đang xác thực cho ai
        \Illuminate\Support\Facades\Session::put('verify_email', $request->email);
        return redirect()->route('otp.form')
            ->with('success', 'Mã xác thực OTP đã được gửi. Vui lòng kiểm tra email của bạn để tiếp tục!');
    }
}
