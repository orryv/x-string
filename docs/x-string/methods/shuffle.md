# XString::shuffle()

## Table of Contents
- [XString::shuffle()](#xstringshuffle)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Deterministic shuffling with a seeded RNG](#deterministic-shuffling-with-a-seeded-rng)
    - [Grapheme clusters shuffle as whole units](#grapheme-clusters-shuffle-as-whole-units)
    - [Byte mode shuffles the raw bytes](#byte-mode-shuffles-the-raw-bytes)
    - [Code point mode exposes flag halves](#code-point-mode-exposes-flag-halves)
    - [Empty strings stay empty](#empty-strings-stay-empty)
    - [Original instance is untouched](#original-instance-is-untouched)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function shuffle(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Returns a new immutable `XString` with its characters randomly shuffled. The behaviour respects the current mode so you can
shuffle by grapheme, code point, or raw byte without manually exploding the string first. Internally it mirrors PHP's native
`shuffle()` algorithm so seeding the RNG with `mt_srand()` yields deterministic results—useful for reproducible tests.

## Important notes and considerations

- **Mode aware.** Grapheme mode keeps emoji and combining sequences intact, while code point and byte modes may split them.
- **Deterministic with seeds.** Use `mt_srand()` before invoking `shuffle()` to obtain repeatable results.
- **Immutable clone.** The original instance is never changed.

## Parameters

This method does not accept parameters.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` containing the shuffled characters. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Deterministic shuffling with a seeded RNG

<!-- test:shuffle-seeded -->
```php
use Orryv\XString;

mt_srand(1234);
$value = XString::new('abcd');
$result = $value->shuffle();

#Test: self::assertSame('bcad', (string) $result);
```

### Grapheme clusters shuffle as whole units

<!-- test:shuffle-graphemes -->
```php
use Orryv\XString;

mt_srand(2);
$value = XString::new("a\u{0301}b");
$result = $value->shuffle();

#Test: self::assertSame("ba\u{0301}", (string) $result);
```

### Byte mode shuffles the raw bytes

<!-- test:shuffle-bytes -->
```php
use Orryv\XString;

mt_srand(5);
$value = XString::new("a\u{0301}b")->withMode('bytes');
$result = $value->shuffle();

#Test: self::assertSame(4, $result->length());
#Test: self::assertSame('81cc6162', bin2hex((string) $result));
```

### Code point mode exposes flag halves

<!-- test:shuffle-codepoints -->
```php
use Orryv\XString;

mt_srand(13);
$value = XString::new('🇳🇱🇩🇪🇧🇪');
$graphemeShuffle = $value->shuffle();
mt_srand(13);
$codepointShuffle = $value->withMode('codepoints')->shuffle();

#Test: self::assertSame('🇧🇪🇳🇱🇩🇪', (string) $graphemeShuffle);
#Test: self::assertSame('🇱🇳🇪🇪🇩🇧', (string) $codepointShuffle);
```

### Empty strings stay empty

<!-- test:shuffle-empty -->
```php
use Orryv\XString;

$value = XString::new('');
$result = $value->shuffle();

#Test: self::assertSame('', (string) $result);
```

### Original instance is untouched

<!-- test:shuffle-immutable -->
```php
use Orryv\XString;

mt_srand(99);
$value = XString::new('loop');
$result = $value->shuffle();

#Test: self::assertSame('loop', (string) $value);
#Test: self::assertSame('lopo', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::shuffle` | `public function shuffle(): self` — Randomly reorder the string according to the active mode while keeping the original untouched. |
