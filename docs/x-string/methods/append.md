# XString::append()

## Table of Contents
- [XString::append()](#xstringappend)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Append a simple string](#append-a-simple-string)
    - [Append multiple fragments at once](#append-multiple-fragments-at-once)
    - [Appending `Newline`, `HtmlTag`, and `Regex` instances](#appending-newline-htmltag-and-regex-instances)
    - [Original instance remains unchanged](#original-instance-remains-unchanged)
    - [Invalid fragments raise an exception](#invalid-fragments-raise-an-exception)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function append(Newline|HtmlTag|Regex|Stringable|string|array $data): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Returns a new immutable `XString` with the provided fragment(s) appended to the current value. The method accepts plain strings,
objects implementing `Stringable` (including `Newline`, `HtmlTag`, and `Regex`), or an array of such values that will be concatenated in order.

## Important notes and considerations

- **Immutable chaining.** `append()` never mutates the existing instance; a new clone is returned with the appended data.
- **Array convenience.** Pass an array of fragments to append them in a single call.
- **Null-safe.** `null` fragments are treated as empty strings, mirroring [`XString::new()`](./new.md).

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$data` | — | `Newline\|HtmlTag\|Regex\|Stringable\|string\|array<Newline\|HtmlTag\|Regex\|Stringable\|string>` | Fragment(s) to append. Arrays are concatenated in order. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new immutable `XString` with the appended fragment(s). |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$data` (or an element inside it) cannot be converted to string. |

## Examples

### Append a simple string

<!-- test:append-basic -->
```php
use Orryv\XString;

$original = XString::new('Hello');
$updated = $original->append(', World');
#Test: self::assertSame('Hello, World', (string) $updated);
```

### Append multiple fragments at once

<!-- test:append-array -->
```php
use Orryv\XString;

$original = XString::new('foo');
$updated = $original->append(['-', 'bar', '-', 'baz']);
#Test: self::assertSame('foo-bar-baz', (string) $updated);
```

### Appending `Newline`, `HtmlTag`, and `Regex` instances

<!-- test:append-stringables -->
```php
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;
use Orryv\XString\Regex;
use Orryv\XString;

$original = XString::new('Pattern');
$updated = $original->append([
    Newline::new(),
    HtmlTag::new('span')->withClass('highlight'),
    Regex::new('/[a-z]+/i'),
]);
#Test: self::assertSame("Pattern\n<span class=\"highlight\"></span>/[a-z]+/i", (string) $updated);
```

### Original instance remains unchanged

<!-- test:append-immutability -->
```php
use Orryv\XString;

$original = XString::new('start');
$updated = $original->append(' end');
#Test: self::assertSame('start', (string) $original);
#Test: self::assertSame('start end', (string) $updated);
```

### Invalid fragments raise an exception

<!-- test:append-invalid -->
```php
use InvalidArgumentException;
use Orryv\XString;
use stdClass;

$original = XString::new('foo');
#Test: $this->expectException(InvalidArgumentException::class);
$original->append([new stdClass()]);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::append` | `public function append(Newline\|HtmlTag\|Regex\|Stringable\|string\|array $data): self` — Return a new instance with the provided fragment(s) appended. |
