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
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            // only add it if it doesn't already exist
            if (! Schema::hasColumn('password_reset_tokens', 'verification_code')) {
                $table->string('verification_code', 6)
                      ->nullable()
                      ->after('token');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            // only drop it if it really exists
            if (Schema::hasColumn('password_reset_tokens', 'verification_code')) {
                $table->dropColumn('verification_code');
            }
        });
    }
};
