<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('from_currency_id');
            $table->uuid('to_currency_id');
            $table->decimal('rate', 15, 6);
            $table->date('effective_date');
            $table->string('source', 50)->default('api'); // api, manual, etc.
            $table->timestamps();
            
            // $table->foreign('from_currency_id')->references('id')->on('currencies');
            // $table->foreign('to_currency_id')->references('id')->on('currencies');
            
            $table->index(['from_currency_id', 'to_currency_id']);
            $table->index(['effective_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
