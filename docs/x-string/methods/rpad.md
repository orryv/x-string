# XString::rpad()

## Table of Contents
- [XString::rpad()](#xstringrpad)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Pad strings on the right](#pad-strings-on-the-right)
    - [Use multi-character padding](#use-multi-character-padding)
    - [Pad in byte mode](#pad-in-byte-mode)
    - [Immutability check](#immutability-check)
    - [Empty pad fragment throws an exception](#empty-pad-fragment-throws-an-exception)
    - [Negative length throws an exception](#negative-length-throws-an-exception)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function rpad(int $length, Newline|HtmlTag|Regex|string $pad_string = ' '): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Pads the string on the right until it reaches the requested `$length`, using the provided `$pad_string`. The behavior is the
same as calling [`pad()`](pad.md) with `$left = false` and `$right = true`.

## Important notes and considerations

- **Mode aware.** Padding counts units according to the active mode (`bytes`, `codepoints`, `graphemes`).
- **Immutable.** Returns a new `XString`; the original instance is unchanged.
- **Alias of `pad()`.** All validation rules from `pad()` apply here as well.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$length` | `int` | — | Target length in the current mode. Must be `>= 0`. |
| `$pad_string` | `Newline\|HtmlTag\|Regex\|string` | `' '` | Fragment used to build the right padding. Must not be empty after normalization. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` padded to the requested length on the right side. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$length` is negative or `$pad_string` normalizes to an empty string. |

## Examples

### Pad strings on the right

<!-- test:rpad-basic -->
```php
use Orryv\XString;

$result = XString::new('data')->rpad(8, '.');
#Test: self::assertSame('data....', (string) $result);
```

### Use multi-character padding

<!-- test:rpad-multi-fragment -->
```php
use Orryv\XString;

$result = XString::new('topic')->rpad(12, '->');
#Test: self::assertSame('topic->->->-', (string) $result);
```

### Pad in byte mode

<!-- test:rpad-byte-mode -->
```php
use Orryv\XString;

$xstring = XString::new('á')->withMode('bytes');
$result = $xstring->rpad(4, '*');
#Test: self::assertSame('á**', (string) $result);
```

### Immutability check

<!-- test:rpad-immutability -->
```php
use Orryv\XString;

$xstring = XString::new('cat');
$padded = $xstring->rpad(6, '.');
#Test: self::assertSame('cat', (string) $xstring);
#Test: self::assertSame('cat...', (string) $padded);
```

### Empty pad fragment throws an exception

<!-- test:rpad-empty-fragment -->
```php
use InvalidArgumentException;
use Orryv\XString;

$xstring = XString::new('oops');
#Test: $this->expectException(InvalidArgumentException::class);
$xstring->rpad(5, '');
```

### Negative length throws an exception

<!-- test:rpad-negative-length -->
```php
use InvalidArgumentException;
use Orryv\XString;

$xstring = XString::new('oops');
#Test: $this->expectException(InvalidArgumentException::class);
$xstring->rpad(-1, '.');
```

## One-line API table entry

| Method | Version | Signature & Description |
| --- | --- | --- |
| `rpad` | 1.0 | `public function rpad(int $length, Newline\|HtmlTag\|Regex\|string $pad_string = ' '): self` — Pad the value on the right up to `$length` using `$pad_string`. |
