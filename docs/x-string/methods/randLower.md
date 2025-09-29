# XString::randLower()

## Table of Contents
- [XString::randLower()](#xstringrandlower)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Lowercase letters only](#lowercase-letters-only)
    - [Allow digits alongside lowercase characters](#allow-digits-alongside-lowercase-characters)
    - [Reject invalid lengths](#reject-invalid-lengths)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public static function randLower(int $length, bool $include_numbers = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Static | ✓ | Public |

## Description

Produces a cryptographically secure random lowercase string. By default the method samples uniformly from the ASCII lowercase
alphabet (`a`–`z`). When `$include_numbers` is `true`, the digits `0`–`9` are merged into the sampling pool. The generated text is
wrapped in a new immutable `XString` instance.

**Algorithm overview:**

- Validates `$length >= 1`.
- Selects the character pool: `[a-z]` by default, optionally `[a-z0-9]`.
- Uses `random_int()` to pick `$length` characters uniformly with replacement.
- Returns a new `XString` with the generated content.
- Time complexity: **O(length)**; Space complexity: **O(length)**.

## Important notes and considerations

- **Character set.** Only ASCII lowercase letters (and digits when enabled) are used, ensuring compatibility with systems that
  demand restricted alphabets.
- **Immutability.** Results are independent objects; no existing `XString` is mutated.
- **Security.** Built on top of `random_int()` for strong randomness.

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$length` | — | `int` | Number of characters to generate. Must be ≥ 1. |
| `$include_numbers` | `false` | `bool` | When `true`, digits `0`–`9` are included in the character pool. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Lowercase (optionally alphanumeric) `XString` with the requested length. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `Orryv\XString\Exceptions\InvalidLengthException` | `$length < 1`. |

## Examples

### Lowercase letters only

<!-- test:rand-lower-basic -->
```php
use Orryv\XString\XString;

$token = XString::randLower(12);
#Test: self::assertSame(12, $token->length());
#Test: self::assertMatchesRegularExpression('/^[a-z]{12}$/', (string) $token);
```

### Allow digits alongside lowercase characters

<!-- test:rand-lower-with-digits -->
```php
use Orryv\XString\XString;

$token = XString::randLower(16, true);
#Test: self::assertSame(16, $token->length());
#Test: self::assertMatchesRegularExpression('/^[a-z0-9]{16}$/', (string) $token);
```

### Reject invalid lengths

<!-- test:rand-lower-invalid-length -->
```php
use Orryv\XString\XString;
use Orryv\XString\Exceptions\InvalidLengthException;

#Test: $this->expectException(InvalidLengthException::class);
XString::randLower(0);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::randLower` | `public static function randLower(int $length, bool $include_numbers = false): self` — Generate a secure lowercase string, optionally mixing in digits. |
