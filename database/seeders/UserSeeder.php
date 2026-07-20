<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::create([
            'uuid' => Str::uuid(),
            'name' => 'DEVELOPER',
            'email' => 'dzakysan2002@gmail.com',
            'pokja' => json_encode(['Developer SI-JAKI']),
            'password' => Hash::make('dev_ds2002'),
            'akses' => 'Dev',
        ]);

        User::create([
            'uuid' => Str::uuid(),
            'name' => 'ADMINKLK',
            'pokja' => json_encode(['Admin SI-JAKI']),
            'password' => Hash::make('admin_lldikti3'),
            'akses' => 'Admin',
        ]);

        User::create([
            'uuid' => Str::uuid(),
            'name' => 'KLK',
            'pokja' => json_encode(['Kelembagaan dan Kemitraan']),
            'akses' => 'User',
            'password' => Hash::make('lldikti3'),
        ]);

        User::create([
            'uuid' => Str::uuid(),
            'name' => 'BAGUM',
            'pokja' => json_encode(['Kepala Bagian Umum']),
            'akses' => 'User',
            'password' => Hash::make('lldikti3'),
        ]);

        User::create([
            'uuid' => Str::uuid(),
            'name' => 'KEPALA',
            'pokja' => json_encode(['Kepala LLDIKTI']),
            'akses' => 'User',
            'password' => Hash::make('lldikti3'),
        ]);

        User::create([
            'uuid' => Str::uuid(),
            'name' => 'SI',
            'pokja' => json_encode(['Sistem Informasi dan PDDikti']),
            'akses' => 'User',
            'password' => Hash::make('lldikti3'),
        ]);

        User::create([
            'uuid' => Str::uuid(),
            'name' => 'HUMAS',
            'pokja' => json_encode(['Hubungan Masyarakat dan Kerja Sama']),
            'akses' => 'User',
            'password' => Hash::make('lldikti3'),
        ]);

        User::create([
            'uuid' => Str::uuid(),
            'name' => 'HKT',
            'pokja' => json_encode(['Hukum, Kepegawaian, dan Tata Laksana']),
            'akses' => 'User',
            'password' => Hash::make('lldikti3'),
        ]);

        User::create([
            'uuid' => Str::uuid(),
            'name' => 'RPM',
            'pokja' => json_encode(['Riset dan Pengabdian Masyarakat']),
            'akses' => 'User',
            'password' => Hash::make('lldikti3'),
        ]);

        User::create([
            'uuid' => Str::uuid(),
            'name' => 'BELMAWA',
            'pokja' => json_encode(['Pembelajaran, Kemahasiswaan, dan Prestasi']),
            'akses' => 'User',
            'password' => Hash::make('lldikti3'),
        ]);

        User::create([
            'uuid' => Str::uuid(),
            'name' => 'PENJAMU',
            'pokja' => json_encode(['Penjaminan Mutu']),
            'akses' => 'User',
            'password' => Hash::make('lldikti3'),
        ]);

        User::create([
            'uuid' => Str::uuid(),
            'name' => 'SDP',
            'pokja' => json_encode(['Sumber Daya']),
            'akses' => 'User',
            'password' => Hash::make('lldikti3'),
        ]);

        User::create([
            'uuid' => Str::uuid(),
            'name' => 'ADIA',
            'pokja' => json_encode(['Anti Dosa Pendidikan dan Integritas Akademik']),
            'akses' => 'User',
            'password' => Hash::make('lldikti3'),
        ]);

        User::create([
            'uuid' => Str::uuid(),
            'name' => 'PP',
            'pokja' => json_encode(['Perencanaan dan Keuangan']),
            'akses' => 'User',
            'password' => Hash::make('lldikti3'),
        ]);

        User::create([
            'uuid' => Str::uuid(),
            'name' => 'TUBMN',
            'pokja' => json_encode(['Tata Usaha dan Barang Milik Negara']),
            'akses' => 'User',
            'password' => Hash::make('lldikti3'),
        ]);
    }
}
