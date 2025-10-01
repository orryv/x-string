# XString::center()

## Table of Contents
- [XString::center()](#xstringcenter)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Center within an odd-width field](#center-within-an-odd-width-field)
    - [Center with multi-character padding](#center-with-multi-character-padding)
    - [Center while counting bytes](#center-while-counting-bytes)
    - [No change when the string is already wide enough](#no-change-when-the-string-is-already-wide-enough)
    - [Immutability check](#immutability-check-4)
    - [Invalid pad string throws an exception](#invalid-pad-string-throws-an-exception)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function center(int $length, Newline|HtmlTag|Regex|string $pad_string = ' '): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Pads the string on both sides so that it is centered within the requested width. The method splits any extra unit
between the right and left sides (the right side receives the extra unit when the difference is odd).

## Important notes and considerations

- **Uses `pad()` internally.** All validation and mode-aware behaviour are delegated to [`pad()`](pad.md).
- **Mode aware.** Width is measured using the current mode (graphemes by default). Switch modes with `withMode()` or helpers
  such as `asBytes()`.
- **Immutability.** Returns a new instance; the source string does not change.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$length` | `int` | — | Target centered width measured in the current mode. |
| `$pad_string` | `Newline\|HtmlTag\|Regex\|string` | `' '` | Fragment repeated on both sides. Must not normalize to an empty string. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` centered within the specified width. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | Propagated from `pad()` when `$length` is negative, the pad string is empty, or other pad constraints fail. |

## Examples

### Center within an odd-width field

<!-- test:center-basic -->
```php
use Orryv\XString;

$xstring = XString::new('cat');
$result = $xstring->center(8, '-');
#Test: self::assertSame('--cat---', (string) $result);
```

### Center with multi-character padding

<!-- test:center-multi-char -->
```php
use Orryv\XString;

$xstring = XString::new('menu');
$result = $xstring->center(12, '[]');
#Test: self::assertSame('[][]menu[][]', (string) $result);
```

### Center while counting bytes

<!-- test:center-byte-mode -->
```php
use Orryv\XString;

$xstring = XString::new('猫')->withMode('bytes');
$result = $xstring->center(7, '.');
#Test: self::assertSame('..猫..', (string) $result);
#Test: self::assertSame(7, $result->length());
```

### No change when the string is already wide enough

<!-- test:center-no-change -->
```php
use Orryv\XString;

$xstring = XString::new('long text');
$result = $xstring->center(4, '*');
#Test: self::assertSame('long text', (string) $result);
```

### Immutability check

<!-- test:center-immutability -->
```php
use Orryv\XString;

$xstring = XString::new('core');
$centered = $xstring->center(10, '~');
#Test: self::assertSame('core', (string) $xstring);
#Test: self::assertSame('~~~core~~~', (string) $centered);
```

### Invalid pad string throws an exception

<!-- test:center-invalid-pad -->
```php
use InvalidArgumentException;
use Orryv\XString;

$xstring = XString::new('oops');
#Test: $this->expectException(InvalidArgumentException::class);
$xstring->center(6, '');
```

## One-line API table entry

| Method | Version | Signature & Description |
| --- | --- | --- |
| `center` | 1.0 | `public function center(int $length, Newline\|HtmlTag\|Regex\|string $pad_string = ' '): self` — Center the string within the requested width by padding both sides. |
