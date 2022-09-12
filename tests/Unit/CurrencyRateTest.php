<?php

namespace Tests\Unit;

use App\Enums\CurrencyEnum;
use App\Models\CurrencyRate;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CurrencyRateTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreateSameCurrencyRate()
    {
        $this->expectExceptionMessage('Wrong currency, you cannot set exchange rate for the same currency.');

        CurrencyRate::addNewRate(
            CurrencyEnum::EUR,
            CurrencyEnum::EUR,
            120
        );
    }

    public function testCreateWrongValueCurrencyRate()
    {
        $this->expectExceptionMessage('Wrong value.');

        CurrencyRate::addNewRate(
            CurrencyEnum::EUR,
            CurrencyEnum::RUR,
            -5
        );
    }
}
