<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('approval_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_request_id')->constrained('approval_requests')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('core_users')->nullOnDelete();
            $table->string('action');
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('approval_logs'); }
};
