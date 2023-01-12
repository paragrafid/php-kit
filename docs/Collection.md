# Collection

Collection is a class for dealing with arrays.

## Sorting

```
use Paragraf\Kit\Collection;

$numbers = new Collection([4, 3, 5, 1, 2]);
$students = new Collection([
    ['name' => 'John', 'age' => 24],
    ['name' => 'Ana', 'age' => 23],
    ['name' => 'Adam', 'age' => 27],
    ['name' => 'Sophia', 'age' => 22],
]);

$sortedNumbers = $numbers->sort();
// [1, 2, 3, 4, 5]

$sortedStudents = $students->sortBy('age')->pluck('name')->values();
// ['Sophia', 'Ana', 'John', 'Adam']
```