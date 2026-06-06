<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('saran', function (Blueprint $table) {
            $table->text('catatan')->nullable()->after('deskripsi');
            // Since altering an enum can be tricky, we change it to string
            $table->string('status')->default('Waiting')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saran', function (Blueprint $table) {
            $table->dropColumn('catatan');
        });
    }
};
