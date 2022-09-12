<?php

namespace Database\Factories;

use App\Models\CurrencyRate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CurrencyRate>
 */
class CurrencyRateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CurrencyRate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'value' => 0
        ];
    }

    /**
     * @inheritDoc
     */
//    public function configure()
//    {
//        return $this->afterMaking(
//            function (CurrencyRate $currencyRate) {
//                $currencyRate->value = $currencyRate->value ?? 0;
////                $currencyRate->first_currency_id = $currencyRate->first_currency_id ?? 0;
//
////                first_currency_id
////second_currency_id
//
////                $operation->value = $operation->value ?? 0;
////                $operation->account_currency_id = $operation->account_currency_id ??
////                    AccountCurrency::factory()->create()->id;
//            }
//        );
//    }
}
