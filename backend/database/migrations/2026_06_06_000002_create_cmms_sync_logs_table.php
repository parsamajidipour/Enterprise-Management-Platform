<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cmms_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('direction');
            $table->string('action');
            $table->string('external_id')->nullable();
            $table->string('local_type')->nullable();
            $table->unsignedBigInteger('local_id')->nullable();
            $table->string('status');
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['direction', 'action', 'status']);
            $table->index(['external_id', 'local_type', 'local_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cmms_sync_logs');
    }
};
