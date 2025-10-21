# XString::toWindowsFolderName()

## Table of Contents
- [XString::toWindowsFolderName()](#xstringtowindowsfoldername)
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
    - [Original instance remains unchanged](#original-instance-remains-unchanged)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toWindowsFolderName(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Produce a folder name that is safe to use on NTFS/FAT filesystems. The method removes characters that Windows forbids,
collapses control characters, trims disallowed trailing dots/spaces, and prefixes reserved device names (e.g. `CON`, `PRN`).

## Important notes and considerations

- **Character sanitisation.** Invalid characters (`<>:"/\\|?*` and ASCII control bytes) are replaced with underscores.
- **Reserved names.** Device names such as `CON`, `NUL`, `PRN`, `AUX`, `COM1`–`COM9`, and `LPT1`–`LPT9` are prefixed with `_`.
- **Length guard.** Results are truncated to 255 bytes to respect Windows filesystem limits.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Sanitised folder name suitable for Windows. |

## Examples

### Replace forbidden characters

<!-- test:windows-folder-forbidden -->
```php
use Orryv\XString;

$value = XString::new('Reports?:2024');
$result = $value->toWindowsFolderName();

#Test: self::assertSame('Reports__2024', (string) $result);
```

### Escape reserved device names

<!-- test:windows-folder-reserved -->
```php
use Orryv\XString;

$value = XString::new('NUL');
$result = $value->toWindowsFolderName();

#Test: self::assertSame('_NUL', (string) $result);
```

### Trim trailing dots and spaces

<!-- test:windows-folder-trim -->
```php
use Orryv\XString;

$value = XString::new(' logs . ');
$result = $value->toWindowsFolderName();

#Test: self::assertSame('logs', (string) $result);
```

### Unicode characters are preserved

<!-- test:windows-folder-unicode -->
```php
use Orryv\XString;

$value = XString::new('Réunion');
$result = $value->toWindowsFolderName();

#Test: self::assertSame('Réunion', (string) $result);
```

### Original instance remains unchanged

<!-- test:windows-folder-immutability -->
```php
use Orryv\XString;

$value = XString::new('temp?.tmp');
$value->toWindowsFolderName();

#Test: self::assertSame('temp?.tmp', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toWindowsFolderName` | `public function toWindowsFolderName(): self` — Convert the value into a Windows-safe folder name by removing illegal characters, handling reserved names, and trimming trailing dots/spaces. |
