<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $currencies = [
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'exchange_rate' => 1.0000],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'exchange_rate' => 0.8500],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'exchange_rate' => 0.7300],
            ['code' => 'JPY', 'name' => 'Japanese Yen', 'symbol' => '¥', 'exchange_rate' => 110.0000],
            ['code' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => 'C$', 'exchange_rate' => 1.2500],
            ['code' => 'AUD', 'name' => 'Australian Dollar', 'symbol' => 'A$', 'exchange_rate' => 1.3500],
            ['code' => 'CHF', 'name' => 'Swiss Franc', 'symbol' => 'CHF', 'exchange_rate' => 0.9200],
            ['code' => 'CNY', 'name' => 'Chinese Yuan', 'symbol' => '¥', 'exchange_rate' => 6.4500],
            ['code' => 'INR', 'name' => 'Indian Rupee', 'symbol' => '₹', 'exchange_rate' => 74.5000],
            ['code' => 'ZAR', 'name' => 'South African Rand', 'symbol' => 'R', 'exchange_rate' => 14.5000],
        ];

        foreach ($currencies as $currency) {
            $currency['created_at'] = now();
            $currency['updated_at'] = now();
        }

        DB::table('currencies')->insert($currencies);
    }

    public function down(): void
    {
        DB::table('currencies')->truncate();
    }
};
