<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Operation
 * @property float $value
 * @property int $account_currency_id
 */
class Operation extends Model
{
    use HasFactory;
}
