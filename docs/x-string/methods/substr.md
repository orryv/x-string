# XString::substr()

## Table of Contents
- [XString::substr()](#xstringsubstr)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Extract the first word using grapheme slicing](#extract-the-first-word-using-grapheme-slicing)
    - [Grab a section from the middle of the string](#grab-a-section-from-the-middle-of-the-string)
    - [Negative starts count from the end](#negative-starts-count-from-the-end)
    - [Negative lengths trim characters from the end](#negative-lengths-trim-characters-from-the-end)
    - [Mode-aware slicing of combining characters](#mode-aware-slicing-of-combining-characters)
    - [Code point mode can split emoji sequences](#code-point-mode-can-split-emoji-sequences)
    - [Empty inputs stay empty](#empty-inputs-stay-empty)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function substr(int $start, ?int $length = null): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Returns a new immutable `XString` containing a slice of the original value. The method honours the instance's current
**mode**—`graphemes`, `codepoints`, or `bytes`—when interpreting the `$start` offset and `$length`. Negative offsets count from
the end of the string, and negative lengths trim characters from the right-hand side, mirroring PHP's `substr()`/`mb_substr()`
behaviour.

## Important notes and considerations

- **Mode aware.** `substr()` uses the active mode to determine how offsets are measured. Combine it with
  [`withMode()`](./withMode.md) to work with bytes or code points explicitly.
- **Immutable.** The original instance is never modified; each call returns a brand-new `XString`.
- **Graceful bounds handling.** Offsets beyond the string length simply yield an empty string.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$start` | `int` | — | 0-based offset indicating where the substring starts. Negative values count back from the end. |
| `$length` | `null\|int` | `null` | Optional length of the slice. `null` consumes the rest of the string. Negative lengths trim characters from the right-hand side. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new immutable `XString` containing the requested slice. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Extract the first word using grapheme slicing

<!-- test:substr-first-word -->
```php
use Orryv\XString;

$original = XString::new('naïve café');
$segment = $original->substr(0, 5);

#Test: self::assertSame('naïve', (string) $segment);
#Test: self::assertSame('naïve café', (string) $original);
```

### Grab a section from the middle of the string

<!-- test:substr-middle -->
```php
use Orryv\XString;

$original = XString::new('The quick brown fox');
$segment = $original->substr(4, 5);

#Test: self::assertSame('quick', (string) $segment);
```

### Negative starts count from the end

<!-- test:substr-negative-start -->
```php
use Orryv\XString;

$original = XString::new('Spacewalk');
$segment = $original->substr(-4);

#Test: self::assertSame('walk', (string) $segment);
```

### Negative lengths trim characters from the end

<!-- test:substr-negative-length -->
```php
use Orryv\XString;

$original = XString::new('Hello World');
$segment = $original->substr(0, -6);

#Test: self::assertSame('Hello', (string) $segment);
```

### Mode-aware slicing of combining characters

<!-- test:substr-mode-combining -->
```php
use Orryv\XString;

$value = XString::new("a\u{0301}b");
$graphemes = $value->substr(0, 2);
$bytes = $value->withMode('bytes')->substr(0, 2);

#Test: self::assertSame("a\u{0301}b", (string) $graphemes);
#Test: self::assertSame('61cc', bin2hex((string) $bytes));
```

### Code point mode can split emoji sequences

<!-- test:substr-mode-codepoints -->
```php
use Orryv\XString;

$value = XString::new("👍🏽!");
$graphemeSlice = $value->substr(0, 1);
$codepointSlice = $value->withMode('codepoints')->substr(0, 1);

#Test: self::assertSame("👍🏽", (string) $graphemeSlice);
#Test: self::assertSame("👍", (string) $codepointSlice);
```

### Empty inputs stay empty

<!-- test:substr-empty -->
```php
use Orryv\XString;

$value = XString::new('');
$result = $value->substr(0, 3);

#Test: self::assertSame('', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::substr` | `public function substr(int $start, ?int $length = null): self` — Slice the string according to the active mode, supporting negative offsets and lengths. |
