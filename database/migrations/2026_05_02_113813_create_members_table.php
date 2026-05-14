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
        Schema::create('members', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')
                ->unique()
                ->default(DB::raw('(UUID())'));

            $table->foreignId('employee_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('member_role_id')
                ->constrained('member_roles')
                ->cascadeOnUpdate();
            
            $table->enum('status', [
                'pending',
                'registered',
                'inactive',
                'rejected'
            ])->default('pending');

            $table->boolean('is_active')
                ->default(true);

            $table->boolean('is_show')
                ->default(true);

            $table->timestamp('approved_at')
                ->nullable();

            $table->string('sign_image')
                ->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};