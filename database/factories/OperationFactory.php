<?php

namespace Database\Factories;

use App\Models\AccountCurrency;
use App\Models\Operation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Operation>
 */
class OperationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Operation::class;

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
    public function configure()
    {
        return $this->afterMaking(
            function (Operation $operation) {
                $operation->value = $operation->value ?? 0;
                $operation->account_currency_id = $operation->account_currency_id ??
                    AccountCurrency::factory()->create()->id;
            }
        );
    }
}
