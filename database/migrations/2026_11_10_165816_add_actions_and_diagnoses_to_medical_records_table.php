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
        Schema::table('medical_records', function (Blueprint $table) {
            $table->string('secondary_icd10_code', 20)->nullable()->after('icd10_description');
            $table->string('secondary_diagnosis')->nullable()->after('secondary_icd10_code');
        });

        Schema::create('medical_record_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained('medical_records')->cascadeOnDelete();
            $table->foreignId('medical_action_id')->constrained('medical_actions')->cascadeOnDelete();
            $table->string('action_name_snapshot', 150);
            $table->decimal('price_snapshot', 12, 2)->default(0);
            $table->unsignedSmallInteger('qty')->default(1);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_record_actions');

        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn(['secondary_icd10_code', 'secondary_diagnosis']);
        });
    }
};
