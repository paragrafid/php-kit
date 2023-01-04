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
    public static function toBoolean($item)
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
}
