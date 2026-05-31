<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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
            ->get();

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
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'nama' => $validated['nama'],
            'no_hp' => $validated['no_hp'],
            'email' => $validated['email'],
            'status_member' => $validated['status_member'],
            'membership_status' => $validated['status_member'] ? 'active' : 'inactive',
            'role' => 'user',
            'password' => Hash::make($validated['password']),
        ]);

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
            'password' => 'nullable|string|min:6',
        ]);

        $pelanggan->nama = $validated['nama'];
        $pelanggan->no_hp = $validated['no_hp'];
        $pelanggan->email = $validated['email'];
        $pelanggan->status_member = $validated['status_member'];
        $pelanggan->membership_status = $validated['status_member'] ? 'active' : 'inactive';

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
}
