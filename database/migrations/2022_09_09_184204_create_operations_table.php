<?php

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
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_currency_id');
            $table->float('value');   // Может быть положительным - зачисление и отрицательным - списание.
                                             // сумма всех операций должна быть равна текущему значению счёта
            $table->timestamps();

            $table->foreign('account_currency_id')
                ->references('id')
                ->on('account_currencies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operations');
    }
};
