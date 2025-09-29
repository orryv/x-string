# XString::randBase64()

## Table of Contents
- [XString::randBase64()](#xstringrandbase64)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Create a 24-character Base64 token](#create-a-24-character-base64-token)
    - [All characters belong to the Base64 alphabet](#all-characters-belong-to-the-base64-alphabet)
    - [Reject invalid lengths](#reject-invalid-lengths)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public static function randBase64(int $length): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Static | ✓ | Public |

## Description

Generates a cryptographically secure random string using the canonical Base64 alphabet (`A-Z`, `a-z`, `0-9`, `+`, `/`). The
result is returned as a new immutable `XString` instance.

**Algorithm overview:**

- Validates `$length >= 1`.
- Delegates to [`XString::rand()`](./rand.md) with the 64-character Base64 alphabet.
- Wraps the sampled characters in a new `XString`.
- Time complexity: **O(length)**; Space complexity: **O(length)**.

## Important notes and considerations

- **Alphabet only.** The method samples Base64 alphabet characters only; it does not emit padding (`=`). When a padded Base64
  string is required, manually add the padding or use a proper encoder.
- **Uniform randomness.** Each symbol in the Base64 alphabet is equally likely thanks to `random_int()`.
- **Immutability.** Always returns a fresh `XString` instance.

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$length` | — | `int` | Number of Base64 characters to generate. Must be ≥ 1. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` containing the random Base64 characters. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `Orryv\XString\Exceptions\InvalidLengthException` | `$length < 1`. |

## Examples

### Create a 24-character Base64 token

<!-- test:rand-base64-basic -->
```php
use Orryv\XString\XString;

$token = XString::randBase64(24);
#Test: self::assertSame(24, $token->length());
#Test: self::assertMatchesRegularExpression('/^[A-Za-z0-9+\/]{24}$/', (string) $token);
```

### All characters belong to the Base64 alphabet

<!-- test:rand-base64-character-set -->
```php
use Orryv\XString\XString;

$token = XString::randBase64(8);
$alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
$overlap = strspn((string) $token, $alphabet);
#Test: self::assertSame(8, $overlap);
#Test: self::assertStringNotContainsString('=', (string) $token);
```

### Reject invalid lengths

<!-- test:rand-base64-invalid-length -->
```php
use Orryv\XString\XString;
use Orryv\XString\Exceptions\InvalidLengthException;

#Test: $this->expectException(InvalidLengthException::class);
XString::randBase64(0);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::randBase64` | `public static function randBase64(int $length): self` — Generate a secure random string from the Base64 alphabet (no padding). |
