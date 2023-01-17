# Collection <!-- omit in toc --> 

The `Paragraf\Kit\Collection` class provides convenience for dealing with arrays of data.

## Table of Contents <!-- omit in toc --> 
- [Behavior](#behavior)
- [Available Methods](#available-methods)
  - [`append()`](#append)
  - [`count()`](#count)
  - [`filter()`](#filter)
  - [`first()`](#first)
  - [`get()`](#get)
  - [`hasItems()`](#hasitems)
  - [`implode()`](#implode)
  - [`items()`](#items)
  - [`keys()`](#keys)
  - [`keyBy()`](#keyby)
  - [`last()`](#last)
  - [`map()`](#map)
  - [`merge()`](#merge)
  - [`sort()`](#sort)
  - [`sortBy()`](#sortby)

## Behavior

The `Collection` class implements `ArrayAccess`, `Countable`, `IteratorAggregate`, and `JsonSerializable` interfaces. Thus, you can do the similar things when you are working with regular `array`.

``` php
$fruits = new Collection(['Orange', 'Apple', 'Banana']);

$fruits[0];

// Orange

$fruits[] = 'Mango';

$fruits->items();

// ['Orange', 'Apple', 'Banana', 'Mango']

count($fruits);

// 4

foreach ($fruits as $fruit) {
    // Do something...
}

json_encode($fruits);

// '["Orange","Apple","Banana","Mango"]'

```

## Available Methods

### `append()`

Adds an item at the end of the collection.

It does not create a new collection instance.

``` php
$numbers = new Collection([1, 2, 3]);

$numbers->append(4);
$numbers->append(5, 6, 7);

$numbers->items();

// [1, 2, 3, 4, 5, 6, 7]
```

### `count()`

Returns the number of items inside the collection.

``` php
$collection = new Collection(['foo', 'bar', 'baz']);

$collection->count();

// 3
```

### `filter()`

Filters the items. Without a callback given, it removes the falsy items determined by the `Paragraf\Kit\Conversion::toBoolean()`.

It returns a new collection instance.

``` php
$collection = new Collection(['foo', 0, '', ' ', 1]);

$collection->filter()->values()->items();

// ['foo', 1]

$collection->filter(fn ($item) => is_int($item))->values()->items();

// [0, 1]
```

### `first()`

Returns the first item of the collection.

``` php
$collection = new Collection(['foo', 'bar', 'baz']);

$collection->first();

// 'foo'
```

### `get()`

Gets an item at the specific offset.

``` php
$collection = new Collection(['foo', 'bar', 'baz']);

$collection->get(1);

// 'bar'
```

### `hasItems()`

Returns a `boolean` indicating that the collection has at least an item.

``` php
$collection = new Collection;

$collection->hasItems();

// false

$collection->append('orange');

$collection->hasItems();

// true
```

### `implode()`

Returns a string from the items.

``` php
$collection = new Collection(['foo', 'bar', 'baz']);

$collection->implode('-');

// 'foo-bar-baz'
```

### `items()`

Returns the items as an `array`.

``` php
$collection = new Collection(['foo', 'bar', 'baz']);

$collection->items();

// ['foo', 'bar', 'baz']

$collection->sort()->items();

// [1 => 'bar', 2 => 'baz', 0 => 'foo']
```

### `keys()`

Returns the keys of the items as an `array`.

``` php
$collection = new Collection(['foo', 'bar', 'baz']);

$collection->keys();

// [0, 1, 2]
```

### `keyBy()`

Creates a new collection instance with specific item keys.

``` php
$students = new Collection([
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

$keyed = $students->keyBy('email');

$plucked = $keyed->pluck('name');

$plucked->items();

// ['ana@example.com' => 'Ana', 'george@example.com' => 'George', 'adam@example.com' => 'Adam J.']
```

### `last()`

Returns the last item of the collection.

``` php
$collection = new Collection(['foo', 'bar', 'baz']);

$collection->last();

// 'baz'
```

### `map()`

Iterates over the items and returns a new collection instance of the mapped items.

``` php
$collection = new Collection(['foo', 'bar', 'baz']);

$collection->map(fn ($value, $key) => ($key + 1) . '-' . $value)->items();

// ['1-foo', '2-bar', '3-baz']
```

### `merge()`

Merge an `array` or a collection to the current collection.

It returns a new collection instances.

``` php
$a = new Collection(['foo']);

$b = new Collection(['bar']);

$c = $a->merge($b, ['baz']);

$c->items();

// ['foo', 'bar', 'baz']
```

### `sort()`

Sorts the items. Without a callback given, it compares the items using the `Paragraf\Kit\Comparison::stringOrder()`.

It returns a new collection instance.

``` php
$numbers = new Collection([4, 3, 5, 1, 2]);

$numbers->sort()->values()->items();

// [1, 2, 3, 4, 5]
```

You might want to see [`reverse()`](#reverse).

### `sortBy()`

Sorts the items by the item properties. Without a callback given, it compares the item properties using the `Paragraf\Kit\Comparison::stringOrder()`.

It returns a new collection instance.

``` php
$students = new Collection([
    ['name' => 'John', 'age' => 24],
    ['name' => 'Ana', 'age' => 23],
    ['name' => 'Adam', 'age' => 27],
    ['name' => 'Sophia', 'age' => 22],
]);

$students->sortBy('age')->pluck('name')->values()->items();

// ['Sophia', 'Ana', 'John', 'Adam']
```

You can even add a callback as the comparison algorithm. For an example, we use the natural order algorithm provided by the `Paragraf\Kit\Comparison` class.

``` php
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

// ['Bob', 'Adrian', 'Alice', 'George']
```

You might want to see [`reverse()`](#reverse).