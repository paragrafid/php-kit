<?php

namespace Paragraf\Kit\Traits;

use Exception;

trait DisableSet
{
    /**
     * Magic set.
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set(string $name, $value): void
    {
        throw new Exception('Property modification is prohibited.');
    }
}
