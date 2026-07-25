<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE work_orders MODIFY status VARCHAR(50) NOT NULL DEFAULT 'pending'");

        Schema::table('work_orders', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('priority');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });

        DB::statement("ALTER TABLE work_orders MODIFY status ENUM('pending', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
    }
};
