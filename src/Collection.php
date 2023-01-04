<?php

namespace Paragraf\Kit;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Exception;
use InvalidArgumentException;
use IteratorAggregate;
use JsonSerializable;

class Collection implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable
{
    /**
     * Items
     *
     * @var array<int|string, mixed>
     */
    protected array $items;

    /**
     * Constructor.
     *
     * @param array<int|string, mixed> $items
     */
    final public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Set an item.
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    /**
     * Check if an item exists.
     *
     * @param mixed $offset
     * @return boolean
     */
    public function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * Unset an item.
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }

    /**
     * Get an item.
     *
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }

    /**
     * Count items.
     *
     * @return integer
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Get items iterator.
     *
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * JSON encode behavior.
     *
     * @return array<int|string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->items;
    }

    /**
     * Get field from an item.
     *
     * @param object|array<int|string, mixed> $item
     * @param string $key
     * @return mixed
     */
    protected static function getItemField($item, $key)
    {
        if (is_array($item) && isset($item[$key])) {
            return $item[$key];
        }

        if (is_object($item)) {
            // We have not to direct access it to let magic getter kicks in.
            $value = $item->{$key} ?? null;

            if (isset($value)) {
                return $value;
            }
        }
    }

    /**
     * Check if collection has items.
     *
     * @return boolean
     */
    public function hasItems()
    {
        return !empty($this->items);
    }

    /**
     * Check if given array is associative.
     *
     * @param array<mixed> $arr
     * @return boolean
     */
    public static function isAssoc(array $arr): bool
    {
        if ([] === $arr) {
            return false;
        }

        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * Check if given array is linear.
     *
     * @param array<mixed> $arr
     * @return boolean
     */
    public static function isLinear(array $arr): bool
    {
        return count($arr) === count($arr, COUNT_RECURSIVE);
    }

    /**
     * Take items from collection.
     *
     * @param integer $offset
     * @param integer $length
     * @return static
     */
    public function take(int $offset, int $length = 1)
    {
        return new static(array_slice($this->items, $offset, $length));
    }

    /**
     * Get item on a specific offset.
     *
     * @param integer $offset
     * @return mixed
     */
    public function get(int $offset)
    {
        $items = $this->take($offset)->items();

        return array_pop($items);
    }

    /**
     * Get first item.
     *
     * @return mixed
     */
    public function first()
    {
        return $this->get(0);
    }

    /**
     * Get last item.
     *
     * @return mixed
     */
    public function last()
    {
        return $this->get(-1);
    }

    /**
     * Get random item.
     *
     * @return mixed
     */
    public function random()
    {
        return $this->shuffle()->first();
    }

    /**
     * Add values from the beginning.
     *
     * @param mixed ...$values
     * @return static
     */
    public function prepend(...$values)
    {
        array_unshift($this->items, ...$values);

        return $this;
    }

    /**
     * Add values from the end.
     *
     * @param mixed ...$values
     * @return static
     */
    public function append(...$values)
    {
        array_push($this->items, ...$values);

        return $this;
    }

    /**
     * Merge items.
     *
     * @param mixed ...$items
     * @return static
     */
    public function merge(...$items)
    {
        foreach ($items as &$item) {
            if (!$item instanceof static && !is_array($item)) {
                throw new InvalidArgumentException('Invalid arguments.');
            }

            if ($item instanceof static) {
                $item = $item->items();
            }
        }

        return new static(array_merge($this->items, ...$items));
    }

    /**
     * Map items.
     *
     * @param callable $callback
     * @return static
     */
    public function map(callable $callback)
    {
        $mapped = [];

        foreach ($this->items as $key => $item) {
            $mapped[$key] = $callback($item, $key);
        }

        return new static($mapped);
    }

    /**
     * Filter items.
     *
     * @param callable|null $callback
     * @return static
     */
    public function filter(?callable $callback = null)
    {
        $callback = $callback ?? [Conversion::class, 'toBoolean'];

        $filtered = [];

        foreach ($this->items as $key => $item) {
            if ($callback($item, $key)) {
                $filtered[$key] = $item;
            }
        }

        return new static($filtered);
    }

    /**
     * Remove duplicates.
     *
     * @param mixed $uniqueness Key for associative collection or a callback returning a uniqueness.
     * @return static
     */
    public function unique($uniqueness = null)
    {
        if (empty($uniqueness)) {
            $uniqueness = [Conversion::class, 'toString'];
        }

        if (!is_callable($uniqueness) && !is_string($uniqueness)) {
            throw new InvalidArgumentException('The uniqueness must be a string or a callable.');
        }

        $uniques = [];
        $items = [];

        foreach ($this->items as $key => $value) {
            $unique = is_callable($uniqueness) ? $uniqueness($value, $key) : $this->getItemField($value, $uniqueness);
            $unique = Conversion::toString($unique);

            if (in_array($unique, $uniques)) {
                continue;
            }

            $uniques[] = $unique;
            $items[$key] = $value;
        }

        return new static($items);
    }

    /**
     * Filter items based on whereIn condition.
     *
     * @param mixed ...$args
     * @return static
     */
    public function whereIn(...$args)
    {
        if (!isset($args[0]) && !isset($args[1])) {
            throw new InvalidArgumentException('Invalid arguments.');
        }

        if (is_string($args[0])
            && is_array($args[1])
            && (!isset($args[2]) || is_bool($args[2]))
            && (!isset($args[3]) || is_bool($args[3]))
        ) {
            $key     = $args[0];
            $values  = $args[1];
            $strict  = (bool) ($args[2] ?? false);
            $negated = (bool) ($args[3] ?? false);

            $filter = function ($item) use ($key, $values, $strict, $negated) {
                $is = in_array(
                    $this->getItemField($item, $key),
                    $values,
                    $strict
                );

                return $negated ? !$is : $is;
            };
        } elseif (is_array($args[0])
            && (!isset($args[1]) || is_bool($args[1]))
            && (!isset($args[2]) || is_bool($args[2]))
        ) {
            $values  = $args[0];
            $strict  = (bool) ($args[1] ?? false);
            $negated = (bool) ($args[2] ?? false);

            $filter = function ($item, $key) use ($values, $strict, $negated) {
                $is = in_array($key, $values, $strict);

                return $negated ? !$is : $is;
            };
        } else {
            throw new InvalidArgumentException('Invalid arguments.');
        }

        return $this->filter($filter);
    }

    /**
     * Filter items based on whereNotIn condition.
     *
     * @param mixed ...$args
     * @return static
     */
    public function whereNotIn(...$args)
    {
        if (!isset($args[0])) {
            throw new InvalidArgumentException('Invalid arguments.');
        }

        // Set arguments.
        if (is_string($args[0])) {
            $args[1] = $args[1] ?? null;
            $args[2] = $args[2] ?? null;
            $args[3] = true;
        } else {
            $args[1] = $args[1] ?? null;
            $args[2] = true;
        }

        return $this->whereIn(...$args);
    }

    /**
     * Filter items based on where condition
     *
     * @param string $key
     * @param mixed $value
     * @param boolean $strict
     * @return static
     */
    public function where(string $key, $value, bool $strict = false)
    {
        if (is_callable($value)) {
            return $this->filter(function ($item) use ($key, $value) {
                return $value($this->getItemField($item, $key));
            });
        }

        return $this->whereIn($key, [$value], $strict);
    }

    /**
     * Pluck items.
     *
     * @param string $key
     * @return static
     */
    public function pluck(string $key)
    {
        return $this->map(function ($item) use ($key) {
            return $this->getItemField($item, $key);
        });
    }

    /**
     * Reverse items.
     *
     * @return static
     */
    public function reverse()
    {
        $items = $this->items;
        $items = array_reverse($items, true);

        return new static($items);
    }

    /**
     * Shuffle items.
     *
     * @return static
     */
    public function shuffle()
    {
        $items = $this->items;

        if (shuffle($items) === false) {
            throw new Exception('Cannot shuffle items.');
        }

        return new static($items);
    }

    /**
     * Sort items.
     *
     * @param ?callable $callback
     * @return static
     */
    public function sort(?callable $callback = null)
    {
        $items = $this->items;

        if ($callback) {
            uasort($items, $callback);
        } else {
            asort($items);
        }

        return new static($items);
    }

    /**
     * Sort items by keys.
     *
     * @param ?callable $callback
     * @return static
     */
    public function sortKeys(?callable $callback = null)
    {
        $items = $this->items;

        if ($callback) {
            uksort($items, $callback);
        } else {
            ksort($items);
        }

        return new static($items);
    }

    /**
     * Sort by item field.
     *
     * @param string $key
     * @param ?callable $callback
     * @return static
     */
    public function sortBy(string $key, ?callable $callback = null)
    {
        $items = $this->items;

        $callback = $callback ?? 'strcmp';

        return $this->sort(function ($before, $after) use ($key, $callback) {
            $valueBefore = $this->getItemField($before, $key);
            $valueAfter = $this->getItemField($after, $key);

            if ($callback === 'strcmp') {
                $valueBefore = is_null($valueBefore) ? '' : $valueBefore;
                $valueAfter = is_null($valueAfter) ? '' : $valueAfter;
            }

            return $callback($valueBefore, $valueAfter);
        });
    }

    /**
     * Implode items.
     *
     * @param string $separator
     * @return string
     */
    public function implode(string $separator = '')
    {
        return implode($separator, $this->items);
    }

    /**
     * Get items.
     *
     * @return array<int|string, mixed>
     */
    public function items()
    {
        return $this->items;
    }

    /**
     * Get keys.
     *
     * @return array<int, mixed>
     */
    public function keys()
    {
        return array_keys($this->items);
    }

    /**
     * Get values.
     *
     * @return array<int, mixed>
     */
    public function values()
    {
        return array_values($this->items);
    }

    /**
     * Get debug info.
     *
     * @return array<int|string, mixed>
     */
    public function __debugInfo(): array
    {
        return $this->items;
    }
}
