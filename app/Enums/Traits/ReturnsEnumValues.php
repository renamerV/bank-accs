<?php

namespace App\Enums\Traits;

trait ReturnsEnumValues
{
    public static function all(): array
    {
        $reflection = new \ReflectionClass(self::class);

        return array_values($reflection->getConstants());
    }

    public static function contains(mixed $value): bool
    {
        return in_array($value, self::all(), true);
    }
}
