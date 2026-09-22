<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_item_id')->constrained('asset_items')->cascadeOnDelete();
            $table->date('maintenance_date');
            $table->string('type')->default('routine');
            $table->text('description');
            $table->decimal('cost', 15, 2)->nullable();
            $table->string('technician')->nullable();
            $table->string('status')->default('completed');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('asset_maintenances'); }
};
