# XString::pad()

## Table of Contents
- [XString::pad()](#xstringpad)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Pad on both sides by default](#pad-on-both-sides-by-default)
    - [Pad on the right side](#pad-on-the-right-side)
    - [Pad on the left side](#pad-on-the-left-side)
    - [Respect grapheme-aware padding](#respect-grapheme-aware-padding)
    - [Pad while in byte mode](#pad-while-in-byte-mode)
    - [Immutability check](#immutability-check-3)
    - [Target length shorter than current string](#target-length-shorter-than-current-string)
    - [Empty pad string throws an exception](#empty-pad-string-throws-an-exception)
    - [At least one side must be selected](#at-least-one-side-must-be-selected)
    - [Negative length throws an exception](#negative-length-throws-an-exception)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function pad(
    int $length,
    Newline|HtmlTag|Regex|string $pad_string = ' ',
    bool $left = true,
    bool $right = true
): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Extends the current string to the desired length by adding copies of `$pad_string` to the left and/or right. Padding is
calculated using the active length mode (graphemes by default), ensuring multi-byte characters are handled correctly.

## Important notes and considerations

- **Default direction.** With the default arguments the string is padded on both sides (slightly favouring the right when the
  required units are odd). Toggle `$left` and `$right` or use [`lpad()`](lpad.md)/[`rpad()`](rpad.md) for single-side padding.
- **Mode aware.** Padding counts units according to the current mode (`bytes`, `codepoints`, `graphemes`). Switch modes with
  [`withMode()`](withMode.md) or its helpers (`asBytes()`, etc.).
- **Immutability.** Returns a new `XString` instance; the original remains unchanged.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$length` | `int` | — | Target length in the current mode. Must be `>= 0`. |
| `$pad_string` | `Newline\|HtmlTag\|Regex\|string` | `' '` | Fragment used to pad the value. Must not be empty after normalization. |
| `$left` | `bool` | `true` | Whether to apply padding on the left side. |
| `$right` | `bool` | `true` | Whether to apply padding on the right side. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` padded to the requested length. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$length` is negative, `$pad_string` normalizes to an empty string, or both `$left` and `$right` are `false`. |

## Examples

### Pad on both sides by default

<!-- test:pad-default-both-sides -->
```php
use Orryv\XString;

$xstring = XString::new('42');
$result = $xstring->pad(5, '0');
#Test: self::assertSame('04200', (string) $result);
```

### Pad on the right side

<!-- test:pad-right -->
```php
use Orryv\XString;

$xstring = XString::new('data');
$result = $xstring->pad(7, '.', left: false, right: true);
#Test: self::assertSame('data...', (string) $result);
```

### Pad on the left side

<!-- test:pad-left -->
```php
use Orryv\XString;

$xstring = XString::new('cat');
$result = $xstring->pad(6, '_', left: true, right: false);
#Test: self::assertSame('___cat', (string) $result);
```

### Respect grapheme-aware padding

<!-- test:pad-grapheme -->
```php
use Orryv\XString;

$result = XString::new('🙂')->pad(3, '⭐');
#Test: self::assertSame('⭐🙂⭐', (string) $result);
```

### Pad while in byte mode

<!-- test:pad-byte-mode -->
```php
use Orryv\XString;

$xstring = XString::new('猫')->withMode('bytes');
$result = $xstring->pad(5, '?');
#Test: self::assertSame('?猫?', (string) $result);
#Test: self::assertSame(5, $result->length());
```

### Immutability check

<!-- test:pad-immutability -->
```php
use Orryv\XString;

$xstring = XString::new('core');
$padded = $xstring->pad(6, '*', left: false, right: true);
#Test: self::assertSame('core', (string) $xstring);
#Test: self::assertSame('core**', (string) $padded);
```

### Target length shorter than current string

<!-- test:pad-no-change -->
```php
use Orryv\XString;

$xstring = XString::new('sample');
$result = $xstring->pad(3, '0');
#Test: self::assertSame('sample', (string) $result);
```

### Empty pad string throws an exception

<!-- test:pad-empty-pad-string -->
```php
use InvalidArgumentException;
use Orryv\XString;

$xstring = XString::new('fail');
#Test: $this->expectException(InvalidArgumentException::class);
$xstring->pad(10, '');
```

### At least one side must be selected

<!-- test:pad-no-sides -->
```php
use InvalidArgumentException;
use Orryv\XString;

$xstring = XString::new('fail');
#Test: $this->expectException(InvalidArgumentException::class);
$xstring->pad(10, '.', left: false, right: false);
```

### Negative length throws an exception

<!-- test:pad-negative-length -->
```php
use InvalidArgumentException;
use Orryv\XString;

$xstring = XString::new('fail');
#Test: $this->expectException(InvalidArgumentException::class);
$xstring->pad(-1, '.');
```

## One-line API table entry

| Method | Version | Signature & Description |
| --- | --- | --- |
| `pad` | 1.0 | `public function pad(int $length, Newline\|HtmlTag\|Regex\|string $pad_string = ' ', bool $left = true, bool $right = true): self` — Extend the string to a target length by adding padding on the selected sides. |
