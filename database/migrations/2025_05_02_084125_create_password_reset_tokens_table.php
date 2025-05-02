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
        // Si la tabla no existe, la creamos
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->string('verification_code', 6)->nullable();
                $table->timestamp('created_at')->nullable();
            });
        } 
        // Si existe pero no tiene la columna verification_code, la agregamos
        else if (!Schema::hasColumn('password_reset_tokens', 'verification_code')) {
            Schema::table('password_reset_tokens', function (Blueprint $table) {
                $table->string('verification_code', 6)->nullable()->after('token');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No eliminamos la tabla completa por seguridad
        if (Schema::hasColumn('password_reset_tokens', 'verification_code')) {
            Schema::table('password_reset_tokens', function (Blueprint $table) {
                $table->dropColumn('verification_code');
            });
        }
    }
};