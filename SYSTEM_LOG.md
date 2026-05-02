# CINEBOOK SYSTEM PROGRESS LOG

### NHẬT KÝ NÂNG CẤP HỆ THỐNG - 30/04/2026 21:57
- **Bảo mật Root Owner**: Cập nhật AuthController để ép buộc sử dụng Master Password từ .env cho các tài khoản Root. Chặn hoàn toàn việc sử dụng mật khẩu cũ/bừa trong Database để truy cập quyền tối cao.
- **Xác thực OTP 5 phút**: 
    - Backend: Thêm `expires_at` vào Cache và kiểm tra tính hợp lệ của thời gian trong `OtpController`. 
    - Frontend: Tích hợp đồng hồ đếm ngược JavaScript real-time.
    - UX: Tự động dùng SweetAlert thông báo và chuyển hướng về trang đăng ký khi mã OTP hết hạn.
- **Cấu trúc dữ liệu**: Thêm trường `expires_at` vào mảng dữ liệu tạm thời khi đăng ký.
- **Thông báo**: Sửa đổi thông báo lỗi đăng nhập thành dạng chung (Email/Mật khẩu không chính xác) để đảm bảo bảo mật.
