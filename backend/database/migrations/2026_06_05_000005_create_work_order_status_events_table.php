<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_order_status_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->cascadeOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('source')->default('admin');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['work_order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_order_status_events');
    }
};
