<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ticket_issue_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_issue_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('core_users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::table('ticket_issues', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropColumn('assigned_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_issues', function (Blueprint $table) {
            $table->foreignId('assigned_to')->nullable()->constrained('core_users')->nullOnDelete();
        });
        Schema::dropIfExists('ticket_issue_user');
    }
};
