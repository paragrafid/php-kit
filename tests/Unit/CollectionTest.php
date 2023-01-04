<?php

namespace Tests\Unit;

use SalimId\Kit\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    public function testCanAccessOffset()
    {
        $items = new Collection([4, 5, 3, 1, 2]);

        // Get an item.
        $this->assertEquals(
            3,
            $items[2]
        );

        // Append an item.
        $items[] = 6;

        $this->assertEquals([4, 5, 3, 1, 2, 6], $items->items());

        // Set an item.
        $items[4] = 7;

        $this->assertEquals([4, 5, 3, 1, 7, 6], $items->items());

        // Remove an item.
        unset($items[2]);

        $this->assertEquals([4, 5, 1, 7, 6], $items->values());
    }

    public function testCanCount()
    {
        $this->assertEquals(
            5,
            (new Collection([4, 5, 3, 1, 2]))->count()
        );
    }

    public function testCanTurnedToJson()
    {
        $this->assertEquals(
            '[4,5,3,1,2]',
            json_encode(new Collection([4, 5, 3, 1, 2]))
        );
    }

    public function testHasItems()
    {
        $this->assertTrue((new Collection([4, 5, 3, 1, 2]))->hasItems());
        $this->assertTrue((new Collection([null]))->hasItems());
        $this->assertFalse((new Collection([]))->hasItems());
    }

    public function testIsAssoc()
    {
        $this->assertFalse(Collection::isAssoc([]));
        $this->assertFalse(Collection::isAssoc([3, 2, 1]));
        $this->assertFalse(Collection::isAssoc(['foo', 'bar', 'baz']));
        $this->assertTrue(Collection::isAssoc([0, 'foo' => 'bar', 3, 1, 2]));
    }

    public function testCanWhereIn()
    {
        $items = (new Collection([
            'foo' => 1,
            'bar' => 2,
            'baz' => 3,
        ]))->whereIn(['bar'])->values();

        $this->assertEquals([2], $items);

        $items = (new Collection([
            [
                'title' => 'Foo',
                'category' => 'A',
            ],
            [
                'title' => 'Bar',
            ],
            [
                'title' => 'Baz',
                'category' => 'B',
            ],
        ]))->whereIn('category', ['B'])->values();

        $this->assertEquals([
            [
                'title' => 'Baz',
                'category' => 'B',
            ]
        ], $items);
    }

    public function testCanWhereNotIn()
    {
        $items = (new Collection([
            'foo' => 1,
            'bar' => 2,
            'baz' => 3,
        ]))->whereNotIn(['bar'])->values();

        $this->assertEquals([1, 3], $items);


        $items = (new Collection([
            [
                'title' => 'Foo',
                'category' => 'A',
            ],
            [
                'title' => 'Bar',
            ],
            [
                'title' => 'Baz',
                'category' => 'B',
            ],
        ]))->whereNotIn('category', ['B'])->values();

        $this->assertEquals([
            [
                'title' => 'Foo',
                'category' => 'A',
            ],
            [
                'title' => 'Bar',
            ],
        ], $items);
    }

    public function testCanSort()
    {
        $items = (new Collection([4, 5, 3, 1, 2]))->sort()->items();

        $this->assertEquals([
            3 => 1,
            4 => 2,
            2 => 3,
            0 => 4,
            1 => 5,
        ], $items);

        $items = (new Collection([4, 5, 3, 1, 2]))->sort()->values();

        $this->assertEquals([1, 2, 3, 4, 5], $items);
    }

    public function testCanReverse()
    {
        $items = (new Collection([4, 5, 3, 1, 2]))->reverse()->items();

        $this->assertEquals([
            4 => 2,
            3 => 1,
            2 => 3,
            1 => 5,
            0 => 4
        ], $items);

        $items = (new Collection([4, 5, 3, 1, 2]))->reverse(false)->items();

        $this->assertEquals([2, 1, 3, 5, 4], $items);
    }

    public function testCanSortBy()
    {
        $items = (new Collection([
            [
                'title' => '5 June 2022',
                'date' => '2022-06-05',
            ],
            [
                'title' => '2 April 2022',
                'date' => '2022-04-02',
            ],
            [
                'title' => '5 August 2022',
                'date' => '2022-08-05',
            ],
            [
                'title' => 'No Date 1',
            ],
            [
                'title' => '1 January 2022',
                'date' => '2022-01-01',
            ],
            [
                'title' => '23 March 2022',
                'date' => '2022-03-23',
            ],
            [
                'title' => 'No Date 2',
            ],
        ]))->sortBy('date')->pluck('title')->implode(' - ');

        $this->assertEquals(
            'No Date 1 - No Date 2 - 1 January 2022 - 23 March 2022 - 2 April 2022 - 5 June 2022 - 5 August 2022',
            $items
        );
    }

    public function testCanPrepend()
    {
        $items = (new Collection([1, 2, 3]))->prepend(5, 6)->values();

        $this->assertEquals([5, 6, 1, 2, 3], $items);
    }

    public function testCanAppend()
    {
        $items = (new Collection([1, 2, 3]))->append(5, 6)->values();

        $this->assertEquals([1, 2, 3, 5, 6], $items);
    }

    public function testCanMerge()
    {
        $a = new Collection([1, 2, 3]);
        $b = new Collection([4, 5, 6]);
        $c = [7, 8, 9];

        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], $a->merge($b, $c)->items());
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], $a->merge($b)->merge($c)->items());

        $a = new Collection([
            'name' => 'John',
            'sex' => 'male',
        ]);

        $b = [
            'name' => 'Bob',
            'age' => 20,
        ];

        $c = new Collection([
            'weight' => 60,
        ]);

        $this->assertEquals([
            'name' => 'Bob',
            'sex' => 'male',
            'age' => 20,
        ], $a->merge($b)->items());

        $this->assertEquals([
            'name' => 'John',
            'sex' => 'male',
            'weight' => 60,
        ], $a->merge($c)->items());

        $this->assertEquals([
            'name' => 'Bob',
            'sex' => 'male',
            'age' => 20,
            'weight' => 60,
        ], $a->merge($b)->merge($c)->items());
    }
}