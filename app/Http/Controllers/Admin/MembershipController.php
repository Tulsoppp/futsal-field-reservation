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

        $duration = 1;
        if ($user->membership_type == 'Pro Team') $duration = 2;
        if ($user->membership_type == 'Elite League') $duration = 6;

        $user->membership_expires_at = Carbon::now()->addMonths($duration);
        $user->save();

        return redirect()->back()->with('success', 'Membership disetujui.');
    }

    public function tolak(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->membership_status = 'rejected';
        $user->save();

        return redirect()->back()->with('success', 'Membership Ditolak.');
    }
}
