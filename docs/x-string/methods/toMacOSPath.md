# XString::toMacOSPath()

## Table of Contents
- [XString::toMacOSPath()](#xstringtomacospath)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Replace colons within segments](#replace-colons-within-segments)
    - [Normalise separators and trailing slash](#normalise-separators-and-trailing-slash)
    - [Sanitise special names](#sanitise-special-names-1)
    - [Fallback for empty strings](#fallback-for-empty-strings)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toMacOSPath(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Normalise the string into a macOS-safe path. Segments are cleaned with [`toMacOSFileName()`](toMacOSFileName.md), forward
slashes are used as separators, and optional leading `/` and trailing `/` are preserved.

## Important notes and considerations

- **Colon replacement per segment.** Colons in path components become `_`, matching Finder and POSIX behaviour.
- **Separator normalisation.** Backslashes are converted to `/` and consecutive separators collapse to a single `/`.
- **Special segment handling.** Empty, `.` and `..` segments are converted to `_`.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | macOS-friendly path. |

## Examples

### Replace colons within segments

<!-- test:macos-path-colon -->
```php
use Orryv\XString;

$value = XString::new('Users:Shared/Logs');
$result = $value->toMacOSPath();

#Test: self::assertSame('Users_Shared/Logs', (string) $result);
```

### Normalise separators and trailing slash

<!-- test:macos-path-trailing -->
```php
use Orryv\XString;

$value = XString::new('/Library//Caches/');
$result = $value->toMacOSPath();

#Test: self::assertSame('/Library/Caches/', (string) $result);
```

### Sanitise special names

<!-- test:macos-path-special -->
```php
use Orryv\XString;

$value = XString::new('../config');
$result = $value->toMacOSPath();

#Test: self::assertSame('_/config', (string) $result);
```

### Fallback for empty strings

<!-- test:macos-path-empty -->
```php
use Orryv\XString;

$value = XString::new('   ');
$result = $value->toMacOSPath();

#Test: self::assertSame('_', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toMacOSPath` | `public function toMacOSPath(): self` — Normalise a path for macOS by cleaning each segment, converting separators to `/`, and preserving leading/trailing slashes. |
