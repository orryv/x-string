# XString::toMacOSFileName()

## Table of Contents
- [XString::toMacOSFileName()](#xstringtomacosfilename)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Replace colon separators](#replace-colon-separators)
    - [Replace slashes](#replace-slashes)
    - [Collapse special names](#collapse-special-names)
    - [Whitespace-only names become underscores](#whitespace-only-names-become-underscores-1)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toMacOSFileName(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Prepare a filename that is safe on modern macOS filesystems (HFS+/APFS) by replacing the legacy colon (`:`) separator,
converting `/` to `_`, removing control characters, and collapsing `''`, `'.'`, and `'..'` to `_`.

## Important notes and considerations

- **Colon handling.** macOS reserves `:` for internal path separation; it is replaced with `_`.
- **Slash replacement.** `/` is not permitted inside a filename and is also replaced with `_`.
- **Whitespace guard.** Names that normalise to empty or only whitespace resolve to `_`.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | macOS-safe filename. |

## Examples

### Replace colon separators

<!-- test:macos-filename-colon -->
```php
use Orryv\XString;

$value = XString::new('data:export.csv');
$result = $value->toMacOSFileName();

#Test: self::assertSame('data_export.csv', (string) $result);
```

### Replace slashes

<!-- test:macos-filename-slash -->
```php
use Orryv\XString;

$value = XString::new('report/summary');
$result = $value->toMacOSFileName();

#Test: self::assertSame('report_summary', (string) $result);
```

### Collapse special names

<!-- test:macos-filename-special -->
```php
use Orryv\XString;

$value = XString::new('..');
$result = $value->toMacOSFileName();

#Test: self::assertSame('_', (string) $result);
```

### Whitespace-only names become underscores

<!-- test:macos-filename-whitespace -->
```php
use Orryv\XString;

$value = XString::new("   ");
$result = $value->toMacOSFileName();

#Test: self::assertSame('_', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toMacOSFileName` | `public function toMacOSFileName(): self` — Produce a macOS-safe filename by replacing colons and slashes, stripping control characters, and normalising empty names to underscores. |
