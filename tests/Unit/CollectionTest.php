<?php

namespace Tests\Unit;

use Paragraf\Kit\Collection;
use Paragraf\Kit\Comparison;
use PHPUnit\Framework\TestCase;
use stdClass;

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

        $this->assertEquals([4, 5, 1, 7, 6], $items->values()->items());
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

    public function testIsLinear()
    {
        $this->assertTrue(Collection::isLinear([]));
        $this->assertTrue(Collection::isLinear([3, 2, 1]));
        $this->assertTrue(Collection::isLinear(['foo', 'bar', 'baz']));
        $this->assertTrue(Collection::isLinear([0, 'foo' => 'bar', 3, 1, 2]));
        $this->assertTrue(Collection::isLinear([0, new stdClass, 1, 2]));
        $this->assertFalse(Collection::isLinear([0, [3], 1, 2]));
        $this->assertFalse(Collection::isLinear([0, ['foo' => 'bar'], 3, 1, 2]));
    }

    public function testCanWhereIn()
    {
        $items = (new Collection([
            'foo' => 1,
            'bar' => 2,
            'baz' => 3,
        ]))->whereIn(['bar'])->values()->items();

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
        ]))->whereIn('category', ['B'])->values()->items();

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
        ]))->whereNotIn(['bar'])->values()->items();

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
        ]))->whereNotIn('category', ['B'])->values()->items();

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

        $items = (new Collection([4, 5, 3, 1, 2]))->sort()->values()->items();

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

        $items = (new Collection([4, 5, 3, 1, 2]))->reverse()->values()->items();

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

    public function testCanKeyBy()
    {
        $items = new Collection([
            [
                'email' => 'ana@example.com',
                'name' => 'Ana',
            ],
            [
                'email' => 'adam@example.com',
                'name' => 'Adam',
            ],
            [
                'email' => 'george@example.com',
                'name' => 'George',
            ],
            [
                'email' => 'adam@example.com',
                'name' => 'Adam J.',
            ],
        ]);

        $this->assertEquals([
            'ana@example.com' => 'Ana',
            'george@example.com' => 'George',
            'adam@example.com' => 'Adam J.',
        ], $items->keyBy('email')->pluck('name')->items());
    }

    public function testCanReduce()
    {
        $collection = new Collection([1, 2, 3]);

        $this->assertEquals(6, $collection->reduce(fn ($carry, $item) => $carry + $item));
        $this->assertEquals(11, $collection->reduce(fn ($carry, $item) => $carry + $item, 5));
    }

    public function testCanPrepend()
    {
        $items = (new Collection([1, 2, 3]))->prepend(5, 6)->values()->items();

        $this->assertEquals([5, 6, 1, 2, 3], $items);
    }

    public function testCanAppend()
    {
        $items = (new Collection([1, 2, 3]))->append(5, 6)->values()->items();

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

    public function testCanUnique()
    {
        $this->assertEquals([
            0 => 1,
            1 => 2,
            3 => 4,
            4 => 3,
            5 => 5,
        ], (new Collection([1, 2, 2, 4, 3, 5, 3, 1]))->unique()->items());

        $people = [
            [
                'name' => 'John',
                'sex' => 'male',
                'age' => 20,
            ],
            [
                'name' => 'Bob',
                'sex' => 'male',
                'age' => 20,
            ],
            (object) [
                'name' => 'Adam',
                'sex' => 'male',
                'age' => 24,
            ],
            [
                'name' => 'Ana',
                'sex' => 'female',
                'age' => 23,
            ],
            [
                'name' => 'Alice',
                'sex' => 'female',
                'age' => 26,
            ],
            [
                'name' => 'George',
                'sex' => 'male',
                'age' => 25,
            ],
            [
                'name' => 'Sophia',
                'sex' => 'female',
                'age' => 25,
            ],
            (object) [
                'name' => 'George',
                'sex' => 'male',
                'age' => 25,
            ],
            [
                'name' => 'Juan',
                'sex' => 'male',
                'age' => 27,
            ],
        ];
        $people = new Collection($people);

        $this->assertEquals([
            'John',
            'Bob',
            'Adam',
            'Ana',
            'Alice',
            'George',
            'Sophia',
            'Juan',
        ], $people->unique()->pluck('name')->values()->items());

        $this->assertEquals([
            'John',
            'Adam',
            'Ana',
            'Alice',
            'George',
            'Juan',
        ], $people->unique('age')->pluck('name')->values()->items());

        $this->assertEquals([
            'John',
            'Adam',
            'Ana',
            'Alice',
            'George',
            'Sophia',
            'Juan',
        ], $people->unique(function ($person) {
            if (is_object($person)) {
                return $person->sex . $person->age;
            }

            return $person['sex'] . $person['age'];
        })->pluck('name')->values()->items());
    }

    public function testCanBeConvertedIntoString()
    {
        $this->assertEquals('{"foo":123,"bar":[4,5,6],"baz":{"foo":"1"}}', (string) (new Collection([
            'foo' => 123,
            'bar' => [4, 5, 6],
            'baz' => [
                'foo' => '1',
            ],
        ])));
    }

    public function testCanSortNatural()
    {
        $this->assertEquals(
            [
                'img1.png',
                'img2.png',
                'img10.png',
                'img12.png',
            ],
            (new Collection([
                'img1.png',
                'img10.png',
                'img12.png',
                'img2.png',
            ]))
                ->sort([Comparison::class, 'stringNaturalOrder'])
                ->values()
                ->items()
        );

        $this->assertEquals(
            [
                'Bob',
                'Adrian',
                'Alice',
                'George',
            ],
            (new Collection([
                [
                    'name' => 'Adrian',
                    'file' => 'img2.png',
                ],
                [
                    'name' => 'George',
                    'file' => 'img12.png',
                ],
                [
                    'name' => 'Alice',
                    'file' => 'img10.png',
                ],
                [
                    'name' => 'Bob',
                    'file' => 'img1.png',
                ],
            ]))
                ->sortBy('file', [Comparison::class, 'stringNaturalOrder'])
                ->pluck('name')
                ->values()
                ->items()
        );
    }
}
