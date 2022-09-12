<?php

namespace Database\Factories;

use App\Enums\CurrencyEnum;
use App\Models\Account;
use App\Models\AccountCurrency;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AccountCurrency>
 */
class AccountCurrencyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AccountCurrency::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'current_value' => 0
        ];
    }

    /**
     * @inheritDoc
     */
    public function configure()
    {
        return $this->afterMaking(
            function (AccountCurrency $accountCurrency) {
                $accountCurrency->account_id = $accountCurrency->account_id ?? Account::factory()->create()->id;
                $accountCurrency->is_primary = $accountCurrency->is_primary ?? false;
                $accountCurrency->currency_id = $accountCurrency->currency_id ??
                    CurrencyEnum::RUR->value; // Default Currency is RUR
            }
        );
    }
}
