# XString::reverse()

## Table of Contents
- [XString::reverse()](#xstringreverse)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Reverse a simple ASCII sentence](#reverse-a-simple-ascii-sentence)
    - [Grapheme clusters stay intact](#grapheme-clusters-stay-intact)
    - [Byte mode reveals combining marks separately](#byte-mode-reveals-combining-marks-separately)
    - [Code point mode splits skin-tone modifiers](#code-point-mode-splits-skin-tone-modifiers)
    - [Empty strings reverse to empty strings](#empty-strings-reverse-to-empty-strings)
    - [Original instance remains unchanged](#original-instance-remains-unchanged)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function reverse(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Produces a new immutable `XString` with the characters reversed according to the active mode. In grapheme mode it flips whole
user-perceived characters, keeping emoji and combining sequences intact, while byte and code point modes expose the raw
underlying representation.

## Important notes and considerations

- **Mode dependent behaviour.** Reverse outcome differs depending on whether you operate in `graphemes`, `codepoints`, or `bytes`.
- **No mutation.** The original `XString` is left untouched so you can reuse it if needed.
- **Encoding aware.** Grapheme-aware logic gracefully falls back to multibyte operations when the intl extension is unavailable.

## Parameters

This method does not accept parameters.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` with the characters reversed based on the active mode. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Reverse a simple ASCII sentence

<!-- test:reverse-basic -->
```php
use Orryv\XString;

$value = XString::new('desserts');
$result = $value->reverse();

#Test: self::assertSame('stressed', (string) $result);
```

### Grapheme clusters stay intact

<!-- test:reverse-graphemes -->
```php
use Orryv\XString;

$value = XString::new("a\u{0301}b");
$result = $value->reverse();

#Test: self::assertSame("ba\u{0301}", (string) $result);
```

### Byte mode reveals combining marks separately

<!-- test:reverse-bytes -->
```php
use Orryv\XString;

$value = XString::new("a\u{0301}b")->withMode('bytes');
$result = $value->reverse();

#Test: self::assertSame('6281cc61', bin2hex((string) $result));
#Test: self::assertSame(4, $result->length());
```

### Code point mode splits skin-tone modifiers

<!-- test:reverse-codepoints -->
```php
use Orryv\XString;

$value = XString::new('👍🏽🙂');
$grapheme = $value->reverse();
$codepoints = $value->withMode('codepoints')->reverse();

#Test: self::assertSame('🙂👍🏽', (string) $grapheme);
#Test: self::assertSame("🙂🏽👍", (string) $codepoints);
```

### Empty strings reverse to empty strings

<!-- test:reverse-empty -->
```php
use Orryv\XString;

$value = XString::new('');
$result = $value->reverse();

#Test: self::assertSame('', (string) $result);
```

### Original instance remains unchanged

<!-- test:reverse-immutable -->
```php
use Orryv\XString;

$value = XString::new('Palindrome');
$result = $value->reverse();

#Test: self::assertSame('Palindrome', (string) $value);
#Test: self::assertSame('emordnilaP', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::reverse` | `public function reverse(): self` — Reverse the string according to the active mode without mutating the original instance. |
