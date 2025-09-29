# XString::prepend()

## Table of Contents
- [XString::prepend()](#xstringprepend)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Prepend a simple string](#prepend-a-simple-string)
    - [Prepend multiple fragments in order](#prepend-multiple-fragments-in-order)
    - [Prepending `Newline` and `Regex` instances](#prepending-newline-and-regex-instances)
    - [Original instance stays unchanged](#original-instance-stays-unchanged)
    - [Invalid fragments raise an exception](#invalid-fragments-raise-an-exception)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function prepend(Newline|Regex|Stringable|string|array $data): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Returns a new immutable `XString` with the provided fragment(s) inserted before the current value. Just like `append()`, the
method accepts plain strings, `Stringable` objects (including `Newline`/`Regex`), or an array of such fragments.

## Important notes and considerations

- **Immutable result.** The original instance is never mutated; a new instance is produced with the prepended content.
- **Ordering preserved.** When `$data` is an array, fragments are prepended in the order they are provided.
- **Null-safe input.** `null` fragments are treated as empty strings.

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$data` | — | `Newline\|Regex\|Stringable\|string\|array<Newline\|Regex\|Stringable\|string>` | Fragment(s) to prepend. Arrays are concatenated in order. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new immutable `XString` with the fragment(s) prepended. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$data` (or an element within it) cannot be converted to string. |

## Examples

### Prepend a simple string

<!-- test:prepend-basic -->
```php
use Orryv\XString;

$original = XString::new('world');
$updated = $original->prepend('hello ');
#Test: self::assertSame('hello world', (string) $updated);
```

### Prepend multiple fragments in order

<!-- test:prepend-array -->
```php
use Orryv\XString;

$original = XString::new('body');
$updated = $original->prepend(['<', 'div', '>']);
#Test: self::assertSame('<div>body', (string) $updated);
```

### Prepending `Newline` and `Regex` instances

<!-- test:prepend-stringables -->
```php
use Orryv\XString\Newline;
use Orryv\XString\Regex;
use Orryv\XString;

$original = XString::new('Content');
$updated = $original->prepend([
    Regex::new('/^Title:/'),
    Newline::new(),
]);
#Test: self::assertSame("/^Title:/\nContent", (string) $updated);
```

### Original instance stays unchanged

<!-- test:prepend-immutability -->
```php
use Orryv\XString;

$original = XString::new('core');
$updated = $original->prepend('pre-');
#Test: self::assertSame('core', (string) $original);
#Test: self::assertSame('pre-core', (string) $updated);
```

### Invalid fragments raise an exception

<!-- test:prepend-invalid -->
```php
use InvalidArgumentException;
use Orryv\XString;
use stdClass;

$original = XString::new('foo');
#Test: $this->expectException(InvalidArgumentException::class);
$original->prepend([new stdClass()]);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::prepend` | `public function prepend(Newline\|Regex\|Stringable\|string\|array $data): self` — Return a new instance with the provided fragment(s) placed before the current value. |
