# XString::randInt()

## Table of Contents
- [XString::randInt()](#xstringrandint)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Random numeric string with default digit range](#random-numeric-string-with-default-digit-range)
    - [Limit digits to a custom inclusive range](#limit-digits-to-a-custom-inclusive-range)
    - [Reject zero or negative lengths](#reject-zero-or-negative-lengths)
    - [Reject inverted numeric ranges](#reject-inverted-numeric-ranges)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public static function randInt(int $length, int $int_min = 0, int $int_max = 9): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Static | ✓ | Public |

## Description

Generates a cryptographically secure numeric string where every digit is chosen uniformly from the inclusive range
`[$int_min, $int_max]`. The resulting digits are concatenated in the order they were generated and wrapped in a new immutable
`XString` instance. The method is convenient for producing PIN codes, verification tokens, and other numeric identifiers.

**Algorithm overview:**

- Validates that `$length >= 1` and `$int_min <= $int_max`.
- Builds an array of allowed digits from `$int_min`…`$int_max` (each digit is cast to its string representation).
- For `$length` iterations, selects a random index via `random_int()` and appends the corresponding digit.
- Returns a new `XString` in the caller's default mode and encoding.
- Time complexity: **O(length)**; Space complexity: **O(length)**.

## Important notes and considerations

- **Digit interpretation.** `$int_min` and `$int_max` are clamped to the single-digit range `0-9`. Values outside this range are
  expected to be rejected by the implementation, ensuring predictable output width.
- **Uniformity.** Each position is sampled independently and uniformly across the allowed digit set.
- **Immutability.** The produced `XString` does not affect any existing instances.
- **Security.** Uses `random_int()` for cryptographically secure randomness.

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$length` | — | `int` | Number of digits to generate. Must be ≥ 1. |
| `$int_min` | `0` | `int` | Lower bound of the inclusive digit range. Must be ≤ `$int_max`. |
| `$int_max` | `9` | `int` | Upper bound of the inclusive digit range. Must be ≥ `$int_min`. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Numeric `XString` containing `$length` digits sampled from the requested range. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `Orryv\XString\Exceptions\InvalidLengthException` | `$length < 1`. |
| `\InvalidArgumentException` | `$int_min > $int_max` or the digit range falls outside `0-9`. |

## Examples

### Random numeric string with default digit range

<!-- test:rand-int-default -->
```php
use Orryv\XString\XString;

$pin = XString::randInt(6);
#Test: self::assertSame(6, $pin->length());
#Test: self::assertMatchesRegularExpression('/^[0-9]{6}$/', (string) $pin);
```

### Limit digits to a custom inclusive range

<!-- test:rand-int-custom-range -->
```php
use Orryv\XString\XString;

$digits = XString::randInt(8, 3, 7);
#Test: self::assertSame(8, $digits->length());
#Test: self::assertMatchesRegularExpression('/^[3-7]{8}$/', (string) $digits);
```

### Reject zero or negative lengths

<!-- test:rand-int-invalid-length -->
```php
use Orryv\XString\XString;
use Orryv\XString\Exceptions\InvalidLengthException;

#Test: $this->expectException(InvalidLengthException::class);
XString::randInt(0);
```

### Reject inverted numeric ranges

<!-- test:rand-int-invalid-range -->
```php
use Orryv\XString\XString;

#Test: $this->expectException(\InvalidArgumentException::class);
XString::randInt(4, 9, 3);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::randInt` | `public static function randInt(int $length, int $int_min = 0, int $int_max = 9): self` — Generate a secure numeric string by sampling digits uniformly from the provided inclusive range. |
