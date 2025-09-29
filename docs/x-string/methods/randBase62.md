# XString::randBase62()

## Table of Contents
- [XString::randBase62()](#xstringrandbase62)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Generate a Base62 identifier](#generate-a-base62-identifier)
    - [Base62 output mixes digits, uppercase, and lowercase characters](#base62-output-mixes-digits-uppercase-and-lowercase-characters)
    - [Reject invalid lengths](#reject-invalid-lengths)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public static function randBase62(int $length): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Static | ✓ | Public |

## Description

Produces a cryptographically secure random string using the Base62 alphabet (`0-9`, `A-Z`, `a-z`). The characters are sampled
uniformly and the resulting text is wrapped in a new immutable `XString` instance.

**Algorithm overview:**

- Validates `$length >= 1`.
- Delegates to [`XString::rand()`](./rand.md) with the 62-character alphanumeric alphabet.
- Returns the sampled characters wrapped in a new `XString`.
- Time complexity: **O(length)**; Space complexity: **O(length)**.

## Important notes and considerations

- **Digits first, then letters.** The internal alphabet is ordered `0-9`, `A-Z`, `a-z`, matching a common Base62 convention.
- **Uniform randomness.** `random_int()` ensures each Base62 symbol is equally likely.
- **Immutability.** Every invocation returns a brand-new `XString` instance.

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$length` | — | `int` | Number of Base62 characters to generate. Must be ≥ 1. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` containing the random Base62 characters. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `Orryv\XString\Exceptions\InvalidLengthException` | `$length < 1`. |

## Examples

### Generate a Base62 identifier

<!-- test:rand-base62-basic -->
```php
use Orryv\XString;

$token = XString::randBase62(20);
#Test: self::assertSame(20, $token->length());
#Test: self::assertMatchesRegularExpression('/^[0-9A-Za-z]{20}$/', (string) $token);
```

### Base62 output mixes digits, uppercase, and lowercase characters

<!-- test:rand-base62-character-diversity -->
```php
use Orryv\XString;

$token = XString::randBase62(60);
$characters = str_split((string) $token);
#Test: self::assertGreaterThanOrEqual(1, count(array_intersect($characters, range('0', '9'))));
#Test: self::assertGreaterThanOrEqual(1, count(array_intersect($characters, range('A', 'Z'))));
#Test: self::assertGreaterThanOrEqual(1, count(array_intersect($characters, range('a', 'z'))));
```

### Reject invalid lengths

<!-- test:rand-base62-invalid-length -->
```php
use Orryv\XString;
use Orryv\XString\Exceptions\InvalidLengthException;

#Test: $this->expectException(InvalidLengthException::class);
XString::randBase62(0);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::randBase62` | `public static function randBase62(int $length): self` — Generate a secure random Base62 (0-9, A-Z, a-z) string. |
