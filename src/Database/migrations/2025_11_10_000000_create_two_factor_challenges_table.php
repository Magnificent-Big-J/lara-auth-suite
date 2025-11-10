<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('two_factor_challenges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->index();
            $table->string('channel', 20);                  // email | sms | totp (for backup)
            $table->string('code_hash', 255);               // store bcrypt/argon hash, never plain code
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->unsignedTinyInteger('max_attempts')->default(5);
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable();
            $table->json('meta')->nullable();               // ip, ua, etc.
            $table->timestamps();

            $table->index(['user_id', 'channel']);
            $table->index(['user_id', 'channel', 'consumed_at']);
            $table->index(['expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('two_factor_challenges');
    }
};
