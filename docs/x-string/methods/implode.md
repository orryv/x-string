# XString::implode()

## Table of Contents
- [XString::implode()](#xstringimplode)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Join fragments without glue](#join-fragments-without-glue)
    - [Join with a custom separator](#join-with-a-custom-separator)
    - [Join `Newline`, `HtmlTag`, and string fragments](#join-newline-htmltag-and-string-fragments)
    - [An empty array results in an empty string](#an-empty-array-results-in-an-empty-string)
    - [Invalid elements trigger an exception](#invalid-elements-trigger-an-exception)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public static function implode(array $data, string $glue = ''): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Static | ✓ | Public |

## Description

Combines an array of fragments into a single string, optionally inserting a glue string between each fragment. Each fragment may
be a plain string or any `Stringable` implementation such as `Newline`, `HtmlTag`, or `Regex`. The result is returned in a new immutable
`XString` instance.

**Algorithm overview:**

- Iterate over `$data`, normalising each fragment via the same logic used by [`XString::new()`](./new.md).
- Concatenate the fragments with the optional `$glue` between them.
- Wrap the combined string into a new `XString`.
- Time complexity: **O(n)** with respect to the number of fragments; Space complexity: **O(total fragment length)**.

## Important notes and considerations

- **Accepts stringables.** Any object implementing `Stringable` (including `Newline`, `HtmlTag`, and `Regex`) is accepted.
- **Immutable result.** The input array is not modified and a brand-new `XString` is returned.
- **Glue defaults to empty.** When `$glue` is omitted, fragments are concatenated directly.

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$data` | — | `array<Newline\|HtmlTag\|Regex\|Stringable\|string>` | Fragments to join together. Each element must be convertible to string. |
| `$glue` | `''` | `string` | Optional separator inserted between fragments. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | New `XString` containing the concatenated result. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | Any element in `$data` is not a string or `Stringable` instance. |

## Examples

### Join fragments without glue

<!-- test:implode-no-glue -->
```php
use Orryv\XString;

$result = XString::implode(['foo', 'bar', 'baz']);
#Test: self::assertSame('foobarbaz', (string) $result);
```

### Join with a custom separator

<!-- test:implode-with-glue -->
```php
use Orryv\XString;

$result = XString::implode(['apples', 'bananas', 'cherries'], ', ');
#Test: self::assertSame('apples, bananas, cherries', (string) $result);
```

### Join `Newline`, `HtmlTag`, and string fragments

<!-- test:implode-with-newline -->
```php
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;
use Orryv\XString;

$fragments = [
    'Line 1',
    Newline::new(),
    'Line 2',
    HtmlTag::new('br', true),
    Newline::new("\r\n"),
    'Line 3',
];
$result = XString::implode($fragments);
#Test: self::assertSame("Line 1\nLine 2<br />\r\nLine 3", (string) $result);
```

### An empty array results in an empty string

<!-- test:implode-empty -->
```php
use Orryv\XString;

$result = XString::implode([]);
#Test: self::assertSame('', (string) $result);
```

### Invalid elements trigger an exception

<!-- test:implode-invalid-fragment -->
```php
use InvalidArgumentException;
use Orryv\XString;

#Test: $this->expectException(InvalidArgumentException::class);
XString::implode(['ok', 123]);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::implode` | `public static function implode(array $data, string $glue = ''): self` — Join fragments (strings or Stringables, including HtmlTag) with an optional glue. |
