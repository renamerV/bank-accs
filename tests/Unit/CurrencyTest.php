<?php

namespace Tests\Unit;

use App\Enums\CurrencyEnum;
use App\Models\CurrencyRate;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CurrencyTest extends TestCase
{
    use DatabaseTransactions;

    public function test_set_currency()
    {
//        $currencyRate = CurrencyRate::addNewRate(
//            CurrencyEnum::EUR,
//            CurrencyEnum::RUR,
//            80
//        );


//        dd($currencyRate);
    }
}
