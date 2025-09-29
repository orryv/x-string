# XString::lcfirst()

## Table of Contents
- [XString::lcfirst()](#xstringlcfirst)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Lowercase the first letter of an ASCII sentence](#lowercase-the-first-letter-of-an-ascii-sentence)
    - [Lowercase the first multibyte character](#lowercase-the-first-multibyte-character)
    - [Original instance remains unchanged](#original-instance-remains-unchanged)
    - [Works in grapheme mode](#works-in-grapheme-mode)
    - [Handles empty strings without errors](#handles-empty-strings-without-errors)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function lcfirst(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Returns a new immutable `XString` where only the first character is converted to lowercase. The remainder of the string is kept
as-is. When PHP's multibyte string functions are available, the conversion uses `mb_strtolower()` and `mb_substr()` to correctly
handle characters outside the ASCII range.

## Important notes and considerations

- **Immutable transformation.** The method never mutates the original instance.
- **Encoding aware.** Uses multibyte-aware functions whenever available so that accented and non-Latin characters are treated
  correctly.
- **Mode preserved.** The returned `XString` retains the original logical mode and encoding.

## Parameters

This method does not accept parameters.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new immutable `XString` with the first character lowercased. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Lowercase the first letter of an ASCII sentence

<!-- test:lcfirst-basic -->
```php
use Orryv\XString;

$xstring = XString::new('Hello World');
$result = $xstring->lcfirst();
#Test: self::assertSame('hello World', (string) $result);
```

### Lowercase the first multibyte character

<!-- test:lcfirst-multibyte -->
```php
use Orryv\XString;

$xstring = XString::new('Éclair');
$result = $xstring->lcfirst();
#Test: self::assertSame('éclair', (string) $result);
```

### Original instance remains unchanged

<!-- test:lcfirst-immutable -->
```php
use Orryv\XString;

$xstring = XString::new('Already lower');
$result = $xstring->lcfirst();
#Test: self::assertSame('Already lower', (string) $xstring);
#Test: self::assertSame('already lower', (string) $result);
```

### Works in grapheme mode

<!-- test:lcfirst-grapheme-mode -->
```php
use Orryv\XString;

$xstring = XString::new('Ωmega')->withMode('graphemes');
$result = $xstring->lcfirst();
#Test: self::assertSame('ωmega', (string) $result);
```

### Handles empty strings without errors

<!-- test:lcfirst-empty -->
```php
use Orryv\XString;

$xstring = XString::new('');
$result = $xstring->lcfirst();
#Test: self::assertSame('', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::lcfirst` | `public function lcfirst(): self` — Return an immutable clone with only the first character lowercased. |
