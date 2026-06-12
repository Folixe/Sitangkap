<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminLog;
use App\Models\Admin;

class SettingController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.settings.index', compact('admin'));
    }

    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:admins,email,' . $admin->id],
            'no_telepon' => ['nullable', 'string', 'max:20'],
            'old_password' => ['nullable', 'required_with:password'],
            'password' => ['nullable', 'confirmed', 'min:6'],
        ]);

        // Check password change
        if ($request->filled('password')) {
            if (!Hash::check($request->old_password, $admin->password)) {
                return back()->withErrors([
                    'old_password' => 'Kata sandi lama yang Anda masukkan tidak sesuai.',
                ]);
            }
        }

        $dataSebelum = $admin->toArray();

        $admin->update([
            'nama_lengkap' => $validated['nama_lengkap'],
            'email' => $validated['email'],
            'no_telepon' => $validated['no_telepon'],
        ]);

        if ($request->filled('password')) {
            $admin->update([
                'password' => Hash::make($request->password),
            ]);
        }

        AdminLog::create([
            'admin_id' => $admin->id,
            'aksi' => 'Memperbarui profil administrator',
            'tabel_target' => 'admins',
            'id_target' => $admin->id,
            'data_sebelum' => $dataSebelum,
            'data_sesudah' => $admin->toArray(),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.settings.index')->with('success', 'Profil Anda berhasil diperbarui.');
    }
}
