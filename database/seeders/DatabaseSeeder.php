<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kecamatan;
use App\Models\Desa;
use App\Models\KelompokNelayan;
use App\Models\Admin;
use App\Models\Nelayan;
use App\Models\ProfilNelayan;
use App\Models\JenisIkan;
use App\Models\Tangkapan;
use App\Models\DetailTangkapan;
use App\Models\FotoTangkapan;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Admin
        $admin = Admin::create([
            'nama_lengkap' => 'Admin Dinas Kelautan',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'no_telepon' => '081234567890',
            'level' => 'superadmin',
            'is_active' => true,
        ]);

        // 2. Seed Kecamatan
        $kecamatans = [
            ['nama' => 'Cilacap Selatan', 'kode' => '33.01.01'],
            ['nama' => 'Adipala', 'kode' => '33.01.14'],
            ['nama' => 'Kesugihan', 'kode' => '33.01.13'],
            ['nama' => 'Kampung Laut', 'kode' => '33.01.24'],
        ];

        $kecamatanModels = [];
        foreach ($kecamatans as $kec) {
            $kecamatanModels[$kec['nama']] = Kecamatan::create($kec);
        }

        // 3. Seed Desa
        $desas = [
            'Cilacap Selatan' => [
                ['nama' => 'Tambakreja', 'kode' => '33.01.01.1001'],
                ['nama' => 'Tegalkamulyan', 'kode' => '33.01.01.1004'],
            ],
            'Adipala' => [
                ['nama' => 'Adipala', 'kode' => '33.01.14.2001'],
                ['nama' => 'Bunton', 'kode' => '33.01.14.2003'],
            ],
            'Kesugihan' => [
                ['nama' => 'Kesugihan', 'kode' => '33.01.13.2001'],
                ['nama' => 'Kuripan', 'kode' => '33.01.13.2004'],
            ],
            'Kampung Laut' => [
                ['nama' => 'Ujungalang', 'kode' => '33.01.24.2001'],
                ['nama' => 'Klaces', 'kode' => '33.01.24.2002'],
            ],
        ];

        $desaModels = [];
        foreach ($desas as $kecName => $listDesa) {
            $kecModel = $kecamatanModels[$kecName];
            foreach ($listDesa as $d) {
                $d['kecamatan_id'] = $kecModel->id;
                $desaModels[$d['nama']] = Desa::create($d);
            }
        }

        // 4. Seed Kelompok Nelayan
        $kelompoks = [
            ['nama_kelompok' => 'Mina Makmur', 'kode_kelompok' => 'KP-CS-001', 'nama_ketua' => 'Sutarno', 'no_telepon' => '085123456789', 'desa_name' => 'Tambakreja'],
            ['nama_kelompok' => 'Samudra Jaya', 'kode_kelompok' => 'KP-AD-002', 'nama_ketua' => 'Kasiran', 'no_telepon' => '085234567890', 'desa_name' => 'Adipala'],
            ['nama_kelompok' => 'Bakti Laut', 'kode_kelompok' => 'KP-KS-003', 'nama_ketua' => 'Parmin', 'no_telepon' => '085345678901', 'desa_name' => 'Kesugihan'],
        ];

        $kelompokModels = [];
        foreach ($kelompoks as $k) {
            $desaModel = $desaModels[$k['desa_name']];
            $k['desa_id'] = $desaModel->id;
            unset($k['desa_name']);
            $kelompokModels[$k['nama_kelompok']] = KelompokNelayan::create($k);
        }

        // 5. Seed Jenis Ikan
        $jenisIkans = [
            ['nama_lokal' => 'Tuna', 'nama_ilmiah' => 'Thunnus', 'kategori' => 'Pelagis', 'is_active' => true],
            ['nama_lokal' => 'Cakalang', 'nama_ilmiah' => 'Katsuwonus pelamis', 'kategori' => 'Pelagis', 'is_active' => true],
            ['nama_lokal' => 'Tongkol', 'nama_ilmiah' => 'Euthynnus affinis', 'kategori' => 'Pelagis', 'is_active' => true],
            ['nama_lokal' => 'Layur', 'nama_ilmiah' => 'Trichiurus lepturus', 'kategori' => 'Demersal', 'is_active' => true],
            ['nama_lokal' => 'Kembung', 'nama_ilmiah' => 'Rastrelliger', 'kategori' => 'Pelagis', 'is_active' => true],
            ['nama_lokal' => 'Kakap Merah', 'nama_ilmiah' => 'Lutjanus campechanus', 'kategori' => 'Demersal', 'is_active' => true],
            ['nama_lokal' => 'Kerapu', 'nama_ilmiah' => 'Epinephelinae', 'kategori' => 'Demersal', 'is_active' => true],
        ];

        $ikanModels = [];
        foreach ($jenisIkans as $ikan) {
            $ikan['admin_id'] = $admin->id;
            $ikanModels[] = JenisIkan::create($ikan);
        }

        // 6. Seed Nelayan & Profil
        $nelayans = [
            [
                'nama_lengkap' => 'Budi Santoso',
                'email' => 'budi@gmail.com',
                'password' => Hash::make('password'),
                'no_telepon' => '087712345678',
                'tempat_lahir' => 'Cilacap',
                'tanggal_lahir' => '1980-05-12',
                'status_akun' => 'active',
                'profil' => [
                    'kelompok_name' => 'Mina Makmur',
                    'desa_name' => 'Tambakreja',
                    'rt' => '02', 'rw' => '04',
                    'jenis_kapal' => 'Compreng',
                    'nama_kapal' => 'Mina Berkah',
                    'no_registrasi_kapal' => 'CIL-2023-015',
                    'jenis_tangkapan_utama' => 'Tuna & Tongkol',
                    'status_verifikasi' => 'verified',
                ]
            ],
            [
                'nama_lengkap' => 'Sutrisno Bahari',
                'email' => 'sutris@gmail.com',
                'password' => Hash::make('password'),
                'no_telepon' => '087798765432',
                'tempat_lahir' => 'Kebumen',
                'tanggal_lahir' => '1978-11-03',
                'status_akun' => 'active',
                'profil' => [
                    'kelompok_name' => 'Bakti Laut',
                    'desa_name' => 'Kesugihan',
                    'rt' => '05', 'rw' => '01',
                    'jenis_kapal' => 'Gilnet',
                    'nama_kapal' => 'Sinar Laut',
                    'no_registrasi_kapal' => 'CIL-2022-098',
                    'jenis_tangkapan_utama' => 'Layur & Kembung',
                    'status_verifikasi' => 'verified',
                ]
            ],
            [
                'nama_lengkap' => 'Agus Purnomo',
                'email' => 'agus.p@yahoo.com',
                'password' => Hash::make('password'),
                'no_telepon' => '085212345678',
                'tempat_lahir' => 'Cilacap',
                'tanggal_lahir' => '1985-08-21',
                'status_akun' => 'active',
                'profil' => [
                    'kelompok_name' => 'Samudra Jaya',
                    'desa_name' => 'Adipala',
                    'rt' => '01', 'rw' => '02',
                    'jenis_kapal' => 'Jukung',
                    'nama_kapal' => 'Bumi Intan',
                    'no_registrasi_kapal' => 'CIL-2024-002',
                    'jenis_tangkapan_utama' => 'Kerapu',
                    'status_verifikasi' => 'pending',
                ]
            ],
            [
                'nama_lengkap' => 'Wahyudi',
                'email' => 'wahyu_laut@gmail.com',
                'password' => Hash::make('password'),
                'no_telepon' => '087812341234',
                'tempat_lahir' => 'Cilacap',
                'tanggal_lahir' => '1990-02-15',
                'status_akun' => 'active',
                'profil' => [
                    'kelompok_name' => 'Mina Makmur',
                    'desa_name' => 'Tambakreja',
                    'rt' => '02', 'rw' => '04',
                    'jenis_kapal' => 'Compreng',
                    'nama_kapal' => 'Bahari Indah',
                    'no_registrasi_kapal' => 'CIL-2025-110',
                    'jenis_tangkapan_utama' => 'Cakalang',
                    'status_verifikasi' => 'pending',
                ]
            ]
        ];

        $verifiedNelayanModels = [];

        foreach ($nelayans as $nel) {
            $profData = $nel['profil'];
            unset($nel['profil']);

            $nelayanModel = Nelayan::create($nel);

            $kelompokModel = $kelompokModels[$profData['kelompok_name']];
            $desaModel = $desaModels[$profData['desa_name']];

            $profile = ProfilNelayan::create([
                'nelayan_id' => $nelayanModel->id,
                'kelompok_id' => $kelompokModel->id,
                'desa_id' => $desaModel->id,
                'rt' => $profData['rt'],
                'rw' => $profData['rw'],
                'jenis_kapal' => $profData['jenis_kapal'],
                'nama_kapal' => $profData['nama_kapal'],
                'no_registrasi_kapal' => $profData['no_registrasi_kapal'],
                'jenis_tangkapan_utama' => $profData['jenis_tangkapan_utama'],
                'status_verifikasi' => $profData['status_verifikasi'],
                'foto_profil' => 'https://ui-avatars.com/api/?name=' . urlencode($nelayanModel->nama_lengkap) . '&background=random&color=fff',
                'admin_id' => $profData['status_verifikasi'] === 'verified' ? $admin->id : null,
                'verified_at' => $profData['status_verifikasi'] === 'verified' ? Carbon::now() : null,
            ]);

            if ($profData['status_verifikasi'] === 'verified') {
                $verifiedNelayanModels[] = $nelayanModel;
            }
        }

        // 7. Seed Tangkapan, DetailTangkapan & FotoTangkapan over the past year (June 2025 - June 2026)
        // We will generate data points per week/month for each verified fisherman.
        $startDate = Carbon::create(2025, 6, 1);
        $endDate = Carbon::create(2026, 6, 4);

        $cuacaList = ['Cerah', 'Berawan', 'Gerimis', 'Hujan', 'Hujan Lebat'];

        // Let's create catches weekly
        while ($startDate->lessThanOrEqualTo($endDate)) {
            // Each verified fisherman goes to sea 2-3 times a week
            foreach ($verifiedNelayanModels as $nelayan) {
                // Determine catch weight multiplier based on season (Month of year)
                // Peak season: August - October (East monsoon) -> multiplier 2.0 to 3.5
                // Low season: January - March (West monsoon) -> multiplier 0.3 to 0.7
                // Regular season: other months -> multiplier 0.8 to 1.5
                $month = $startDate->month;
                if ($month >= 8 && $month <= 10) {
                    $multiplier = rand(20, 35) / 10;
                } elseif ($month >= 1 && $month <= 3) {
                    $multiplier = rand(3, 7) / 10;
                } else {
                    $multiplier = rand(8, 15) / 10;
                }

                // Random number of trips in this week (1 to 2 trips)
                $trips = rand(1, 2);
                for ($t = 0; $t < $trips; $t++) {
                    $tripDate = $startDate->copy()->addDays(rand(0, 5));
                    if ($tripDate->greaterThan($endDate)) {
                        continue;
                    }

                    $tangkapan = Tangkapan::create([
                        'nelayan_id' => $nelayan->id,
                        'tanggal_penangkapan' => $tripDate->format('Y-m-d'),
                        'lokasi_nama' => 'Perairan Teluk Penyu, Cilacap',
                        'latitude' => -7.750000 + (rand(-1000, 1000) / 100000),
                        'longitude' => 109.020000 + (rand(-1000, 1000) / 100000),
                        'kondisi_cuaca' => $cuacaList[array_rand($cuacaList)],
                        'keterangan' => 'Hasil tangkapan normal.',
                        'status' => 'verified',
                        'admin_id' => $admin->id,
                        'verified_at' => $tripDate->copy()->addDay(),
                    ]);

                    // Add details (1 to 3 types of fish per catch)
                    $fishTypesCount = rand(1, 3);
                    $selectedFish = (array) array_rand($ikanModels, $fishTypesCount);

                    foreach ($selectedFish as $index) {
                        $fishModel = $ikanModels[$index];
                        // Base weight per fish type: 15kg to 120kg
                        $baseWeight = rand(15, 120);
                        $weight = $baseWeight * $multiplier;

                        DetailTangkapan::create([
                            'tangkapan_id' => $tangkapan->id,
                            'jenis_ikan_id' => $fishModel->id,
                            'nama_ikan' => $fishModel->nama_lokal,
                            'berat_kg' => $weight,
                            'jumlah_ekor' => rand(10, 50),
                            'keterangan' => 'Kualitas baik.',
                        ]);
                    }

                    // Add photo proof
                    FotoTangkapan::create([
                        'tangkapan_id' => $tangkapan->id,
                        'file_path' => 'uploads/catches/sample_catch.jpg',
                        'file_name' => 'sample_catch.jpg',
                        'ukuran_byte' => 150000,
                        'mime_type' => 'image/jpeg',
                        'is_primary' => true,
                    ]);
                }
            }
            $startDate->addWeek();
        }
    }
}
