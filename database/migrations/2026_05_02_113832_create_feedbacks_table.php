<?php

// database/migrations/2026_05_06_000005_create_feedbacks_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saran', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')
                ->unique()
                ->default(DB::raw('(UUID())'));

            $table->foreignId('anggota_id')
                ->constrained('anggota')
                ->cascadeOnDelete();

            $table->string('berkas')
                ->nullable();

            $table->text('deskripsi');

            $table->enum('status', [
                'pending',
                'reviewed',
                'approved',
                'rejected'
            ])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saran');
    }
};