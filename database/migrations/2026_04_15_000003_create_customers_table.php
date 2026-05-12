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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->unsignedInteger('usage_limit')->default(0);
            $table->unsignedInteger('usage_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['merchant_id', 'account_id']);
            $table->index(['merchant_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
