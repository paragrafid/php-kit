<?php

namespace Paragraf\Kit;

class Conversion
{
    /**
     * To boolean.
     *
     * @param mixed $item
     * @return boolean
     */
    public static function toBoolean($item): bool
    {
        if (is_array($item)) {
            return !empty($item);
        }

        if (is_object($item) && method_exists($item, '__toString')) {
            $item = (string) $item;
        }

        if (is_string($item)) {
            $item = trim($item);
        }

        return (bool) $item;
    }

    /**
     * To string.
     *
     * @param mixed $item
     * @return string
     */
    public static function toString($item): string
    {
        if (is_object($item) && method_exists($item, '__toString')) {
            return (string) $item;
        }

        if (is_array($item)) {
            $item = json_encode($item);

            return is_string($item) ? $item : '';
        }

        return (string) $item;
    }
}
