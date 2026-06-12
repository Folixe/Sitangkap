<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JenisIkan;
use App\Models\AdminLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FishTypeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = JenisIkan::with('admin');

        if ($search) {
            $query->where('nama_lokal', 'like', "%{$search}%")
                  ->orWhere('nama_ilmiah', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%");
        }

        $fishTypes = $query->orderBy('nama_lokal', 'asc')->paginate(10)->withQueryString();

        return view('admin.fish-types.index', compact('fishTypes', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lokal' => ['required', 'string', 'max:255'],
            'nama_ilmiah' => ['nullable', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'is_active' => ['required', 'boolean'],
        ]);

        $fishType = DB::transaction(function() use ($validated, $request) {
            $fishType = JenisIkan::create([
                'nama_lokal' => $validated['nama_lokal'],
                'nama_ilmiah' => $validated['nama_ilmiah'],
                'kategori' => $validated['kategori'],
                'is_active' => $validated['is_active'],
                'admin_id' => Auth::guard('admin')->id(),
            ]);

            AdminLog::create([
                'admin_id' => Auth::guard('admin')->id(),
                'aksi' => 'Menambahkan master jenis ikan baru',
                'tabel_target' => 'jenis_ikan',
                'id_target' => $fishType->id,
                'data_sesudah' => $fishType->toArray(),
                'ip_address' => $request->ip(),
            ]);

            return $fishType;
        });

        return redirect()->route('admin.fish-types.index')->with('success', 'Jenis ikan "' . $fishType->nama_lokal . '" berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $fishType = JenisIkan::findOrFail($id);

        $validated = $request->validate([
            'nama_lokal' => ['required', 'string', 'max:255'],
            'nama_ilmiah' => ['nullable', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'is_active' => ['required', 'boolean'],
        ]);

        DB::transaction(function() use ($fishType, $validated, $request) {
            $dataSebelum = $fishType->toArray();

            $fishType->update([
                'nama_lokal' => $validated['nama_lokal'],
                'nama_ilmiah' => $validated['nama_ilmiah'],
                'kategori' => $validated['kategori'],
                'is_active' => $validated['is_active'],
            ]);

            AdminLog::create([
                'admin_id' => Auth::guard('admin')->id(),
                'aksi' => 'Mengubah master jenis ikan',
                'tabel_target' => 'jenis_ikan',
                'id_target' => $fishType->id,
                'data_sebelum' => $dataSebelum,
                'data_sesudah' => $fishType->toArray(),
                'ip_address' => $request->ip(),
            ]);
        });

        return redirect()->route('admin.fish-types.index')->with('success', 'Master jenis ikan berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $fishType = JenisIkan::findOrFail($id);

        // Check if referenced in catches
        $referenced = DB::table('detail_tangkapan')->where('jenis_ikan_id', $id)->exists();

        if ($referenced) {
            return redirect()->route('admin.fish-types.index')->with('error', 'Tidak dapat menghapus jenis ikan ini karena telah direferensikan dalam laporan tangkapan nelayan.');
        }

        DB::transaction(function() use ($fishType, $request) {
            $dataSebelum = $fishType->toArray();

            $fishType->delete();

            AdminLog::create([
                'admin_id' => Auth::guard('admin')->id(),
                'aksi' => 'Menghapus master jenis ikan',
                'tabel_target' => 'jenis_ikan',
                'id_target' => $fishType->id,
                'data_sebelum' => $dataSebelum,
                'ip_address' => $request->ip(),
            ]);
        });

        return redirect()->route('admin.fish-types.index')->with('success', 'Master jenis ikan berhasil dihapus.');
    }
}
