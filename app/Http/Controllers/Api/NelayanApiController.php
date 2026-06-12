<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Nelayan;
use App\Models\ProfilNelayan;
use App\Models\Tangkapan;
use App\Models\DetailTangkapan;
use App\Models\FotoTangkapan;
use App\Models\Kecamatan;
use App\Models\Desa;
use App\Models\KelompokNelayan;
use App\Models\JenisIkan;

class NelayanApiController extends Controller
{
    // Helper to generate a signed token for a nelayan
    private function generateToken(Nelayan $nelayan)
    {
        $id = $nelayan->id;
        $signature = hash_hmac('sha256', $id, config('app.key') ?? 'fallback-key-123456');
        return $id . '|' . $signature;
    }

    // Helper to get authenticated nelayan by simple token (id) with signature verification
    private function getNelayanByToken(Request $request)
    {
        $token = $request->header('Authorization') ?: $request->input('token');
        if (!$token) {
            return null;
        }
        if (str_starts_with($token, 'Bearer ')) {
            $token = substr($token, 7);
        }
        
        $parts = explode('|', $token);
        if (count($parts) !== 2) {
            return null;
        }
        
        [$id, $signature] = $parts;
        $expectedSignature = hash_hmac('sha256', $id, config('app.key') ?? 'fallback-key-123456');
        
        if (!hash_equals($expectedSignature, $signature)) {
            return null;
        }
        
        return Nelayan::find($id);
    }

    public function referenceData()
    {
        return response()->json([
            'status' => 'success',
            'kecamatan' => Kecamatan::orderBy('nama')->get(),
            'desa' => Desa::orderBy('nama')->get(),
            'kelompok_nelayan' => KelompokNelayan::orderBy('nama_kelompok')->get(),
            'jenis_ikan' => JenisIkan::orderBy('nama_lokal')->get(),
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:nelayan,email',
            'password' => 'required|string|min:6',
            'no_telepon' => 'nullable|string|max:20',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'kelompok_id' => 'required|exists:kelompok_nelayan,id',
            'desa_id' => 'required|exists:desa,id',
            'rt' => 'required|string|max:3',
            'rw' => 'required|string|max:3',
            'jenis_kapal' => 'required|string|max:100',
            'nama_kapal' => 'nullable|string|max:100',
            'no_registrasi_kapal' => 'nullable|string|max:100',
            'jenis_tangkapan_utama' => 'required|string|max:100',
        ]);

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
            'status_verifikasi' => 'pending',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pendaftaran berhasil. Silakan tunggu verifikasi admin.',
            'nelayan' => $nelayan->load('profil.kelompokNelayan', 'profil.desa.kecamatan'),
        ]);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $nelayan = Nelayan::where('email', $validated['email'])->first();

        if (!$nelayan || !Hash::check($validated['password'], $nelayan->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email atau kata sandi salah.',
            ], 401);
        }

        $nelayan->update(['last_login_at' => now()]);

        return response()->json([
            'status' => 'success',
            'token' => $this->generateToken($nelayan),
            'nelayan' => $nelayan->load('profil.kelompokNelayan', 'profil.desa.kecamatan'),
        ]);
    }

    public function profile(Request $request)
    {
        $nelayan = $this->getNelayanByToken($request);

        if (!$nelayan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized.',
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'nelayan' => $nelayan->load('profil.kelompokNelayan', 'profil.desa.kecamatan'),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $nelayan = $this->getNelayanByToken($request);

        if (!$nelayan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized.',
            ], 401);
        }

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_telepon' => 'nullable|string|max:20',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kapal' => 'required|string|max:100',
            'nama_kapal' => 'nullable|string|max:100',
            'jenis_tangkapan_utama' => 'required|string|max:100',
        ]);

        $nelayan->update([
            'nama_lengkap' => $validated['nama_lengkap'],
            'no_telepon' => $validated['no_telepon'],
            'tempat_lahir' => $validated['tempat_lahir'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
        ]);

        $nelayan->profil->update([
            'jenis_kapal' => $validated['jenis_kapal'],
            'nama_kapal' => $validated['nama_kapal'],
            'jenis_tangkapan_utama' => $validated['jenis_tangkapan_utama'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Profil berhasil diperbarui.',
            'nelayan' => $nelayan->load('profil.kelompokNelayan', 'profil.desa.kecamatan'),
        ]);
    }

    public function catches(Request $request)
    {
        $nelayan = $this->getNelayanByToken($request);

        if (!$nelayan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized.',
            ], 401);
        }

        $catches = Tangkapan::where('nelayan_id', $nelayan->id)
            ->with(['details.jenisIkan', 'fotos'])
            ->orderBy('tanggal_penangkapan', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'catches' => $catches,
        ]);
    }

    public function storeCatch(Request $request)
    {
        $nelayan = $this->getNelayanByToken($request);

        if (!$nelayan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized.',
            ], 401);
        }

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jenis_ikan_id' => 'required|exists:jenis_ikan,id',
            'berat' => 'required|numeric|min:0.1',
            'foto' => 'required|image|max:10240', // Max 10MB
        ]);

        // Create Tangkapan
        $tangkapan = Tangkapan::create([
            'nelayan_id' => $nelayan->id,
            'tanggal_penangkapan' => $validated['tanggal'],
            'status' => 'pending', // Awaiting admin verification
        ]);

        // Get Jenis Ikan Name
        $jenisIkan = JenisIkan::find($validated['jenis_ikan_id']);
        $namaIkan = $jenisIkan ? $jenisIkan->nama_lokal : null;

        // Create DetailTangkapan
        DetailTangkapan::create([
            'tangkapan_id' => $tangkapan->id,
            'jenis_ikan_id' => $validated['jenis_ikan_id'],
            'nama_ikan' => $namaIkan,
            'berat_kg' => $validated['berat'],
        ]);

        // Save Foto
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $path = $file->store('tangkapan', 'public');
            
            FotoTangkapan::create([
                'tangkapan_id' => $tangkapan->id,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'ukuran_byte' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'is_primary' => true,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Laporan tangkapan berhasil dikirim dan menunggu verifikasi admin.',
        ]);
    }
}
