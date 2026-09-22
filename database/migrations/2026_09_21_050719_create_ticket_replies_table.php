<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_issue_id')->constrained('ticket_issues')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('core_users')->nullOnDelete();
            $table->text('reply');
            $table->string('attachment')->nullable();
            $table->boolean('is_internal')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('ticket_replies'); }
};
