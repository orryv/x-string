# XString::insertAtInterval()

## Table of Contents
- [XString::insertAtInterval()](#xstringinsertatinterval)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Insert hyphens every three characters](#insert-hyphens-every-three-characters)
    - [Intervals work even when the length is uneven](#intervals-work-even-when-the-length-is-uneven)
    - [Grapheme mode respects emoji boundaries](#grapheme-mode-respects-emoji-boundaries)
    - [Byte mode operates on raw bytes](#byte-mode-operates-on-raw-bytes)
    - [You can insert complex fragments](#you-can-insert-complex-fragments)
    - [Empty strings remain unchanged](#empty-strings-remain-unchanged)
    - [Interval must be greater than zero](#interval-must-be-greater-than-zero)
    - [Original instance is unaffected](#original-instance-is-unaffected)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function insertAtInterval(Newline|HtmlTag|Regex|string $insert, int $interval): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Returns a new immutable `XString` where the provided `$insert` fragment is injected before every `$interval` characters, counted
according to the current mode. This makes it easy to format identifiers, credit-card numbers, or any other strings that require
regular separators.

## Important notes and considerations

- **Mode sensitive.** The interval is counted using the active mode—graphemes by default—which prevents splitting apart emoji or
  combining sequences.
- **Flexible inserts.** Accepts plain strings as well as `Newline`, `HtmlTag`, and `Regex` objects (anything stringable).
- **Validation.** `$interval` must be at least `1`; smaller values raise an `InvalidArgumentException`.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$insert` | `Newline\|HtmlTag\|Regex\|string` | — | Fragment to insert at each interval. |
| `$interval` | `int` | — | Positive number of characters (as defined by the current mode) between insertions. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` with the fragment inserted at the requested cadence. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$interval` is less than 1. |

## Examples

### Insert hyphens every three characters

<!-- test:insert-interval-basic -->
```php
use Orryv\XString;

$value = XString::new('123456789');
$result = $value->insertAtInterval('-', 3);

#Test: self::assertSame('123-456-789', (string) $result);
```

### Intervals work even when the length is uneven

<!-- test:insert-interval-uneven -->
```php
use Orryv\XString;

$value = XString::new('abcdefg');
$result = $value->insertAtInterval('.', 2);

#Test: self::assertSame('ab.cd.ef.g', (string) $result);
```

### Grapheme mode respects emoji boundaries

<!-- test:insert-interval-grapheme -->
```php
use Orryv\XString;

$value = XString::new('🍎🍐🍊🍋🍌');
$result = $value->insertAtInterval('|', 2);

#Test: self::assertSame('🍎🍐|🍊🍋|🍌', (string) $result);
```

### Byte mode operates on raw bytes

<!-- test:insert-interval-bytes -->
```php
use Orryv\XString;

$value = XString::new("a\u{0301}b")->withMode('bytes');
$result = $value->insertAtInterval('.', 1);

#Test: self::assertSame('612ecc2e812e62', bin2hex((string) $result));
```

### You can insert complex fragments

<!-- test:insert-interval-fragment -->
```php
use Orryv\XString\Newline;
use Orryv\XString;

$value = XString::new('line1line2line3');
$result = $value->insertAtInterval(Newline::new(), 5);

#Test: self::assertSame('line1' . PHP_EOL . 'line2' . PHP_EOL . 'line3', (string) $result);
```

### Empty strings remain unchanged

<!-- test:insert-interval-empty -->
```php
use Orryv\XString;

$value = XString::new('');
$result = $value->insertAtInterval('-', 3);

#Test: self::assertSame('', (string) $result);
```

### Interval must be greater than zero

<!-- test:insert-interval-invalid -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('error');

#Test: $this->expectException(InvalidArgumentException::class);
$value->insertAtInterval('*', 0);
```

### Original instance is unaffected

<!-- test:insert-interval-immutable -->
```php
use Orryv\XString;

$value = XString::new('ABCD');
$result = $value->insertAtInterval(':', 2);

#Test: self::assertSame('ABCD', (string) $value);
#Test: self::assertSame('AB:CD', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::insertAtInterval` | `public function insertAtInterval(Newline\|HtmlTag\|Regex|string $insert, int $interval): self` — Insert a fragment every `$interval` characters based on the active mode. |
