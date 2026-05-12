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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('users')->cascadeOnDelete();
            $table->string('label');
            $table->string('email')->nullable();
            $table->string('username')->nullable();
            $table->text('password_encrypted');
            $table->text('secret_key_encrypted');
            $table->timestamps();

            $table->index(['merchant_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
