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
        Schema::table('patients', function (Blueprint $table) {
            $table->string('bpjs_number', 30)->nullable()->after('allergies');
            $table->string('insurance_name', 100)->nullable()->after('bpjs_number');
            $table->string('insurance_number', 50)->nullable()->after('insurance_name');
            $table->string('emergency_contact_name', 100)->nullable()->after('address');
            $table->string('emergency_contact_phone', 30)->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_relation', 50)->nullable()->after('emergency_contact_phone');
        });

        Schema::table('visits', function (Blueprint $table) {
            $table->string('bpjs_card_number', 50)->nullable()->after('payment_type');
            $table->string('referral_letter_path', 255)->nullable()->after('bpjs_card_number');
            $table->string('insurance_name', 100)->nullable()->after('referral_letter_path');
            $table->string('insurance_card_number', 50)->nullable()->after('insurance_name');
        });

        Schema::table('queues', function (Blueprint $table) {
            $table->string('qr_token', 64)->nullable()->unique()->after('queue_code');
            $table->string('estimated_time', 10)->nullable()->after('qr_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            $table->dropColumn(['qr_token', 'estimated_time']);
        });

        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn([
                'bpjs_card_number',
                'referral_letter_path',
                'insurance_name',
                'insurance_card_number',
            ]);
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'bpjs_number',
                'insurance_name',
                'insurance_number',
                'emergency_contact_name',
                'emergency_contact_phone',
                'emergency_contact_relation',
            ]);
        });
    }
};
