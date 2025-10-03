# XString::join()

## Table of Contents
- [XString::join()](#xstringjoin)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Join fragments with a separator](#join-fragments-with-a-separator)
    - [Alias behaves identically to `implode()`](#alias-behaves-identically-to-implode)
    - [Join `Newline`, `HtmlTag`, and string fragments](#join-newline-htmltag-and-string-fragments)
    - [Joining an empty array returns an empty string](#joining-an-empty-array-returns-an-empty-string)
    - [Invalid fragments trigger an exception](#invalid-fragments-trigger-an-exception)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public static function join(array $data, string $glue = ''): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Static | ✓ | Public |

## Description

`XString::join()` is a thin alias around [`XString::implode()`](./implode.md). It concatenates an array of fragments into a new
immutable `XString`, optionally inserting a glue string between each fragment. Fragments may be plain strings or any
`Stringable` value such as `Newline`, `HtmlTag`, or `Regex` instances.

## Important notes and considerations

- **Alias semantics.** This method is strictly equivalent to `XString::implode()` and exists for familiarity with PHP's native
  `join()` helper.
- **Accepts stringables.** Any fragment implementing `Stringable` (including `Newline`/`HtmlTag`/`Regex`) is supported.
- **Immutable result.** The input array is untouched; a brand-new `XString` is returned.

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$data` | — | `array<Newline\|HtmlTag\|Regex\|Stringable\|string>` | Fragments to concatenate. Each element must be convertible to string. |
| `$glue` | `''` | `string` | Optional separator inserted between fragments. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new immutable `XString` containing the concatenated result. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | Any fragment in `$data` is not a string or `Stringable`. |

## Examples

### Join fragments with a separator

<!-- test:join-basic -->
```php
use Orryv\XString;

$result = XString::join(['foo', 'bar', 'baz'], '-');
#Test: self::assertSame('foo-bar-baz', (string) $result);
```

### Alias behaves identically to `implode()`

<!-- test:join-alias -->
```php
use Orryv\XString;

$fragments = ['left', 'right'];
$implode = XString::implode($fragments, ' / ');
$join = XString::join($fragments, ' / ');
#Test: self::assertSame((string) $implode, (string) $join);
```

### Join `Newline`, `HtmlTag`, and string fragments

<!-- test:join-newline -->
```php
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;
use Orryv\XString;

$parts = [
    'Line 1',
    Newline::new(),
    'Line 2',
    HtmlTag::new('br', true),
    Newline::new("\r\n"),
    'Line 3',
];
$result = XString::join($parts);
#Test: self::assertSame('Line 1' . PHP_EOL . 'Line 2<br />' . "\r\n" . 'Line 3', (string) $result);
```

### Joining an empty array returns an empty string

<!-- test:join-empty -->
```php
use Orryv\XString;

$result = XString::join([]);
#Test: self::assertSame('', (string) $result);
```

### Invalid fragments trigger an exception

<!-- test:join-invalid -->
```php
use InvalidArgumentException;
use Orryv\XString;

#Test: $this->expectException(InvalidArgumentException::class);
XString::join(['ok', 123]);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::join` | `public static function join(array $data, string $glue = ''): self` — Alias of `implode()` that concatenates fragments (including HtmlTag instances) with an optional glue string. |
