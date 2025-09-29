# XString::rand()

## Table of Contents
- [XString::rand()](#xstringrand)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [10 random characters from 'abcdef'](#10-random-characters-from-abcdef)
    - [15 random alphanumeric characters (default)](#15-random-alphanumeric-characters-default)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:** 

```php
public static function rand(
    int $length, 
    string $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Static | ✓ | Public |

## Description

Generates a cryptographically secure random string of the requested length using the provided character set. The method returns a new immutable `XString` instance that encapsulates the generated string and preserves the class's mode (bytes, codepoints, or graphemes) and encoding.

**Algorithm overview:**

- Validates `$length >= 1` and that `$characters` contains at least one usable element (character/grapheme depending on mode).
- Builds an indexable pool from `$characters` respecting the class mode (bytes/codepoints/graphemes).
- Repeats `length` times:
  - Selects a secure random index via `random_int(0, poolSize - 1)`.
  - Appends the selected unit to the output buffer.
- Wraps the result into a new `XString` instance with the current encoding/mode.
- Time complexity: **O(length)**; Space complexity: **O(length)**.

## Important notes and considerations

- **Immutability.** Returns a new `XString` instance; the original data wrapper is never modified.
- **Randomness.** Uses PHP's `random_int()` for secure random number generation.
- **Mode and Encoding.** The generated string respects the mode (bytes, codepoints, graphemes) and encoding of the `XString` class.
- **Character set semantics.** In grapheme/codepoint modes with UTF‑8 input, selection operates on **grapheme clusters** (e.g., emoji and accented characters count as one unit). In byte mode, selection operates on raw bytes.
- **Uniformity.** Selection is uniform over the provided set; if the set contains duplicate elements, duplicates naturally increase their selection probability.
- **Performance tip.** For large `$length` and big character sets, precomputing the pool once avoids repeated splitting of `$characters`.

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$length` | — | `int` | Length of the random string to generate. Interpreted as a count of the current mode's unit (bytes, codepoints, or graphemes). Must be ≥ 1. |
| `$characters` | `'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'` | `string` | The allowed set used for generation. In Unicode modes, treated as a sequence of grapheme clusters. Must contain at least one usable unit. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | New immutable `XString` instance containing the generated random string. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `Orryv\XString\Exceptions\InvalidLengthException` | `$length` is less than 1. |
| `Orryv\XString\Exceptions\EmptyCharacterSetException` | `$characters` is empty or yields zero usable units for the current mode. |

## Examples

### 10 random characters from 'abcdef'

<!-- test:rand-abcdef -->
```php
use Orryv\XString\XString;

$x = XString::rand(10, 'abcdef');
#Test: self::assertEquals(10, $x->length());
#Test: self::assertMatchesRegularExpression('/^[abcdef]{10}$/', (string) $x);
```

### 15 random alphanumeric characters (default)

<!-- test:rand-default -->
```php
use Orryv\XString\XString;

$x = XString::rand(15);
#Test: self::assertEquals(15, $x->length());
#Test: self::assertMatchesRegularExpression('/^[0-9a-zA-Z]{15}$/', (string) $x);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::rand` | `public static function rand(int $length, string $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): self` — Generate a secure random string using the given character set, returning a new immutable `XString`. |
