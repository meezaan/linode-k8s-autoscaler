<?php

namespace AutoScaler\Helper;

class Resource
{
    public static function getCpuMilliValue(string $value): int
    {
        // If the string m exists, we have millivalues
        if (str_contains($value, 'm')) {
            return self::getNumericValue($value);
        }

        // If it's a decimal value or a number greater less than 5, we must have specified number of CPU cores and not actual millivalues
        if (self::isDecimal($value) || (is_numeric($value) && ( (int) $value <= 5))) {
            // Convert to millivalues and return
            return (int) $value * 1000;
        }

    }

    public static function getMemoryBytes(string $value): int
    {
        if (str_contains($value, 'Gi')) {
             return self::getNumericValue($value) * 1000;
        }
        // Else assume Mi is specified
        return self::getNumericValue($value);

    }

    public static function getNumericValue(string $string): int
    {
        return (int) filter_var($string,FILTER_SANITIZE_NUMBER_INT);
    }

    public static function isDecimal(string $value): bool
    {
        return is_numeric( $value ) && floor( $value ) != $value;
    }

}