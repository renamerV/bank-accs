<?php

namespace App\Models;

use App\Enums\CurrencyEnum;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model CurrencyRate
 * @property float $value
 * @property int $first_currency_id
 * @property int $second_currency_id
 */
class CurrencyRate extends Model
{
    use HasFactory;

    public static function addNewRate(
        CurrencyEnum $firstCurrency,
        CurrencyEnum $secondCurrency,
        float $value
    ): CurrencyRate {
        if ($firstCurrency === $secondCurrency) {
            throw new Exception('Wrong currency, you cannot set exchange rate for the same currency.');
        }

        if ($value <= 0) {
            throw new Exception('Wrong value.');
        }

        $currencyRate = new CurrencyRate();

        $currencyRate->first_currency_id = $firstCurrency;
        $currencyRate->second_currency_id = $secondCurrency;
        $currencyRate->value = $value;

        $currencyRate->save();

        // Setting reverse exchange rate
        // In real banking usually the buying and selling rates of currencies are different,
        // but in our example it will be almost equal to initial rate:
        $reverseRate = new CurrencyRate();

        $reverseRate->first_currency_id = $secondCurrency;
        $reverseRate->second_currency_id = $firstCurrency;
        $reverseRate->value = round(1 / $value, 4);

        $reverseRate->save();

        return $currencyRate;
    }

    public static function getCurrencyRate(CurrencyEnum $firstCurrency, CurrencyEnum $secondCurrency): CurrencyRate
    {
        return CurrencyRate::where('first_currency_id', '=', $firstCurrency)
            ->where('second_currency_id', '=', $secondCurrency)
            ->orderByDesc('updated_at')
            ->limit(1)
            ->first();
    }

    public static function convertToCurrency(
        CurrencyEnum $firstCurrency,
        CurrencyEnum $secondCurrency,
        float $value
    ): float {
        $currencyRate = self::getCurrencyRate($firstCurrency, $secondCurrency);

        return $value * $currencyRate->value;
    }
}
