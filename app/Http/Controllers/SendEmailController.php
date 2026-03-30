<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SendEmailController extends Controller
{
    public function send(Request $request)
    {
        // config thay vao trong file .env
        /*
            MAIL_MAILER=smtp
            MAIL_HOST=smtp.gmail.com
            MAIL_PORT=587
            MAIL_USERNAME=devthanhtu283@gmail.com 
            MAIL_PASSWORD=whvwkmdkogvqtcpz            -> mat khau ung dung
            MAIL_ENCRYPTION=tls
            MAIL_FROM_ADDRESS=devthanhtu283@gmail.com
            MAIL_FROM_NAME="${APP_NAME}"
        */
        Mail::raw('Day la body', function ($message) {
            $message->to('cquangly@gmail.com') // email cua nguoi nhan
                ->subject('Day la subject/ tieu de cua email');
        });

        return 'Đã gửi email thành công!';
    }
}
