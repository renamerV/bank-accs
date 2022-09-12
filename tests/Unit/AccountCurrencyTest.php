<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\AccountCurrency;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccountCurrencyTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public function testWrongValueChange()
    {
        $accountCurrency = AccountCurrency::factory()->create();

        $this->expectExceptionMessage('The balance of the account cannot be less than 0.');

        $accountCurrency->changeValue(-100);
    }
}
