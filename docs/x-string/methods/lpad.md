# XString::lpad()

## Table of Contents
- [XString::lpad()](#xstringlpad)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Pad numbers with leading zeros](#pad-numbers-with-leading-zeros)
    - [Pad using a multi-character fragment](#pad-using-a-multi-character-fragment)
    - [Respect grapheme-aware padding](#respect-grapheme-aware-padding)
    - [Immutability check](#immutability-check)
    - [Empty pad fragment throws an exception](#empty-pad-fragment-throws-an-exception)
    - [Negative length throws an exception](#negative-length-throws-an-exception)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function lpad(int $length, Newline|HtmlTag|Regex|string $pad_string = ' '): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Pads the string on the left until it reaches the requested `$length`, using the provided `$pad_string`. The behavior is the
same as calling [`pad()`](pad.md) with `$left = true` and `$right = false`.

## Important notes and considerations

- **Mode aware.** Padding counts units according to the active mode (`bytes`, `codepoints`, `graphemes`).
- **Immutable.** Returns a new `XString`; the original instance is unchanged.
- **Alias of `pad()`.** All validation rules from `pad()` apply here as well.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$length` | `int` | — | Target length in the current mode. Must be `>= 0`. |
| `$pad_string` | `Newline\|HtmlTag\|Regex\|string` | `' '` | Fragment used to build the left padding. Must not be empty after normalization. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` padded to the requested length on the left side. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$length` is negative or `$pad_string` normalizes to an empty string. |

## Examples

### Pad numbers with leading zeros

<!-- test:lpad-basic -->
```php
use Orryv\XString;

$result = XString::new('42')->lpad(5, '0');
#Test: self::assertSame('00042', (string) $result);
```

### Pad using a multi-character fragment

<!-- test:lpad-multi-fragment -->
```php
use Orryv\XString;

$result = XString::new('file')->lpad(10, '-=');
#Test: self::assertSame('-=-=-=file', (string) $result);
```

### Respect grapheme-aware padding

<!-- test:lpad-grapheme -->
```php
use Orryv\XString;

$result = XString::new('🙂')->lpad(4, '⭐');
#Test: self::assertSame('⭐⭐⭐🙂', (string) $result);
```

### Immutability check

<!-- test:lpad-immutability -->
```php
use Orryv\XString;

$xstring = XString::new('cat');
$padded = $xstring->lpad(6, '.');
#Test: self::assertSame('cat', (string) $xstring);
#Test: self::assertSame('...cat', (string) $padded);
```

### Empty pad fragment throws an exception

<!-- test:lpad-empty-fragment -->
```php
use InvalidArgumentException;
use Orryv\XString;

$xstring = XString::new('oops');
#Test: $this->expectException(InvalidArgumentException::class);
$xstring->lpad(5, '');
```

### Negative length throws an exception

<!-- test:lpad-negative-length -->
```php
use InvalidArgumentException;
use Orryv\XString;

$xstring = XString::new('oops');
#Test: $this->expectException(InvalidArgumentException::class);
$xstring->lpad(-1, '.');
```

## One-line API table entry

| Method | Version | Signature & Description |
| --- | --- | --- |
| `lpad` | 1.0 | `public function lpad(int $length, Newline\|HtmlTag\|Regex\|string $pad_string = ' '): self` — Pad the value on the left up to `$length` using `$pad_string`. |
