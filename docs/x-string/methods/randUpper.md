# XString::randUpper()

## Table of Contents
- [XString::randUpper()](#xstringrandupper)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Uppercase letters only](#uppercase-letters-only)
    - [Allow digits alongside uppercase characters](#allow-digits-alongside-uppercase-characters)
    - [Reject invalid lengths](#reject-invalid-lengths)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public static function randUpper(int $length, bool $include_numbers = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Static | ✓ | Public |

## Description

Produces a cryptographically secure random uppercase string. By default the method samples uniformly from the ASCII uppercase
alphabet (`A`–`Z`). When `$include_numbers` is `true`, the digits `0`–`9` are merged into the sampling pool. The generated text is
wrapped in a new immutable `XString` instance.

**Algorithm overview:**

- Validates `$length >= 1`.
- Selects the character pool: `[A-Z]` by default, optionally `[A-Z0-9]`.
- Uses `random_int()` to pick `$length` characters uniformly with replacement.
- Returns a new `XString` with the generated content.
- Time complexity: **O(length)**; Space complexity: **O(length)**.

## Important notes and considerations

- **Character set.** Only ASCII uppercase letters (and digits when enabled) are used, simplifying integration with code systems
  that expect uppercase tokens.
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
| `self` | ✓ | Uppercase (optionally alphanumeric) `XString` with the requested length. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `Orryv\XString\Exceptions\InvalidLengthException` | `$length < 1`. |

## Examples

### Uppercase letters only

<!-- test:rand-upper-basic -->
```php
use Orryv\XString;

$token = XString::randUpper(10);
#Test: self::assertSame(10, $token->length());
#Test: self::assertMatchesRegularExpression('/^[A-Z]{10}$/', (string) $token);
```

### Allow digits alongside uppercase characters

<!-- test:rand-upper-with-digits -->
```php
use Orryv\XString;

$token = XString::randUpper(14, true);
#Test: self::assertSame(14, $token->length());
#Test: self::assertMatchesRegularExpression('/^[A-Z0-9]{14}$/', (string) $token);
```

### Reject invalid lengths

<!-- test:rand-upper-invalid-length -->
```php
use Orryv\XString;
use Orryv\XString\Exceptions\InvalidLengthException;

#Test: $this->expectException(InvalidLengthException::class);
XString::randUpper(0);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::randUpper` | `public static function randUpper(int $length, bool $include_numbers = false): self` — Generate a secure uppercase string, optionally mixing in digits. |
