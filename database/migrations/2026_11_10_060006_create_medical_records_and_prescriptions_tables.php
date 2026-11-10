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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->unique()->constrained('visits')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('nurse_id')->nullable()->constrained('users')->nullOnDelete();

            // Tanda-Tanda Vital (TTV)
            $table->string('blood_pressure', 20)->nullable(); // Tekanan Darah (mmHg, mis: 120/80)
            $table->unsignedSmallInteger('heart_rate')->nullable(); // Denyut Nadi (bpm)
            $table->unsignedSmallInteger('respiratory_rate')->nullable(); // Laju Nafas (x/menit)
            $table->decimal('temperature', 4, 1)->nullable(); // Suhu Tubuh (°C)
            $table->decimal('weight', 5, 2)->nullable(); // Berat Badan (kg)
            $table->decimal('height', 5, 2)->nullable(); // Tinggi Badan (cm)

            // Catatan Medis SOAP
            $table->text('subjective')->nullable(); // Keluhan Utama & Anamnesis
            $table->text('objective')->nullable(); // Pemeriksaan Fisik
            $table->text('assessment')->nullable(); // Diagnosis Kerja
            $table->string('icd10_code', 20)->nullable()->index(); // Kode ICD-10
            $table->string('icd10_description')->nullable(); // Deskripsi ICD-10
            $table->text('plan')->nullable(); // Rencana Terapi / Edukasi
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('pharmacist_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 30)->default('pending')->index(); // PrescriptionStatus
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained('prescriptions')->cascadeOnDelete();
            $table->foreignId('medicine_id')->constrained('medicines')->cascadeOnDelete();
            $table->string('medicine_name_snapshot', 150);
            $table->unsignedSmallInteger('qty')->default(1);
            $table->string('dosage', 100); // Misal: "3x1 tablet sesudah makan"
            $table->decimal('price_snapshot', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('medical_records');
    }
};
