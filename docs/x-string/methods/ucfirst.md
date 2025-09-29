# XString::ucfirst()

## Table of Contents
- [XString::ucfirst()](#xstringucfirst)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Capitalize the first letter of an ASCII sentence](#capitalize-the-first-letter-of-an-ascii-sentence)
    - [Capitalize the first multibyte character](#capitalize-the-first-multibyte-character)
    - [Instance remains immutable](#instance-remains-immutable)
    - [Works in codepoint mode](#works-in-codepoint-mode)
    - [Handles empty strings gracefully](#handles-empty-strings-gracefully)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function ucfirst(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Converts the first character of the string to uppercase and returns a new immutable `XString`. The remaining characters are left
unchanged. When multibyte support is available, the conversion uses `mb_strtoupper()` and `mb_substr()` so that characters beyond
basic ASCII are handled correctly.

## Important notes and considerations

- **Immutability preserved.** A fresh `XString` instance is returned; the original remains untouched.
- **Encoding aware.** Multibyte characters are handled via `mb_*` functions when present, ensuring accented characters are
  capitalized correctly.
- **Mode preserved.** The resulting instance keeps the same logical mode (`bytes`, `codepoints`, or `graphemes`) and encoding as
  the source.

## Parameters

This method does not accept parameters.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new immutable `XString` with the first character uppercased. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Capitalize the first letter of an ASCII sentence

<!-- test:ucfirst-basic -->
```php
use Orryv\XString;

$xstring = XString::new('hello world');
$result = $xstring->ucfirst();
#Test: self::assertSame('Hello world', (string) $result);
```

### Capitalize the first multibyte character

<!-- test:ucfirst-multibyte -->
```php
use Orryv\XString;

$xstring = XString::new('éclair');
$result = $xstring->ucfirst();
#Test: self::assertSame('Éclair', (string) $result);
```

### Instance remains immutable

<!-- test:ucfirst-immutable -->
```php
use Orryv\XString;

$xstring = XString::new('already capitalized');
$result = $xstring->ucfirst();
#Test: self::assertSame('already capitalized', (string) $xstring);
#Test: self::assertSame('Already capitalized', (string) $result);
```

### Works in codepoint mode

<!-- test:ucfirst-codepoint-mode -->
```php
use Orryv\XString;

$xstring = XString::new('ßharp')->withMode('codepoints');
$result = $xstring->ucfirst();
#Test: self::assertSame('SSharp', (string) $result);
```

### Handles empty strings gracefully

<!-- test:ucfirst-empty -->
```php
use Orryv\XString;

$xstring = XString::new('');
$result = $xstring->ucfirst();
#Test: self::assertSame('', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::ucfirst` | `public function ucfirst(): self` — Return an immutable clone with only the first character uppercased. |
