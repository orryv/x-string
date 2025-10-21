# XString::toWindowsFileName()

## Table of Contents
- [XString::toWindowsFileName()](#xstringtowindowsfilename)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Replace forbidden characters](#replace-forbidden-characters)
    - [Escape reserved device names](#escape-reserved-device-names)
    - [Trim trailing dots and spaces](#trim-trailing-dots-and-spaces)
    - [Unicode characters are preserved](#unicode-characters-are-preserved)
    - [Original instance remains unchanged](#original-instance-remains-unchanged-2)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toWindowsFileName(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Produce a filename that is safe to use on NTFS/FAT filesystems. The method removes characters that Windows forbids,
collapses control characters, trims disallowed trailing dots/spaces, and prefixes reserved device names (e.g. `CON`, `PRN`).

## Important notes and considerations

- **Character sanitisation.** Invalid characters (`<>:"/\\|?*` and ASCII control bytes) are replaced with underscores.
- **Reserved names.** Device names such as `CON`, `NUL`, `PRN`, `AUX`, `COM1`–`COM9`, and `LPT1`–`LPT9` are prefixed with `_`.
- **Length guard.** Results are truncated to 255 bytes to respect Windows filesystem limits.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Sanitised filename suitable for Windows. |

## Examples

### Replace forbidden characters

<!-- test:windows-filename-forbidden -->
```php
use Orryv\XString;

$value = XString::new('Report?.txt');
$result = $value->toWindowsFileName();

#Test: self::assertSame('Report_.txt', (string) $result);
```

### Escape reserved device names

<!-- test:windows-filename-reserved -->
```php
use Orryv\XString;

$value = XString::new('CON');
$result = $value->toWindowsFileName();

#Test: self::assertSame('_CON', (string) $result);
```

### Trim trailing dots and spaces

<!-- test:windows-filename-trim -->
```php
use Orryv\XString;

$value = XString::new(' log . ');
$result = $value->toWindowsFileName();

#Test: self::assertSame('log', (string) $result);
```

### Unicode characters are preserved

<!-- test:windows-filename-unicode -->
```php
use Orryv\XString;

$value = XString::new('Résumé.txt');
$result = $value->toWindowsFileName();

#Test: self::assertSame('Résumé.txt', (string) $result);
```

### Original instance remains unchanged

<!-- test:windows-filename-immutability -->
```php
use Orryv\XString;

$value = XString::new('draft?.md');
$value->toWindowsFileName();

#Test: self::assertSame('draft?.md', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toWindowsFileName` | `public function toWindowsFileName(): self` — Convert the value into a Windows-safe filename by removing illegal characters, handling reserved names, and trimming trailing dots/spaces. |
