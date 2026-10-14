<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_auth_face_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('core_users')->cascadeOnDelete();
            $table->text('descriptor'); // JSON array of 128 floats
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_auth_face_data');
    }
};
