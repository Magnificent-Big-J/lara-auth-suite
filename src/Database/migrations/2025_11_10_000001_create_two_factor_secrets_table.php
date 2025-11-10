<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('two_factor_secrets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->index();
            $table->string('type', 20);                     // totp | sms | email (for future-proofing)
            $table->text('secret')->nullable();             // cast to 'encrypted' in model
            $table->json('recovery_codes')->nullable();     // cast to 'encrypted:array'
            $table->timestamp('enabled_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->json('meta')->nullable();               // issuer/label, device info, etc.
            $table->timestamps();

            $table->index(['user_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('two_factor_secrets');
    }
};
