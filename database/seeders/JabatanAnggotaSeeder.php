<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JabatanAnggota;

class JabatanAnggotaSeeder extends Seeder
{
    /**
     * Seed the member_roles table.
     *
     * is_single: true = hanya 1 orang yang boleh memegang role ini
     * is_sign:   true = tanda tangan role ini muncul di kartu digital
     */
    public function run(): void
    {
        $roles = [
            [
                'nama'      => 'Ketua',
                'tunggal' => true,
                'penandatangan'   => true,
            ],
            [
                'nama'      => 'Sekretaris',
                'tunggal' => true,
                'penandatangan'   => true,
            ],
        ];

        foreach ($roles as $role) {
            JabatanAnggota::firstOrCreate(
                ['nama' => $role['nama']],
                $role
            );
        }
    }
}
