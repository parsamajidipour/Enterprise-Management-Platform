<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->string('source')->nullable()->after('assigned_to');
            $table->string('external_id')->nullable()->after('source');
            $table->string('external_reference')->nullable()->after('external_id');
            $table->string('outage_type')->nullable()->after('external_reference');
            $table->timestamp('reported_at')->nullable()->after('outage_type');
            $table->string('cmms_status')->nullable()->after('reported_at');

            $table->unique('external_id');
            $table->index(['source', 'cmms_status']);
        });
    }

    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropUnique(['external_id']);
            $table->dropIndex(['source', 'cmms_status']);
            $table->dropColumn([
                'source',
                'external_id',
                'external_reference',
                'outage_type',
                'reported_at',
                'cmms_status',
            ]);
        });
    }
};
