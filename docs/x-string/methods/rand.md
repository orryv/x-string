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
    - [Unicode character sets are supported](#unicode-character-sets-are-supported)
    - [Invalid length throws an exception](#invalid-length-throws-an-exception)
    - [Empty character set throws an exception](#empty-character-set-throws-an-exception)
    - [Length remains stable across modes](#length-remains-stable-across-modes)
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

- Validates `$length >= 1` and that `$characters` contains at least one usable element (character or code point).
- Builds an indexable pool from `$characters` using Unicode-aware splitting (`preg_split('//u')`). When the input is not valid UTF-8, it falls back to raw byte splitting.
- Repeats `length` times:
  - Selects a secure random index via `random_int(0, poolSize - 1)`.
  - Appends the selected unit to the output buffer.
- Wraps the result into a new `XString` instance with the current encoding/mode.
- Time complexity: **O(length)**; Space complexity: **O(length)**.

## Important notes and considerations

- **Immutability.** Returns a new `XString` instance; the original data wrapper is never modified.
- **Randomness.** Uses PHP's `random_int()` for secure random number generation.
- **Mode and Encoding.** The resulting instance uses the library defaults (grapheme mode, UTF-8 encoding) but can be transformed afterwards with [`withMode()`](./withMode.md).
- **Character set semantics.** Unicode character sets are split into individual **code points**. Combining marks are treated as separate candidates—include precomposed characters if you need them to stay together.
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
use Orryv\XString;

$x = XString::rand(10, 'abcdef');
#Test: self::assertEquals(10, $x->length());
#Test: self::assertMatchesRegularExpression('/^[abcdef]{10}$/', (string) $x);
```

### 15 random alphanumeric characters (default)

<!-- test:rand-default -->
```php
use Orryv\XString;

$x = XString::rand(15);
#Test: self::assertEquals(15, $x->length());
#Test: self::assertMatchesRegularExpression('/^[0-9a-zA-Z]{15}$/', (string) $x);
```

### Unicode character sets are supported

<!-- test:rand-unicode -->
```php
use Orryv\XString;

$x = XString::rand(4, '🍎🍇🍉');

#Test: self::assertSame(4, $x->length());
#Test: self::assertMatchesRegularExpression('/^[🍎🍇🍉]{4}$/u', (string) $x);
```

### Invalid length throws an exception

<!-- test:rand-invalid-length -->
```php
use Orryv\XString;
use Orryv\XString\Exceptions\InvalidLengthException;

#Test: $this->expectException(InvalidLengthException::class);
XString::rand(0);
```

### Empty character set throws an exception

<!-- test:rand-empty-characters -->
```php
use Orryv\XString;
use Orryv\XString\Exceptions\EmptyCharacterSetException;

#Test: $this->expectException(EmptyCharacterSetException::class);
XString::rand(5, '');
```

### Length remains stable across modes

<!-- test:rand-length-across-modes -->
```php
use Orryv\XString;

$random = XString::rand(6, 'abcdef');
$bytes = $random->withMode('bytes');
$roundTrip = $bytes->withMode('graphemes');

#Test: self::assertSame(6, $random->length());
#Test: self::assertSame(6, $bytes->length());
#Test: self::assertSame((string) $random, (string) $roundTrip);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::rand` | `public static function rand(int $length, string $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): self` — Generate a secure random string using the given character set, returning a new immutable `XString`. |
