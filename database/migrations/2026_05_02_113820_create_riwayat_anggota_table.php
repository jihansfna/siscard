<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_anggota', function (Blueprint $table) {
            $table->id();

            $table->foreignId('anggota_id')
                ->nullable() // Can be nullable if it's a general member action where member was deleted
                ->constrained('anggota')
                ->nullOnDelete();

            $table->foreignId('pelaku_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('aktivitas'); // e.g., 'created', 'updated', 'status_changed', 'deleted', 'exported'

            $table->string('status')->nullable(); // new status if applicable

            $table->text('deskripsi')->nullable(); // detail description
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_anggota');
    }
};