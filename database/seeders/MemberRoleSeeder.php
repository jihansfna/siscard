<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MemberRole;

class MemberRoleSeeder extends Seeder
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
                'name'      => 'Ketua',
                'is_single' => true,
                'is_sign'   => true,
            ],
            [
                'name'      => 'Sekretaris',
                'is_single' => true,
                'is_sign'   => true,
            ],
        ];

        foreach ($roles as $role) {
            MemberRole::firstOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}
