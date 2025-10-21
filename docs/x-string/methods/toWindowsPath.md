# XString::toWindowsPath()

## Table of Contents
- [XString::toWindowsPath()](#xstringtowindowspath)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Sanitise a drive-qualified path](#sanitise-a-drive-qualified-path)
    - [Handle UNC shares and reserved segments](#handle-unc-shares-and-reserved-segments)
    - [Normalise mixed separators](#normalise-mixed-separators)
    - [Preserve trailing directory separators](#preserve-trailing-directory-separators)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toWindowsPath(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Transform the current value into a Windows-safe path. Segments are separated with backslashes, illegal characters are
sanitised using the same rules as [`toWindowsFileName()`](toWindowsFileName.md), and reserved device names are escaped.
Drive prefixes (e.g. `C:`) and UNC shares (`\\server\share`) are preserved when present.

## Important notes and considerations

- **Segment-wise sanitisation.** Every path component is cleaned individually using Windows filename rules.
- **Separator normalisation.** Forward slashes are converted to backslashes and repeated separators collapse to one.
- **Reserved names handled.** Segments named `CON`, `PRN`, etc. are prefixed with `_`.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Windows-compatible path with safe segments. |

## Examples

### Sanitise a drive-qualified path

<!-- test:windows-path-drive -->
```php
use Orryv\XString;

$value = XString::new('C:/Temp/Project?/Readme.txt');
$result = $value->toWindowsPath();

#Test: self::assertSame('C:\\Temp\\Project_\\Readme.txt', (string) $result);
```

### Handle UNC shares and reserved segments

<!-- test:windows-path-unc -->
```php
use Orryv\XString;

$value = XString::new('\\\\Server\\Share\\AUX\\');
$result = $value->toWindowsPath();

#Test: self::assertSame('\\\\Server\\Share\\_AUX\\', (string) $result);
```

### Normalise mixed separators

<!-- test:windows-path-mixed -->
```php
use Orryv\XString;

$value = XString::new('logs//..\\current');
$result = $value->toWindowsPath();

#Test: self::assertSame('logs\\_\\current', (string) $result);
```

### Preserve trailing directory separators

<!-- test:windows-path-trailing -->
```php
use Orryv\XString;

$value = XString::new('C:\\Temp\\');
$result = $value->toWindowsPath();

#Test: self::assertSame('C:\\Temp\\', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toWindowsPath` | `public function toWindowsPath(): self` — Normalise a path for Windows by converting separators, sanitising each segment, and escaping reserved device names. |
