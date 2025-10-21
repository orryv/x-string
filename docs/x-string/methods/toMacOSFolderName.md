# XString::toMacOSFolderName()

## Table of Contents
- [XString::toMacOSFolderName()](#xstringtomacosfoldername)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Replace colon separators](#replace-colon-separators)
    - [Replace slashes](#replace-slashes)
    - [Collapse special names](#collapse-special-names)
    - [Whitespace-only names become underscores](#whitespace-only-names-become-underscores)
    - [Original instance remains unchanged](#original-instance-remains-unchanged)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toMacOSFolderName(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Prepare a directory name that is safe on modern macOS filesystems (HFS+/APFS) by replacing the legacy colon (`:`) separator,
converting `/` to `_`, removing control characters, and collapsing `''`, `'.'`, and `'..'` to `_`.

## Important notes and considerations

- **Colon handling.** macOS reserves `:` for internal path separation; it is replaced with `_`.
- **Slash replacement.** `/` is not permitted inside a folder name and is also replaced with `_`.
- **Whitespace guard.** Names that normalise to empty or only whitespace resolve to `_`.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | macOS-safe folder name. |

## Examples

### Replace colon separators

<!-- test:macos-folder-colon -->
```php
use Orryv\XString;

$value = XString::new('data:exports');
$result = $value->toMacOSFolderName();

#Test: self::assertSame('data_exports', (string) $result);
```

### Replace slashes

<!-- test:macos-folder-slash -->
```php
use Orryv\XString;

$value = XString::new('report/summary');
$result = $value->toMacOSFolderName();

#Test: self::assertSame('report_summary', (string) $result);
```

### Collapse special names

<!-- test:macos-folder-special -->
```php
use Orryv\XString;

$value = XString::new('..');
$result = $value->toMacOSFolderName();

#Test: self::assertSame('_', (string) $result);
```

### Whitespace-only names become underscores

<!-- test:macos-folder-whitespace -->
```php
use Orryv\XString;

$value = XString::new("   ");
$result = $value->toMacOSFolderName();

#Test: self::assertSame('_', (string) $result);
```

### Original instance remains unchanged

<!-- test:macos-folder-immutability -->
```php
use Orryv\XString;

$value = XString::new('reports:2024');
$value->toMacOSFolderName();

#Test: self::assertSame('reports:2024', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toMacOSFolderName` | `public function toMacOSFolderName(): self` — Produce a macOS-safe folder name by replacing colons and slashes, stripping control characters, and normalising empty names to underscores. |
