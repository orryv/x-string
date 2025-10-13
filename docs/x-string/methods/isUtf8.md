# XString::isUtf8()

## Table of Contents
- [XString::isUtf8()](#xstringisutf8)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Valid UTF-8 text returns true](#valid-utf-8-text-returns-true)
    - [ASCII text is valid UTF-8](#ascii-text-is-valid-utf-8)
    - [Invalid byte sequences are detected](#invalid-byte-sequences-are-detected)
    - [Mixed invalid and valid bytes fail](#mixed-invalid-and-valid-bytes-fail)
    - [The original instance is untouched](#the-original-instance-is-untouched)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function isUtf8(): bool
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✗ | Public |

## Description

Checks whether the underlying string is a well-formed UTF-8 byte sequence. When the `mbstring` extension is available the
validation is delegated to `mb_check_encoding()`. Otherwise a regular expression validation is used as a fallback.

## Important notes and considerations

- **No conversion is performed.** The method only validates the current bytes.
- **Empty strings** are considered valid UTF-8.
- **Binary data** (e.g. encrypted payloads) will typically return `false`.

## Parameters

`—` This method does not take any parameters.

## Returns

| Return Type | Description |
| --- | --- |
| `bool` | `true` when the value is a valid UTF-8 byte sequence, otherwise `false`. |

## Examples

### Valid UTF-8 text returns true

<!-- test:is-utf8-valid -->
```php
use Orryv\XString;

$value = XString::new('こんにちは世界');

#Test: self::assertTrue($value->isUtf8());
```

### ASCII text is valid UTF-8

<!-- test:is-utf8-ascii -->
```php
use Orryv\XString;

$value = XString::new("Tabs\tand newlines\nare fine.");

#Test: self::assertTrue($value->isUtf8());
```

### Invalid byte sequences are detected

<!-- test:is-utf8-invalid-leading -->
```php
use Orryv\XString;

$value = XString::new("\xC3\x28");

#Test: self::assertFalse($value->isUtf8());
```

### Mixed invalid and valid bytes fail

<!-- test:is-utf8-invalid-mixed -->
```php
use Orryv\XString;

$value = XString::new("\xFF\xFEUTF-8");

#Test: self::assertFalse($value->isUtf8());
```

### The original instance is untouched

<!-- test:is-utf8-immutability -->
```php
use Orryv\XString;

$value = XString::new('Grüße');
$value->isUtf8();

#Test: self::assertSame('Grüße', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::isUtf8` | `public function isUtf8(): bool` — Determine whether the string is a valid UTF-8 byte sequence. |
