<?php

namespace Paragraf\Kit;

class Comparison
{
    /**
     * Compare string order.
     *
     * @param string|null $a
     * @param string|null $b
     * @return integer
     */
    public static function stringOrder(?string $a, ?string $b): int
    {
        $a = is_null($a) ? '' : $a;
        $b = is_null($b) ? '' : $b;

        $c = strcmp($a, $b);

        if ($c < 0) {
            return -1;
        }

        if ($c > 0) {
            return 1;
        }

        return 0;
    }

    /**
     * Compare string using natural order algorithm.
     *
     * @param string|null $a
     * @param string|null $b
     * @return integer
     */
    public static function stringNaturalOrder(?string $a, ?string $b): int
    {
        $a = is_null($a) ? '' : $a;
        $b = is_null($b) ? '' : $b;

        $c = strnatcmp($a, $b);

        if ($c < 0) {
            return -1;
        }

        if ($c > 0) {
            return 1;
        }

        return 0;
    }
}
