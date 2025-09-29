# XString::randHex()

## Table of Contents
- [XString::randHex()](#xstringrandhex)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Generate a 32-character hexadecimal token](#generate-a-32-character-hexadecimal-token)
    - [Hex output can be consumed as binary data](#hex-output-can-be-consumed-as-binary-data)
    - [Reject invalid lengths](#reject-invalid-lengths)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public static function randHex(int $length): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Static | ✓ | Public |

## Description

Creates a cryptographically secure random hexadecimal string of the requested length. Internally the method samples uniformly
from the lowercase hexadecimal alphabet (`0-9`, `a-f`) using PHP's `random_int()` and returns the resulting text wrapped in a new
immutable `XString` instance.

**Algorithm overview:**

- Validates `$length >= 1`.
- Delegates to [`XString::rand()`](./rand.md) with the hexadecimal character pool.
- Wraps the generated string inside a fresh `XString`.
- Time complexity: **O(length)**; Space complexity: **O(length)**.

## Important notes and considerations

- **Lowercase output.** Hex digits are always lowercase. Use `->toUpper()` on the result if uppercase is required.
- **Uniform distribution.** Every hexadecimal digit has equal probability thanks to `random_int()`.
- **Immutability.** Returns a brand-new `XString` value; no global state is modified.

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$length` | — | `int` | Number of hexadecimal characters to generate. Must be ≥ 1. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` containing the random hexadecimal string. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `Orryv\XString\Exceptions\InvalidLengthException` | `$length < 1`. |

## Examples

### Generate a 32-character hexadecimal token

<!-- test:rand-hex-basic -->
```php
use Orryv\XString\XString;

$token = XString::randHex(32);
#Test: self::assertSame(32, $token->length());
#Test: self::assertMatchesRegularExpression('/^[0-9a-f]{32}$/', (string) $token);
```

### Hex output can be consumed as binary data

<!-- test:rand-hex-hex2bin -->
```php
use Orryv\XString\XString;

$token = XString::randHex(16);
$bytes = hex2bin((string) $token);
#Test: self::assertSame(16, $token->length());
#Test: self::assertNotFalse($bytes);
#Test: self::assertSame(8, strlen($bytes));
```

### Reject invalid lengths

<!-- test:rand-hex-invalid-length -->
```php
use Orryv\XString\XString;
use Orryv\XString\Exceptions\InvalidLengthException;

#Test: $this->expectException(InvalidLengthException::class);
XString::randHex(0);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::randHex` | `public static function randHex(int $length): self` — Generate a secure lowercase hexadecimal string of the requested length. |
