<?php

namespace App\Models;

use App\Enums\CurrencyEnum;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * Class Account
 * @property string $owner
 * @property Collection|AccountCurrency[] $currencies
 */
final class Account extends Model
{
    use HasFactory;

    /**
     * @return HasMany
     */
    public function currencies(): HasMany
    {
        return $this->hasMany(AccountCurrency::class);
    }

    /**
     * @param string $owner
     * @return Account
     */
    public static function create(string $owner): Account
    {
        $account = new Account();
        $account->owner = $owner;
        $account->save();

        return $account;
    }

    /**
     * Method for adding currencies to account.
     * If Account doesn't have any currencies, makes first currency primary.
     * @param string $currency
     * @return $this
     */
    public function addCurrency(CurrencyEnum $currency): AccountCurrency
    {
        if ($this->getCurrency($currency)) {
            throw new Exception('This currency already exist on this account!');
        }

        $accountCurrency = AccountCurrency::factory()
            ->create([
                'account_id' => $this->id,
                'currency_id' => $currency->value,
                'is_primary' => count($this->currencies()->get()) === 0 ? true : false,
            ]);

        return $accountCurrency;
    }

    public function getAvailableCurrencies(): ?Collection
    {
        return $this->currencies()->whereNull('deleted_at')->pluck('currency_id');
    }

    public function setPrimaryCurrency(CurrencyEnum $currency): AccountCurrency
    {
        if (!$oldPrimary = $this->getPrimaryCurrency()) {
            throw new Exception('Account don\'t have this currency');
        }

        $newPrimary = $this->getCurrency($currency);

        $newPrimary = DB::transaction(function () use ($oldPrimary, $newPrimary) {
            $oldPrimary->makeAdditional();

            $newPrimary->makePrimary();

            return $newPrimary;
        });

        return $newPrimary;
    }

    public function increaseBalance(CurrencyEnum $currency, float $value): AccountCurrency
    {
        return $this->changeBalance($currency, $value);
    }

    public function decreaseBalance(CurrencyEnum $currency, float $value): AccountCurrency
    {
        return $this->changeBalance($currency, $value, false);
    }

    public function changeBalance(CurrencyEnum $currency, float $value, bool $increase = true): AccountCurrency
    {
        if (!$accountCurrency = $this->getCurrency($currency)) {
            throw new Exception('Account don\'t have this currency');
        }

        if ($value <= 0) {
            throw new Exception('Value should be more than 0');
        }

        $accountCurrency = DB::transaction(function () use ($accountCurrency, $value, $increase) {
            if ($increase === false) {
                $value = $value * -1;
            }

            $accountCurrency->changeValue($value);

            Operation::factory()->create([
                'account_currency_id' => $accountCurrency->id,
                'value' => $value
            ]);

            return $accountCurrency;
        });

        return $accountCurrency;
    }

    public function getCurrency(CurrencyEnum $currency): ?AccountCurrency
    {
        /** @var AccountCurrency */
        return $this->currencies()
            ->where('currency_id', '=', $currency)
            ->first();
    }


    public function getPrimaryCurrency(): ?AccountCurrency
    {
        /** @var AccountCurrency */
        return $this->currencies()
            ->where('is_primary', '=', true)
            ->first();
    }

    public function getAdditionalCurrencies(): ?Collection
    {
        return $this->currencies()
            ->where('is_primary', '=', false)
            ->get();
    }

    /**
     * @param CurrencyEnum|null $currency
     * @return array
     * // TODO: change returning array to DTO and add methods co converting it to different currencies
     */
    public function getBalance(CurrencyEnum $currency = null): array
    {
        if ($currency === null) {
            return $this->getAggregatedBalance();
        }

        $accountCurrency = $this->getCurrency($currency);

        // This array can be refactored to DTO for more convenient interaction with the amounts of money
        return [
            'currency' => $accountCurrency->currency_id,
            'value' => $accountCurrency->current_value,
        ];
    }


    public function getAggregatedBalance(): array
    {
        /** @var AccountCurrency $primary */
        $primary = $this->getPrimaryCurrency();

        $total = $primary->current_value;

        foreach ($this->getAdditionalCurrencies() as $additionalCurrency) {
            /** @var AccountCurrency $additionalCurrency */
            $total += $additionalCurrency->convertToCurrency($primary->currency_id);
        }

        // This array can be refactored to DTO for more convenient interaction with the amounts of money
        return [
            'currency' => $primary->currency_id,
            'value' => $total,
        ];
    }

    public function disableCurrency(CurrencyEnum $currency): Account
    {
        if (!$accountCurrency = $this->getCurrency($currency)) {
            throw new Exception('Account don\'t have this currency');
        }

        $primaryCurrency = $this->getPrimaryCurrency();

        if ($primaryCurrency == $accountCurrency) {
            throw new Exception('You cannot disable primary currency!');
        }

        if ($accountCurrency->current_value != 0) {
            $this->decreaseBalance($currency, $accountCurrency->current_value);

            $changedAmount = CurrencyRate::convertToCurrency(
                $currency,
                $primaryCurrency->currency_id,
                $accountCurrency->current_value
            );

            $this->increaseBalance($primaryCurrency->currency_id, $changedAmount);
        }

        $accountCurrency->delete();

        return $this;
    }
}
