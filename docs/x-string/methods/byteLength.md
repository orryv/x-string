# XString::byteLength()

## Table of Contents
- [XString::byteLength()](#xstringbytelength)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Count bytes in ASCII text](#count-bytes-in-ascii-text)
    - [Count bytes in multibyte text](#count-bytes-in-multibyte-text)
    - [Byte length ignores the active mode](#byte-length-ignores-the-active-mode)
    - [Emoji byte length](#emoji-byte-length)
    - [Empty strings return zero](#empty-strings-return-zero-1)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function byteLength(): int
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✗ | Public |

## Description

Returns the number of raw bytes used by the underlying string, independent of the current iteration mode. This is equivalent to
`strlen((string) $xstring)` but keeps the fluent `XString` API.

## Important notes and considerations

- **Mode agnostic.** The result does not change when switching between `bytes`, `codepoints`, or `graphemes` modes.
- **Encoding dependent.** The value reflects the current encoding of the instance (default UTF-8).
- **Non-mutating.** Calling `byteLength()` never alters the original string.

## Parameters

This method does not accept parameters.

## Returns

| Return Type | Description |
| --- | --- |
| `int` | Number of bytes required to represent the string. |

## Thrown exceptions

This method does not throw exceptions.

## Examples

### Count bytes in ASCII text

<!-- test:byte-length-ascii -->
```php
use Orryv\XString;

$xstring = XString::new('hello world');

#Test: self::assertSame(11, $xstring->byteLength());
```

### Count bytes in multibyte text

<!-- test:byte-length-multibyte -->
```php
use Orryv\XString;

$value = 'naïve façade';
$xstring = XString::new($value);

#Test: self::assertSame(strlen($value), $xstring->byteLength());
```

### Byte length ignores the active mode

<!-- test:byte-length-mode -->
```php
use Orryv\XString;

$value = "Ångström";
$bytes = strlen($value);

#Test: self::assertSame($bytes, XString::new($value)->byteLength());
#Test: self::assertSame($bytes, XString::new($value)->asCodepoints()->byteLength());
#Test: self::assertSame($bytes, XString::new($value)->asGraphemes()->byteLength());
```

### Emoji byte length

<!-- test:byte-length-emoji -->
```php
use Orryv\XString;

$value = "👩‍🚀";

#Test: self::assertSame(strlen($value), XString::new($value)->byteLength());
```

### Empty strings return zero

<!-- test:byte-length-empty -->
```php
use Orryv\XString;

$xstring = XString::new('');

#Test: self::assertSame(0, $xstring->byteLength());
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::byteLength` | `public function byteLength(): int` — Return the raw byte length of the string, regardless of the active iteration mode. |
