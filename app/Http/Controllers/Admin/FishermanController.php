<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nelayan;
use App\Models\ProfilNelayan;
use App\Models\Kecamatan;
use App\Models\Desa;
use App\Models\KelompokNelayan;
use App\Models\Notifikasi;
use App\Models\AdminLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FishermanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Nelayan::with(['profil.desa.kecamatan', 'profil.kelompokNelayan']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('no_telepon', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->whereHas('profil', function($q) use ($status) {
                $q->where('status_verifikasi', $status);
            });
        }

        $fishermen = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        $kecamatans = Kecamatan::orderBy('nama')->get();
        $desas = Desa::orderBy('nama')->get();
        $kelompoks = KelompokNelayan::where('is_active', true)->orderBy('nama_kelompok')->get();

        return view('admin.fishermen.index', compact('fishermen', 'kecamatans', 'desas', 'kelompoks', 'search', 'status'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:nelayan,email'],
            'password' => ['required', 'string', 'min:6'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
            'tempat_lahir' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date'],
            'kelompok_id' => ['nullable', 'exists:kelompok_nelayan,id'],
            'desa_id' => ['required', 'exists:desa,id'],
            'rt' => ['required', 'string', 'max:10'],
            'rw' => ['required', 'string', 'max:10'],
            'jenis_kapal' => ['required', 'string'],
            'nama_kapal' => ['nullable', 'string', 'max:255'],
            'no_registrasi_kapal' => ['nullable', 'string', 'max:255'],
            'jenis_tangkapan_utama' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function() use ($validated, $request) {
            $nelayan = Nelayan::create([
                'nama_lengkap' => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'no_telepon' => $validated['no_telepon'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'status_akun' => 'active',
            ]);

            ProfilNelayan::create([
                'nelayan_id' => $nelayan->id,
                'kelompok_id' => $validated['kelompok_id'],
                'desa_id' => $validated['desa_id'],
                'rt' => $validated['rt'],
                'rw' => $validated['rw'],
                'jenis_kapal' => $validated['jenis_kapal'],
                'nama_kapal' => $validated['nama_kapal'],
                'no_registrasi_kapal' => $validated['no_registrasi_kapal'],
                'jenis_tangkapan_utama' => $validated['jenis_tangkapan_utama'],
                'foto_profil' => 'https://ui-avatars.com/api/?name=' . urlencode($nelayan->nama_lengkap) . '&background=random&color=fff',
                'status_verifikasi' => 'pending',
            ]);

            AdminLog::create([
                'admin_id' => Auth::guard('admin')->id(),
                'aksi' => 'Menambahkan akun nelayan baru',
                'tabel_target' => 'nelayan',
                'id_target' => $nelayan->id,
                'data_sesudah' => $nelayan->toArray(),
                'ip_address' => $request->ip(),
            ]);
        });

        return redirect()->route('admin.fishermen.index')->with('success', 'Akun nelayan berhasil ditambahkan dan menunggu verifikasi.');
    }

    public function update(Request $request, $id)
    {
        $nelayan = Nelayan::findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:nelayan,email,' . $id],
            'password' => ['nullable', 'string', 'min:6'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
            'tempat_lahir' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date'],
            'kelompok_id' => ['nullable', 'exists:kelompok_nelayan,id'],
            'desa_id' => ['required', 'exists:desa,id'],
            'rt' => ['required', 'string', 'max:10'],
            'rw' => ['required', 'string', 'max:10'],
            'jenis_kapal' => ['required', 'string'],
            'nama_kapal' => ['nullable', 'string', 'max:255'],
            'no_registrasi_kapal' => ['nullable', 'string', 'max:255'],
            'jenis_tangkapan_utama' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function() use ($nelayan, $validated, $request) {
            $dataSebelum = $nelayan->load('profil')->toArray();

            $nelayan->update([
                'nama_lengkap' => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'no_telepon' => $validated['no_telepon'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
            ]);

            if ($validated['password']) {
                $nelayan->update(['password' => Hash::make($validated['password'])]);
            }

            $profil = ProfilNelayan::where('nelayan_id', $nelayan->id)->first();
            $profil->update([
                'kelompok_id' => $validated['kelompok_id'],
                'desa_id' => $validated['desa_id'],
                'rt' => $validated['rt'],
                'rw' => $validated['rw'],
                'jenis_kapal' => $validated['jenis_kapal'],
                'nama_kapal' => $validated['nama_kapal'],
                'no_registrasi_kapal' => $validated['no_registrasi_kapal'],
                'jenis_tangkapan_utama' => $validated['jenis_tangkapan_utama'],
            ]);

            AdminLog::create([
                'admin_id' => Auth::guard('admin')->id(),
                'aksi' => 'Mengubah data nelayan',
                'tabel_target' => 'nelayan',
                'id_target' => $nelayan->id,
                'data_sebelum' => $dataSebelum,
                'data_sesudah' => $nelayan->load('profil')->toArray(),
                'ip_address' => $request->ip(),
            ]);
        });

        return redirect()->route('admin.fishermen.index')->with('success', 'Data nelayan berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $nelayan = Nelayan::findOrFail($id);

        DB::transaction(function() use ($nelayan, $request) {
            $dataSebelum = $nelayan->load('profil')->toArray();

            $nelayan->delete();

            AdminLog::create([
                'admin_id' => Auth::guard('admin')->id(),
                'aksi' => 'Menghapus akun nelayan',
                'tabel_target' => 'nelayan',
                'id_target' => $nelayan->id,
                'data_sebelum' => $dataSebelum,
                'ip_address' => $request->ip(),
            ]);
        });

        return redirect()->route('admin.fishermen.index')->with('success', 'Akun nelayan berhasil dihapus.');
    }

    public function verify(Request $request, $id)
    {
        $profil = ProfilNelayan::where('nelayan_id', $id)->firstOrFail();
        $nelayan = Nelayan::findOrFail($id);

        $validated = $request->validate([
            'status_verifikasi' => ['required', 'in:verified,rejected'],
            'catatan_verifikasi' => ['nullable', 'string'],
        ]);

        DB::transaction(function() use ($profil, $nelayan, $validated, $request) {
            $dataSebelum = $profil->toArray();

            $profil->update([
                'status_verifikasi' => $validated['status_verifikasi'],
                'catatan_verifikasi' => $validated['catatan_verifikasi'],
                'admin_id' => Auth::guard('admin')->id(),
                'verified_at' => now(),
            ]);

            // Send notification to Nelayan
            $statusString = $validated['status_verifikasi'] === 'verified' ? 'DITERIMA' : 'DITOLAK';
            $judul = 'Status Verifikasi Akun: ' . $statusString;
            $pesan = $validated['status_verifikasi'] === 'verified' 
                ? 'Selamat! Akun Anda telah diverifikasi oleh Dinas Perikanan. Anda sekarang dapat mencatat hasil tangkapan.'
                : 'Maaf, pengajuan akun Anda ditolak. Alasan: ' . ($validated['catatan_verifikasi'] ?? 'Tidak ditentukan.');

            Notifikasi::create([
                'nelayan_id' => $nelayan->id,
                'judul' => $judul,
                'pesan' => $pesan,
                'tipe' => $validated['status_verifikasi'] === 'verified' ? 'success' : 'danger',
                'is_read' => false,
            ]);

            AdminLog::create([
                'admin_id' => Auth::guard('admin')->id(),
                'aksi' => 'Verifikasi akun nelayan (' . $validated['status_verifikasi'] . ')',
                'tabel_target' => 'profil_nelayan',
                'id_target' => $profil->id,
                'data_sebelum' => $dataSebelum,
                'data_sesudah' => $profil->toArray(),
                'ip_address' => $request->ip(),
            ]);
        });

        $msg = $validated['status_verifikasi'] === 'verified' 
            ? 'Akun nelayan berhasil diverifikasi (Disetujui).'
            : 'Akun nelayan ditolak dengan catatan.';

        return redirect()->route('admin.fishermen.index')->with('success', $msg);
    }
}
