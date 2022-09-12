<?php

use App\Enums\CurrencyEnum;
use App\Models\CurrencyRate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('first_currency_id');
            $table->unsignedBigInteger('second_currency_id');
            $table->float('value', 10, 4); // For real cases makes sense use integers to avoid rounding operations and loss of accuracy
            $table->timestamps();

            $table->foreign('first_currency_id')
                ->references('id')
                ->on('currencies');

            $table->foreign('second_currency_id')
                ->references('id')
                ->on('currencies');
        });

        // Filling table with initial data:
        CurrencyRate::addNewRate(
            CurrencyEnum::EUR,
            CurrencyEnum::RUR,
            80
        );

        CurrencyRate::addNewRate(
            CurrencyEnum::USD,
            CurrencyEnum::RUR,
            70
        );

        CurrencyRate::addNewRate(
            CurrencyEnum::EUR,
            CurrencyEnum::USD,
            1
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currency_rates');
    }
};
