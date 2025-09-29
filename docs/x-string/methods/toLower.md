# XString::toLower()

## Table of Contents
- [XString::toLower()](#xstringtolower)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Convert ASCII text to lowercase](#convert-ascii-text-to-lowercase)
    - [Lowercasing multibyte characters](#lowercasing-multibyte-characters)
    - [Alias `toLowerCase()` delegates to `toLower()`](#alias-tolowercase-delegates-to-tolower)
    - [Original instance remains unchanged](#original-instance-remains-unchanged)
    - [Lowercasing while in byte mode](#lowercasing-while-in-byte-mode)
    - [Lowercasing an empty string](#lowercasing-an-empty-string)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toLower(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Returns a new immutable `XString` whose contents are converted to lowercase. When available, `mb_strtolower()` is used with the
instance's encoding to correctly handle multibyte characters. The alias [`toLowerCase()`](./toLowerCase.md) simply invokes this
method.

## Important notes and considerations

- **Immutability.** A new `XString` is returned; the original value is preserved.
- **Encoding aware.** The stored encoding is respected when lowercasing, falling back to `strtolower()` if multibyte support is
  unavailable.
- **Mode preserved.** The resulting instance keeps the same mode and encoding as the source.

## Parameters

This method does not accept parameters.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new immutable `XString` containing the lowercase transformation. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Convert ASCII text to lowercase

<!-- test:to-lower-basic -->
```php
use Orryv\XString;

$xstring = XString::new('HELLO WORLD');
$lower = $xstring->toLower();
#Test: self::assertSame('hello world', (string) $lower);
```

### Lowercasing multibyte characters

<!-- test:to-lower-multibyte -->
```php
use Orryv\XString;

$xstring = XString::new('ŻÓŁĆ');
$lower = $xstring->toLower();
#Test: self::assertSame('żółć', (string) $lower);
```

### Alias `toLowerCase()` delegates to `toLower()`

<!-- test:to-lower-alias -->
```php
use Orryv\XString;

$xstring = XString::new('Alias');
#Test: self::assertSame((string) $xstring->toLower(), (string) $xstring->toLowerCase());
```

### Original instance remains unchanged

<!-- test:to-lower-immutability -->
```php
use Orryv\XString;

$xstring = XString::new('KEEP ME');
$lower = $xstring->toLower();
#Test: self::assertSame('KEEP ME', (string) $xstring);
#Test: self::assertSame('keep me', (string) $lower);
```

### Lowercasing while in byte mode

<!-- test:to-lower-byte-mode -->
```php
use Orryv\XString;

$xstring = XString::new('MIXED')->withMode('bytes');
$result = $xstring->toLower();
#Test: self::assertSame('mixed', (string) $result);
```

### Lowercasing an empty string

<!-- test:to-lower-empty -->
```php
use Orryv\XString;

$xstring = XString::new('');
$result = $xstring->toLower();
#Test: self::assertSame('', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toLower` | `public function toLower(): self` — Return a new immutable instance with the contents converted to lowercase. |
