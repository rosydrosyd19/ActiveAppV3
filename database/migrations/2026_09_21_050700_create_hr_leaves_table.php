<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('hr_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('core_users')->nullOnDelete();
            $table->string('leave_type')->default('annual');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days')->nullable();
            $table->text('reason');
            $table->string('status')->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('hr_leaves'); }
};
