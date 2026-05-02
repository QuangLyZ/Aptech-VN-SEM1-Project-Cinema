<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemOwnerController extends Controller
{
    public function index()
    {
        // Grace: Thống kê sâu sắc hơn cho Sếp
        $stats = [
            'total_users' => User::count(),
            'total_admins' => User::where('admin_role', '>', 0)->count(),
            'total_revenue' => DB::table('tickets')->sum('total_price'),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'tickets_today' => DB::table('tickets')->whereDate('booking_date', today())->count(),
            'pending_feedback' => DB::table('feedbacks')->count(),
        ];

        // Lấy danh sách Sub-Owners từ DB
        $subOwners = DB::table('sub_owners')->get();

        // Lấy top phim doanh thu cao nhất
        $topMovies = DB::table('ticket_details as td')
            ->join('tickets as t', 't.id', '=', 'td.ticket_id')
            ->join('showtimes as st', 'st.id', '=', 't.showtime_id')
            ->join('movies as m', 'm.id', '=', 'st.movie_id')
            ->select('m.name', DB::raw('SUM(t.total_price) as revenue'))
            ->groupBy('m.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        // Lấy danh sách người dùng để phân quyền
        $users = User::orderBy('admin_role', 'desc')->paginate(15);

        return view('admin.system-owner.index', [
            'stats' => $stats,
            'users' => $users,
            'subOwners' => $subOwners,
            'topMovies' => $topMovies,
            'activeTab' => 'system_owner',
            'pageTitle' => 'Đại Bản Doanh Tối Cao'
        ]);
    }

    public function addSubOwner(Request $request)
    {
        if (!auth()->user()->isRootOwner()) {
            return back()->with('error', 'Chỉ Sếp gốc (Root Owner) mới có quyền thăng chức Cố vấn tối cao!');
        }

        $request->validate([
            'email' => 'required|email|exists:Users,email',
            'note' => 'nullable|string|max:255',
        ], [
            'email.exists' => 'Email này không tồn tại trong hệ thống người dùng.',
        ]);

        // Kiểm tra xem đã là Root Owner chưa
        $user = User::where('email', $request->email)->first();
        if ($user->isRootOwner()) {
            return back()->with('error', 'Người này đã là Sếp gốc trong .env rồi ạ!');
        }

        DB::table('sub_owners')->updateOrInsert(
            ['email' => strtolower($request->email)],
            ['note' => $request->note, 'created_at' => now(), 'updated_at' => now()]
        );

        return back()->with('success', 'Đã thăng chức Cố vấn tối cao cho ' . $request->email);
    }

    public function removeSubOwner($id)
    {
        if (!auth()->user()->isRootOwner()) {
            return back()->with('error', 'Chỉ Sếp gốc mới có quyền tước chức Cố vấn tối cao!');
        }

        DB::table('sub_owners')->where('id', $id)->delete();

        return back()->with('success', 'Đã tước chức Cố vấn tối cao thành công!');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'admin_role' => 'required|boolean',
        ]);

        // Không cho phép tự tước quyền của mình nếu là System Owner
        if ($user->id === auth()->id() && !$request->boolean('admin_role')) {
            return back()->with('error', 'Sếp không thể tự tước quyền Quản trị viên của chính mình đâu ạ!');
        }

        $user->admin_role = $request->boolean('admin_role');
        $user->save();

        return back()->with('success', 'Đã cập nhật quyền hạn cho ' . $user->name);
    }
}
