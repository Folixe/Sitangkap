<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tangkapan;
use App\Models\DetailTangkapan;
use App\Models\Notifikasi;
use App\Models\AdminLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CatchController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Tangkapan::with(['nelayan.profil.kelompokNelayan', 'details.jenisIkan', 'fotos']);

        if ($search) {
            $query->whereHas('nelayan', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $catches = $query->orderBy('tanggal_penangkapan', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.catches.index', compact('catches', 'search', 'status'));
    }

    public function show($id)
    {
        $catch = Tangkapan::with(['nelayan.profil.kelompokNelayan', 'details.jenisIkan', 'fotos'])->findOrFail($id);

        // Make weights and numbers formatted nicely
        $totalWeight = $catch->details->sum('berat_kg');
        $totalEkor = $catch->details->sum('jumlah_ekor');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $catch->id,
                'nelayan_nama' => $catch->nelayan->nama_lengkap,
                'kelompok' => $catch->nelayan->profil->kelompokNelayan->nama_kelompok ?? 'Tanpa Kelompok',
                'tanggal' => $catch->tanggal_penangkapan->format('d-m-Y'),
                'lokasi' => $catch->lokasi_nama ?? '-',
                'lat' => $catch->latitude,
                'lng' => $catch->longitude,
                'cuaca' => $catch->kondisi_cuaca ?? '-',
                'keterangan' => $catch->keterangan ?? '-',
                'status' => $catch->status,
                'catatan_verifikasi' => $catch->nelayan->profil->catatan_verifikasi ?? '',
                'details' => $catch->details->map(function($d) {
                    return [
                        'nama_ikan' => $d->jenisIkan->nama_lokal,
                        'berat' => $d->berat_kg,
                        'jumlah' => $d->jumlah_ekor ?? '-',
                        'keterangan' => $d->keterangan ?? '-'
                    ];
                }),
                'fotos' => $catch->fotos->map(function($f) {
                    return [
                        'url' => str_starts_with($f->file_path, 'http') ? $f->file_path : asset('storage/' . $f->file_path),
                        'name' => $f->file_name
                    ];
                }),
                'total_weight' => $totalWeight,
                'total_ekor' => $totalEkor
            ]
        ]);
    }

    public function verify(Request $request, $id)
    {
        $catch = Tangkapan::findOrFail($id);

        $validated = $request->validate([
            'status' => ['required', 'in:verified,rejected'],
            'keterangan' => ['nullable', 'string'],
        ]);

        DB::transaction(function() use ($catch, $validated, $request) {
            $dataSebelum = $catch->toArray();

            $catch->update([
                'status' => $validated['status'],
                'admin_id' => Auth::guard('admin')->id(),
                'verified_at' => now(),
            ]);

            // Notify fisherman
            $statusString = $validated['status'] === 'verified' ? 'DISETUJUI' : 'DITOLAK';
            $judul = 'Verifikasi Laporan Tangkapan: ' . $statusString;
            $pesan = $validated['status'] === 'verified'
                ? 'Laporan hasil tangkapan Anda tanggal ' . $catch->tanggal_penangkapan->format('d-m-Y') . ' telah disetujui.'
                : 'Laporan hasil tangkapan Anda tanggal ' . $catch->tanggal_penangkapan->format('d-m-Y') . ' ditolak. Catatan: ' . ($validated['keterangan'] ?? 'Tidak ditentukan.');

            Notifikasi::create([
                'nelayan_id' => $catch->nelayan_id,
                'judul' => $judul,
                'pesan' => $pesan,
                'tipe' => $validated['status'] === 'verified' ? 'success' : 'danger',
                'is_read' => false,
            ]);

            AdminLog::create([
                'admin_id' => Auth::guard('admin')->id(),
                'aksi' => 'Verifikasi laporan tangkapan (' . $validated['status'] . ')',
                'tabel_target' => 'tangkapan',
                'id_target' => $catch->id,
                'data_sebelum' => $dataSebelum,
                'data_sesudah' => $catch->toArray(),
                'ip_address' => $request->ip(),
            ]);
        });

        $msg = $validated['status'] === 'verified'
            ? 'Laporan tangkapan berhasil disetujui.'
            : 'Laporan tangkapan ditolak.';

        return redirect()->route('admin.catches.index')->with('success', $msg);
    }
}
