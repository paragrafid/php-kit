<?php

namespace Tests\Unit;

use SalimId\Kit\Conversion;
use PHPUnit\Framework\TestCase;
use stdClass;

class ConversionTest extends TestCase
{
    public function testCanToBoolean()
    {
        $c = new class {
            public function __toString()
            {
                return '';
            }
        };

        $this->assertTrue(Conversion::toBoolean([0]));
        $this->assertFalse(Conversion::toBoolean([]));
        $this->assertTrue(Conversion::toBoolean(new stdClass));
        $this->assertFalse(Conversion::toBoolean(new $c));
        $this->assertTrue(Conversion::toBoolean('Hello'));
        $this->assertFalse(Conversion::toBoolean(''));
        $this->assertFalse(Conversion::toBoolean(' '));
    }
}
