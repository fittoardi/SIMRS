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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 100);
            $table->string('ward_name', 100); // Misal: Bangsal Melati, Bangsal Mawar, ICU
            $table->string('class_type', 30); // VVIP, VIP, Kelas 1, Kelas 2, Kelas 3, ICU
            $table->decimal('price_per_day', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->string('bed_number', 20);
            $table->string('status', 30)->default('available'); // available, occupied, cleaning, maintenance
            $table->unsignedBigInteger('current_visit_id')->nullable()->index();
            $table->timestamps();

            $table->unique(['room_id', 'bed_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beds');
        Schema::dropIfExists('rooms');
    }
};
