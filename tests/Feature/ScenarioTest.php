<?php

namespace Tests\Feature;

use App\Enums\CurrencyEnum;
use App\Models\Account;
use App\Models\AccountCurrency;
use App\Models\CurrencyRate;
use App\Models\Operation;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ScenarioTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCreatingAndFillingAccount()
    {
        // 1. Creating an account
        $account = Account::create($this->faker->name);

        $this->assertDatabaseHas('accounts', [
            'owner' => $account->owner
        ]);

        // Adding currencies
        $account->addCurrency(CurrencyEnum::RUR);
        $account->addCurrency(CurrencyEnum::EUR);
        $account->addCurrency(CurrencyEnum::USD);

        // Check available currencies
        $this->assertEquals(
            [
                CurrencyEnum::RUR,
                CurrencyEnum::EUR,
                CurrencyEnum::USD
            ],
            $account->getAvailableCurrencies()->toArray()
        );

        // Increasing balance amount
        $account->increaseBalance(CurrencyEnum::RUR, 1000);
        $account->increaseBalance(CurrencyEnum::EUR, 50);
        $account->increaseBalance(CurrencyEnum::USD, 40);


        // 2. Check balances
        $this->assertEquals(
            [
                'currency' => CurrencyEnum::RUR,
                'value' => 7800.0
            ],
            $account->getBalance()
        );

        $this->assertEquals(
            [
                'currency' => CurrencyEnum::EUR,
                'value' => 50.0
            ],
            $account->getBalance(CurrencyEnum::EUR)
        );

        $this->assertEquals(
            [
                'currency' => CurrencyEnum::USD,
                'value' => 40.0
            ],
            $account->getBalance(CurrencyEnum::USD)
        );


        // 3. Increase and decrease balance operations
        $account->increaseBalance(CurrencyEnum::RUR, 1000);
        $account->increaseBalance(CurrencyEnum::EUR, 50);
        $account->decreaseBalance(CurrencyEnum::USD, 10);


        // 4. Changing currency rates
        CurrencyRate::addNewRate(
            CurrencyEnum::EUR,
            CurrencyEnum::RUR,
            150
        );

        CurrencyRate::addNewRate(
            CurrencyEnum::USD,
            CurrencyEnum::RUR,
            100
        );


        // 5. Get ruble balance:
        $this->assertEquals([
            'currency' => CurrencyEnum::RUR,
            'value' => 20000.0
        ], $account->getBalance());


        // 6. Change main currency and getting balance:
        $account->setPrimaryCurrency(CurrencyEnum::EUR);

        $beforeConverting = $account->getBalance();
        $this->assertEquals([
            'currency' => CurrencyEnum::EUR,
            'value' => 143.4
        ], $beforeConverting);


        // 7. Converting rubles to euro:
        $rubleAmount = $account->getBalance(CurrencyEnum::RUR);

        $account->decreaseBalance($rubleAmount['currency'], $rubleAmount['value']);

        $euroAmount = CurrencyRate::convertToCurrency(
            $rubleAmount['currency'],
            CurrencyEnum::EUR,
            $rubleAmount['value']
        );

        $account->increaseBalance(CurrencyEnum::EUR, $euroAmount);


        // 8. Bank changed currency rate and
        CurrencyRate::addNewRate(
            CurrencyEnum::EUR,
            CurrencyEnum::RUR,
            120
        );


        // 9. Checking balance after changing of exchange rate
        $afterConverting = $account->getBalance();
        $this->assertEquals($beforeConverting, $afterConverting);


        // 10. Disabling account currencies and convert it to main currency
        $account->setPrimaryCurrency(CurrencyEnum::RUR);

        $account->disableCurrency(CurrencyEnum::EUR);
        $account->disableCurrency(CurrencyEnum::USD);
    }
}
