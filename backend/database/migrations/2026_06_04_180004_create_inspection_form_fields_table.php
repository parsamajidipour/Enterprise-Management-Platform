<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspection_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_form_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->enum('field_type', ['text', 'number', 'boolean', 'select', 'textarea', 'photo'])->default('text');
            $table->json('options')->nullable();
            $table->boolean('is_required')->default(false);
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_form_fields');
    }
};
