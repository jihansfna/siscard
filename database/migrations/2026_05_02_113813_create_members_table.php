<?php

// database/migrations/2026_05_06_000003_create_members_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')
                ->unique()
                ->default(DB::raw('(UUID())'));

            $table->foreignId('karyawan_id')
                ->constrained('karyawan')
                ->cascadeOnDelete();

            $table->foreignId('jabatan_anggota_id')
                ->constrained('jabatan_anggota')
                ->cascadeOnUpdate();
            
            $table->enum('status', [
                'pending',
                'registered',
                'inactive',
                'rejected'
            ])->default('pending');

            $table->boolean('aktif')
                ->default(true);

            $table->boolean('tampil')
                ->default(true);

            $table->timestamp('disetujui_pada')
                ->nullable();

            $table->string('tanda_tangan')
                ->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota');
    }
};