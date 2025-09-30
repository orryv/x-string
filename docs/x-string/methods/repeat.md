# XString::repeat()

## Table of Contents
- [XString::repeat()](#xstringrepeat)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Repeat a greeting multiple times](#repeat-a-greeting-multiple-times)
    - [Zero repetitions yield an empty string](#zero-repetitions-yield-an-empty-string)
    - [Negative repetitions are rejected](#negative-repetitions-are-rejected)
    - [Repeating multibyte graphemes](#repeating-multibyte-graphemes)
    - [Byte mode preserves raw length accounting](#byte-mode-preserves-raw-length-accounting)
    - [Original instance stays unchanged](#original-instance-stays-unchanged)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function repeat(int $times): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Creates a new immutable `XString` whose value consists of the original string repeated `$times` in sequence. The returned
instance inherits the current mode and encoding so follow-up operations continue to behave consistently.

## Important notes and considerations

- **Non-negative counts only.** Passing a negative repeat count raises an `InvalidArgumentException`.
- **Mode preserved.** The resulting `XString` keeps the same mode/encoding configuration as the source.
- **Efficient concatenation.** Internally leverages `str_repeat()` for predictable performance even with larger counts.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$times` | `int` | — | Number of times to repeat the current string. Must be zero or greater. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` containing the string repeated `$times` times. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$times` is negative. |

## Examples

### Repeat a greeting multiple times

<!-- test:repeat-basic -->
```php
use Orryv\XString;

$value = XString::new('Hi! ');
$result = $value->repeat(3);

#Test: self::assertSame('Hi! Hi! Hi! ', (string) $result);
```

### Zero repetitions yield an empty string

<!-- test:repeat-zero -->
```php
use Orryv\XString;

$value = XString::new('abc');
$result = $value->repeat(0);

#Test: self::assertSame('', (string) $result);
```

### Negative repetitions are rejected

<!-- test:repeat-negative -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('oops');

#Test: $this->expectException(InvalidArgumentException::class);
$value->repeat(-1);
```

### Repeating multibyte graphemes

<!-- test:repeat-grapheme -->
```php
use Orryv\XString;

$value = XString::new('🍰');
$result = $value->repeat(4);

#Test: self::assertSame('🍰🍰🍰🍰', (string) $result);
#Test: self::assertSame(4, $result->length());
```

### Byte mode preserves raw length accounting

<!-- test:repeat-byte-mode -->
```php
use Orryv\XString;

$value = XString::new("a\u{0301}")->withMode('bytes');
$result = $value->repeat(2);

#Test: self::assertSame("a\u{0301}a\u{0301}", (string) $result);
#Test: self::assertSame(6, $result->length());
```

### Original instance stays unchanged

<!-- test:repeat-immutable -->
```php
use Orryv\XString;

$value = XString::new('loop');
$result = $value->repeat(2);

#Test: self::assertSame('loop', (string) $value);
#Test: self::assertSame('looploop', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::repeat` | `public function repeat(int $times): self` — Duplicate the string `$times` times, enforcing a non-negative count. |
