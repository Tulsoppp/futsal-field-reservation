<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

class MembershipController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'membership_type' => 'required|in:149000,299000,449000',
            'bukti_membership' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = User::findOrFail(Auth::id());

        $type = 'Basic';
        if ($validated['membership_type'] == '299000') $type = 'Pro Team';
        if ($validated['membership_type'] == '449000') $type = 'Elite League';

        $path = $request->file('bukti_membership')->store('bukti_membership', 'public');

        $user->membership_type = $type;
        $user->membership_status = 'pending';
        $user->membership_proof = $path;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Pendaftaran membership berhasil, mohon tunggu konfirmasi admin.'
        ]);
    }

    public function profile()
    {
        $user = Auth::user();
        return view('pages.user.profile', compact('user'));
    }
}
