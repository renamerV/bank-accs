<?php

namespace App\Enums;

use App\Enums\Traits\ReturnsEnumValues;
use Attribute;

#[Attribute]
enum CurrencyEnum: int
{
    use ReturnsEnumValues;

    case RUR = 1;
    case EUR = 2;
    case USD = 3;

    /**
     * Method for migrating DB currencies with proper ID's
     * @return array[]
     */
    public static function getCurrenciesForDb(): array
    {
        return [
            self::RUR->name => [
                'id' => self::RUR->value,
                'name' => self::RUR->name,
            ],
            self::EUR->name => [
                'id' => self::EUR->value,
                'name' => self::EUR->name,
            ],
            self::USD->name => [
                'id' => self::USD->value,
                'name' => self::USD->name,
            ],
        ];
    }
}
