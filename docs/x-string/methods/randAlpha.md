# XString::randAlpha()

## Table of Contents
- [XString::randAlpha()](#xstringrandalpha)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Generate mixed-case alphabetic strings](#generate-mixed-case-alphabetic-strings)
    - [Reject invalid lengths](#reject-invalid-lengths)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public static function randAlpha(int $length): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Static | ✓ | Public |

## Description

Generates a cryptographically secure random alphabetic string by sampling uniformly from both lowercase (`a`–`z`) and uppercase
(`A`–`Z`) ASCII letters. The resulting text is wrapped in a new immutable `XString` instance.

**Algorithm overview:**

- Validates `$length >= 1`.
- Uses `random_int()` to pick `$length` characters uniformly from the `[A-Za-z]` pool.
- Returns a new `XString` that encapsulates the generated sequence.
- Time complexity: **O(length)**; Space complexity: **O(length)**.

## Important notes and considerations

- **Alphabet only.** Digits and symbols are never included; use `rand()` or `randLower()`/`randUpper()` when other character sets
  are required.
- **Immutability.** The method always returns a new instance.
- **Security.** Built on top of `random_int()` for strong randomness.

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$length` | — | `int` | Number of alphabetic characters to generate. Must be ≥ 1. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Mixed-case alphabetic `XString` with the requested length. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `Orryv\XString\Exceptions\InvalidLengthException` | `$length < 1`. |

## Examples

### Generate mixed-case alphabetic strings

<!-- test:rand-alpha-basic -->
```php
use Orryv\XString\XString;

$token = XString::randAlpha(20);
#Test: self::assertSame(20, $token->length());
#Test: self::assertMatchesRegularExpression('/^[A-Za-z]{20}$/', (string) $token);
```

### Reject invalid lengths

<!-- test:rand-alpha-invalid-length -->
```php
use Orryv\XString\XString;
use Orryv\XString\Exceptions\InvalidLengthException;

#Test: $this->expectException(InvalidLengthException::class);
XString::randAlpha(0);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::randAlpha` | `public static function randAlpha(int $length): self` — Generate a secure mixed-case alphabetic string. |
