<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('hr_departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('head_of_department')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('hr_departments'); }
};
