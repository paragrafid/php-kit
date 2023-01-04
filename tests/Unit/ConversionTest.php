<?php

namespace Tests\Unit;

use Paragraf\Kit\Conversion;
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

    public function testCanToString()
    {
        $c = new class {
            public function __toString()
            {
                return 'Foo';
            }
        };

        $this->assertEquals('[0]', Conversion::toString([0]));
        $this->assertEquals('[]', Conversion::toString([]));
        $this->assertEquals('[]', Conversion::toString(new stdClass));

        $o = new stdClass;
        $o->foo = 123;

        $this->assertEquals('{"foo":123}', Conversion::toString($o));

        $this->assertEquals('Foo', Conversion::toString(new $c));
        $this->assertEquals('1.231', Conversion::toString('1.231'));
        $this->assertEquals('5', Conversion::toString('5'));
        $this->assertEquals(' ', Conversion::toString(' '));
    }
}
