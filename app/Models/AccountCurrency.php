<?php

namespace App\Models;

use App\Enums\CurrencyEnum;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use function Symfony\Component\Translation\t;

/**
 * Class AccountCurrency
 * @property int $account_id
 * @property int $currency_id
 * @property float $current_value
 * @property bool $is_primary
 */
class AccountCurrency extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'currency_id' => CurrencyEnum::class,
        'is_primary' => 'boolean'
    ];

    public function makePrimary(): AccountCurrency
    {
        $this->is_primary = true;
        $this->save();

        return $this;
    }

    public function makeAdditional(): AccountCurrency
    {
        $this->is_primary = false;
        $this->save();

        return $this;
    }

    public function changeValue(float $value)
    {
        if ($this->current_value + $value < 0) {
            throw new Exception('The balance of the account cannot be less than 0.');
        }

        $this->current_value += $value;
        $this->save();

        return $this;
    }

    public function convertToCurrency(CurrencyEnum $currency, float $value = null)
    {
        $currencyRate = CurrencyRate::getCurrencyRate($this->currency_id, $currency);

        $value = $value === null ? $this->current_value : $value;

        return $currencyRate->value * $value;
    }
}
