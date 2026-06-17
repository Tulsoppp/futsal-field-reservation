<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;

class MembershipController extends Controller
{
    public function index()
    {
        $memberships = User::whereIn('membership_status', ['pending', 'active'])
            ->latest()
            ->paginate(10);
        return view('pages.admin.membership', compact('memberships'));
    }

    public function terima(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->membership_status = 'active';
        $user->status_member = 1;

        // Membership selalu 3 bulan
        $user->membership_expires_at = Carbon::now()->addMonths(3);
        $user->membership_free_hour_used = false; // Reset benefit free 1 jam
        $user->membership_last_booking_at = now(); // Set agar countdown 3 bulan dimulai dari sekarang
        $user->save();

        return redirect()->back()->with('success', 'Membership disetujui (3 bulan aktif, free 1 jam).');
    }

    public function tolak(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->membership_status = 'rejected';
        $user->save();

        return redirect()->back()->with('success', 'Membership Ditolak.');
    }
}
