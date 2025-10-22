# XString::encodeWindowsFileName()

## Table of Contents
- [XString::encodeWindowsFileName()](#xstringencodewindowsfilename)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Escape forbidden characters](#escape-forbidden-characters)
    - [Protect reserved device names](#protect-reserved-device-names)
    - [Preserve trailing spaces and periods](#preserve-trailing-spaces-and-periods)
    - [Percent signs are double-escaped](#percent-signs-are-double-escaped)
    - [Control double encoding](#control-double-encoding)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function encodeWindowsFileName(bool $double_encode = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Percent-encode characters that Windows forbids inside file names. The method leaves legal characters untouched while escaping
slashes, control bytes, reserved device names, and trailing spaces/periods so the result can be decoded without collisions.

## Important notes and considerations

- **Windows rules only.** Encoding targets the Windows forbidden character set (`<>:"/\\|?*`) plus control codes and NUL.
- **Optional double encoding.** Already escaped `%XX` sequences remain untouched unless you pass `$double_encode = true`, mirroring the semantics of `encodeHtmlEntities()`. 
- **Escapes the escape.** Literal percent signs are converted to `%25` so decoded values are unambiguous.
- **Reserved devices.** Device names such as `CON` or `PRN` are prefixed with an encoded character to avoid clashes.
- **Whitespace safety.** Trailing spaces or periods are percent-encoded because Win32 trims them automatically.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Filename with Windows-unsafe characters percent-encoded. |

## Examples

### Escape forbidden characters

<!-- test:windows-encode-filename-forbidden -->
```php
use Orryv\XString;

$value = XString::new('Report?.txt');
$result = $value->encodeWindowsFileName();

#Test: self::assertSame('Report%3F.txt', (string) $result);
```

### Protect reserved device names

<!-- test:windows-encode-filename-reserved -->
```php
use Orryv\XString;

$value = XString::new('CON');
$result = $value->encodeWindowsFileName();

#Test: self::assertSame('%43ON', (string) $result);
```

### Preserve trailing spaces and periods

<!-- test:windows-encode-filename-trailing -->
```php
use Orryv\XString;

$value = XString::new('log .');
$result = $value->encodeWindowsFileName();

#Test: self::assertSame('log%20%2E', (string) $result);
```

### Percent signs are double-escaped

<!-- test:windows-encode-filename-percent -->
```php
use Orryv\XString;

$value = XString::new('Invoice 100% complete');
$result = $value->encodeWindowsFileName();

#Test: self::assertSame('Invoice 100%25 complete', (string) $result);
```

### Control double encoding

<!-- test:windows-encode-filename-double-encode-toggle -->
```php
use Orryv\XString;

$value = XString::new('Archive%202024?.zip');

$noDouble = $value->encodeWindowsFileName();
$double = $value->encodeWindowsFileName(true);

#Test: self::assertSame('Archive%202024%3F.zip', (string) $noDouble);
#Test: self::assertSame('Archive%252024%253F.zip', (string) $double);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::encodeWindowsFileName` | `public function encodeWindowsFileName(bool $double_encode = false): self` — Percent-encode Windows forbidden filename characters so the value can be decoded without collisions. |
