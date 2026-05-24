<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('member_id')
                ->nullable() // Can be nullable if it's a general member action where member was deleted
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('actor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('activity'); // e.g., 'created', 'updated', 'status_changed', 'deleted', 'exported'

            $table->string('status')->nullable(); // new status if applicable

            $table->text('description')->nullable(); // detail description

            $table->text('remark')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_logs');
    }
};