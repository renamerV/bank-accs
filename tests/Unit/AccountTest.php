<?php

namespace Tests\Unit;

use App\Enums\CurrencyEnum;
use App\Models\Account;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class AccountTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    public function testCreateAccountAndFill()
    {
        $account = Account::create($this->faker->name);

        $this->assertDatabaseHas('accounts', [
            'owner' => $account->owner
        ]);

        // Adding currencies
        $account->addCurrency(CurrencyEnum::RUR);
        $account->addCurrency(CurrencyEnum::EUR);
        $account->addCurrency(CurrencyEnum::USD);

        $this->assertDatabaseHas('account_currencies', [
            'account_id' => $account->id,
            'currency_id' => CurrencyEnum::RUR,
            'is_primary' => true,
        ]);

        $this->assertDatabaseHas('account_currencies', [
            'account_id' => $account->id,
            'currency_id' => CurrencyEnum::EUR,
            'is_primary' => false,
        ]);

        $this->assertDatabaseHas('account_currencies', [
            'account_id' => $account->id,
            'currency_id' => CurrencyEnum::USD,
            'is_primary' => false,
        ]);

        // Check available currencies
        $this->assertEquals(
            [
                CurrencyEnum::RUR,
                CurrencyEnum::EUR,
                CurrencyEnum::USD
            ],
            $account->getAvailableCurrencies()->toArray()
        );

        // Setting another currency as primary
        $account->setPrimaryCurrency(CurrencyEnum::EUR);

        $this->assertDatabaseHas('account_currencies', [
            'account_id' => $account->id,
            'currency_id' => CurrencyEnum::EUR,
            'is_primary' => true,
        ]);

        $this->assertDatabaseHas('account_currencies', [
            'account_id' => $account->id,
            'currency_id' => CurrencyEnum::RUR,
            'is_primary' => false,
        ]);

        // Return previous primary currency
        $account->setPrimaryCurrency(CurrencyEnum::RUR);

        $this->assertDatabaseHas('account_currencies', [
            'account_id' => $account->id,
            'currency_id' => CurrencyEnum::RUR,
            'is_primary' => true,
        ]);

        $this->assertDatabaseHas('account_currencies', [
            'account_id' => $account->id,
            'currency_id' => CurrencyEnum::EUR,
            'is_primary' => false,
        ]);

        // Increasing balance amount
        $account->increaseBalance(CurrencyEnum::RUR, 1000);
        $account->increaseBalance(CurrencyEnum::EUR, 50);
        $account->increaseBalance(CurrencyEnum::USD, 40);

        $this->assertDatabaseHas('account_currencies', [
            'account_id' => $account->id,
            'currency_id' => CurrencyEnum::RUR,
            'current_value' => 1000,
        ]);

        $this->assertDatabaseHas('account_currencies', [
            'account_id' => $account->id,
            'currency_id' => CurrencyEnum::EUR,
            'current_value' => 50,
        ]);

        $this->assertDatabaseHas('account_currencies', [
            'account_id' => $account->id,
            'currency_id' => CurrencyEnum::USD,
            'current_value' => 40,
        ]);
    }

    public function testCreateWrongAccount()
    {
        $account = Account::create($this->faker->name);

        $this->expectExceptionMessage('This currency already exist on this account!');

        $account->addCurrency(CurrencyEnum::RUR);
        $account->addCurrency(CurrencyEnum::RUR);
    }

    public function testSetWrongPrimaryCurrency()
    {
        $account = Account::create($this->faker->name);

        $this->expectExceptionMessage('Account don\'t have this currency');

        $account->setPrimaryCurrency(CurrencyEnum::EUR);
    }

    public function testChangeBalanceDontHaveCurrency()
    {
        $account = Account::create($this->faker->name);

        $account->addCurrency(CurrencyEnum::RUR);

        $this->expectExceptionMessage('Account don\'t have this currency');

        $account->changeBalance(CurrencyEnum::EUR, 100);
    }

    public function testChangeBalanceWrongValue()
    {
        $account = Account::create($this->faker->name);

        $account->addCurrency(CurrencyEnum::RUR);

        $this->expectExceptionMessage('Value should be more than 0');

        $account->changeBalance(CurrencyEnum::RUR, -100);
    }

    public function testDisableWrongCurrency()
    {
        $account = Account::create($this->faker->name);

        $this->expectExceptionMessage('Account don\'t have this currency');

        $account->disableCurrency(CurrencyEnum::RUR);
    }

    public function testDisablePrimaryCurrency()
    {
        $account = Account::create($this->faker->name);

        $account->addCurrency(CurrencyEnum::RUR);

        $this->expectExceptionMessage('You cannot disable primary currency!');

        $account->disableCurrency(CurrencyEnum::RUR);
    }
}
