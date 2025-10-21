# XString::toSafePath()

## Table of Contents
- [XString::toSafePath()](#xstringtosafepath)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Sanitise a mixed-platform path](#sanitise-a-mixed-platform-path)
    - [Preserve safe POSIX paths](#preserve-safe-posix-paths)
    - [Normalise special segments](#normalise-special-segments)
    - [Fallback for empty values](#fallback-for-empty-values-1)
    - [Preserve trailing separators when present](#preserve-trailing-separators-when-present)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toSafePath(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Convert the value into a cross-platform safe path. Separators are normalised to `/`, each segment is processed with
[`toSafeFileName()`](toSafeFileName.md), and leading/trailing slashes are preserved when present.

## Important notes and considerations

- **Cross-OS sanitisation.** Windows device names and invalid characters are neutralised while remaining compatible with Unix/macOS semantics.
- **Separator normalisation.** Backslashes are converted to `/` and duplicate separators collapse to a single `/`.
- **Special segment handling.** Empty segments and `.`/`..` become `_` to avoid unintended traversal.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Portable path using `/` separators. |

## Examples

### Sanitise a mixed-platform path

<!-- test:safe-path-mixed -->
```php
use Orryv\XString;

$value = XString::new('C:\\Temp\\AUX\\report?.txt');
$result = $value->toSafePath();

#Test: self::assertSame('C_/Temp/_AUX/report_.txt', (string) $result);
```

### Preserve safe POSIX paths

<!-- test:safe-path-posix -->
```php
use Orryv\XString;

$value = XString::new('/etc/passwd');
$result = $value->toSafePath();

#Test: self::assertSame('/etc/passwd', (string) $result);
```

### Normalise special segments

<!-- test:safe-path-special -->
```php
use Orryv\XString;

$value = XString::new('../..');
$result = $value->toSafePath();

#Test: self::assertSame('_/_', (string) $result);
```

### Fallback for empty values

<!-- test:safe-path-empty -->
```php
use Orryv\XString;

$value = XString::new('   ');
$result = $value->toSafePath();

#Test: self::assertSame('_', (string) $result);
```

### Preserve trailing separators when present

<!-- test:safe-path-trailing -->
```php
use Orryv\XString;

$value = XString::new('/var/tmp/');
$result = $value->toSafePath();

#Test: self::assertSame('/var/tmp/', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toSafePath` | `public function toSafePath(): self` — Produce a conservative, cross-platform safe path by sanitising every segment and joining them with `/`, preserving leading/trailing separators. |
