# XString::isAscii()

## Table of Contents
- [XString::isAscii()](#xstringisascii)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Plain ASCII text returns true](#plain-ascii-text-returns-true)
    - [Accented characters are not ASCII](#accented-characters-are-not-ascii)
    - [Emoji and multibyte scripts return false](#emoji-and-multibyte-scripts-return-false)
    - [Empty strings count as ASCII](#empty-strings-count-as-ascii)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function isAscii(): bool
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✗ | Public |

## Description

Checks whether the string contains only characters from the 7-bit ASCII range (`0x00`–`0x7F`). Uses `mb_check_encoding()` when
available, falling back to a regular expression check.

## Important notes and considerations

- **Newlines and tabs** still qualify as ASCII; the method only verifies byte values.
- **Zero-length strings** are considered ASCII.
- **Validation only.** The original instance is never modified.

## Parameters

`—` This method does not take any parameters.

## Returns

| Return Type | Description |
| --- | --- |
| `bool` | `true` if every byte is ASCII (`<= 0x7F`), `false` otherwise. |

## Examples

### Plain ASCII text returns true

<!-- test:is-ascii-plain -->
```php
use Orryv\XString;

$value = XString::new("Hello, World!\n");

#Test: self::assertTrue($value->isAscii());
```

### Accented characters are not ASCII

<!-- test:is-ascii-accented -->
```php
use Orryv\XString;

$value = XString::new('Café');

#Test: self::assertFalse($value->isAscii());
```

### Emoji and multibyte scripts return false

<!-- test:is-ascii-emoji -->
```php
use Orryv\XString;

$value = XString::new('こんにちは 😊');

#Test: self::assertFalse($value->isAscii());
```

### Empty strings count as ASCII

<!-- test:is-ascii-empty -->
```php
use Orryv\XString;

$value = XString::new('');

#Test: self::assertTrue($value->isAscii());
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::isAscii` | `public function isAscii(): bool` — Check whether the string is confined to the 7-bit ASCII range. |
