<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Movie;
use App\Models\Showtime;
use App\Models\Ticket;
use App\Models\Feedback;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SystemOwnerDummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo thêm User nếu chưa có nhiều
        if (User::count() < 10) {
            User::factory()->count(20)->create();
        }
        $users = User::where('admin_role', false)->get();
        
        // 2. Lấy danh sách showtimes hiện có
        $showtimes = Showtime::all();
        if ($showtimes->isEmpty()) {
            $this->command->warn('Không có showtimes nào để seed ticket!');
            return;
        }

        // 3. Tạo Ticket (Doanh thu)
        foreach ($users->random(min(15, $users->count())) as $user) {
            $numTickets = rand(1, 3);
            for ($i = 0; $i < $numTickets; $i++) {
                $st = $showtimes->random();
                $price = rand(85000, 150000);
                
                $ticket = Ticket::create([
                    'user_id' => $user->id,
                    'showtime_id' => $st->id,
                    'fullname' => $user->fullname ?? $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '0901234567',
                    'total_price' => $price,
                    'booking_date' => Carbon::now()->subDays(rand(0, 5)),
                    'ticket_code' => 'CB' . strtoupper(Str::random(8)),
                    'status' => 'paid',
                ]);

                // Lấy 1 ghế ngẫu nhiên trong phòng của showtime
                $seat = DB::table('seats')->where('room_id', $st->room_id)->inRandomOrder()->first();
                
                if ($seat) {
                    DB::table('ticket_details')->insert([
                        'ticket_id' => $ticket->id,
                        'seat_id' => $seat->id,
                        'price_at_booking' => $price,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 4. Tạo Feedback
        $feedbackTitles = [
            'Rạp hơi lạnh',
            'Âm thanh tuyệt vời',
            'Ghế ngồi thoải mái',
            'Bắp rang bơ hơi mặn',
            'Nhân viên nhiệt tình',
        ];

        foreach ($users->random(min(10, $users->count())) as $user) {
            Feedback::create([
                'user_id' => $user->id,
                'title' => $feedbackTitles[array_rand($feedbackTitles)],
                'context' => 'Đây là ý kiến phản hồi từ hệ thống giả lập để sếp kiểm tra giao diện.',
            ]);
        }
        
        $this->command->info('✅ Đã đổ dữ liệu ảo cho System Owner Dashboard thành công!');
    }
}
