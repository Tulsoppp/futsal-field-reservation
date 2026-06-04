<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PelangganController extends Controller
{
    public function index()
    {
        $pelanggan = User::query()
            ->where(function ($query) {
                $query->whereNull('role')
                    ->orWhere('role', '!=', 'admin');
            })
            ->orderBy('nama')
            ->paginate(10);

        return view('pages.admin.pelanggan', compact('pelanggan'));
    }

    public function create()
    {
        return view('pages.admin.pelanggan-form', [
            'pelanggan' => null,
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'status_member' => 'required|boolean',
            'membership_type' => 'nullable|string|in:Basic,Pro Team,Elite League',
            'password' => 'required|string|min:6',
        ]);

        $userData = [
            'nama' => $validated['nama'],
            'no_hp' => $validated['no_hp'],
            'email' => $validated['email'],
            'status_member' => $validated['status_member'],
            'role' => 'user',
            'password' => Hash::make($validated['password']),
        ];

        // Jika status member aktif, set data membership
        if ($validated['status_member']) {
            $membershipType = $validated['membership_type'] ?? 'Basic';
            $duration = $this->getMembershipDuration($membershipType);

            $userData['membership_type'] = $membershipType;
            $userData['membership_status'] = 'active';
            $userData['membership_expires_at'] = Carbon::now()->addMonths($duration);
            $userData['membership_proof'] = 'cash'; // Tambahkan flag cash
        } else {
            $userData['membership_status'] = null;
            $userData['membership_type'] = null;
            $userData['membership_expires_at'] = null;
            $userData['membership_proof'] = null;
        }

        User::create($userData);

        return redirect()->route('admin.pelanggan')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $pelanggan = User::findOrFail($id);

        return view('pages.admin.pelanggan-form', [
            'pelanggan' => $pelanggan,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, $id)
    {
        $pelanggan = User::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email,' . $pelanggan->id,
            'status_member' => 'required|boolean',
            'membership_type' => 'nullable|string|in:Basic,Pro Team,Elite League',
            'password' => 'nullable|string|min:6',
        ]);

        $pelanggan->nama = $validated['nama'];
        $pelanggan->no_hp = $validated['no_hp'];
        $pelanggan->email = $validated['email'];
        $pelanggan->status_member = $validated['status_member'];

        // Update membership fields
        if ($validated['status_member']) {
            $membershipType = $validated['membership_type'] ?? 'Basic';

            // Jika tipe paket berubah atau belum pernah jadi member, reset expiry
            if ($pelanggan->membership_type !== $membershipType || $pelanggan->membership_status !== 'active') {
                $duration = $this->getMembershipDuration($membershipType);
                $pelanggan->membership_expires_at = Carbon::now()->addMonths($duration);
            }

            $pelanggan->membership_type = $membershipType;
            $pelanggan->membership_status = 'active';
            
            // Set as cash if no existing proof
            if (empty($pelanggan->membership_proof)) {
                $pelanggan->membership_proof = 'cash';
            }
        } else {
            // Reset semua data membership jika diubah ke Non Member
            $pelanggan->membership_type = null;
            $pelanggan->membership_status = null;
            $pelanggan->membership_expires_at = null;
            $pelanggan->membership_proof = null;
        }

        if (!empty($validated['password'])) {
            $pelanggan->password = Hash::make($validated['password']);
        }

        $pelanggan->save();

        return redirect()->route('admin.pelanggan')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pelanggan = User::findOrFail($id);
        $pelanggan->delete();

        return redirect()->route('admin.pelanggan')->with('success', 'Pelanggan berhasil dihapus.');
    }

    /**
     * Hitung durasi membership berdasarkan tipe paket
     */
    private function getMembershipDuration(string $type): int
    {
        return match ($type) {
            'Pro Team' => 2,
            'Elite League' => 6,
            default => 1, // Basic = 1 bulan
        };
    }
}
