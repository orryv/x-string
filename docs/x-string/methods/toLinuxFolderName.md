# XString::toLinuxFolderName()

## Table of Contents
- [XString::toLinuxFolderName()](#xstringtolinuxfoldername)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Replace forward slashes](#replace-forward-slashes)
    - [Collapse reserved directory markers](#collapse-reserved-directory-markers)
    - [Whitespace-only names become underscores](#whitespace-only-names-become-underscores)
    - [Unicode characters are preserved](#unicode-characters-are-preserved)
    - [Original instance remains unchanged](#original-instance-remains-unchanged)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toLinuxFolderName(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Return a directory name that is safe on Linux/Unix filesystems by removing the null byte, replacing directory separators,
and collapsing reserved names (`''`, `'.'`, `'..'`) into underscores.

## Important notes and considerations

- **Slash replacement.** Any `/` characters are converted to `_` because they are not allowed in folder names.
- **Control removal.** ASCII control characters are stripped entirely.
- **Whitespace guard.** Names that normalise to empty (after trimming) become `_`.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Linux-safe folder name. |

## Examples

### Replace forward slashes

<!-- test:linux-folder-slashes -->
```php
use Orryv\XString;

$value = XString::new('var/log');
$result = $value->toLinuxFolderName();

#Test: self::assertSame('var_log', (string) $result);
```

### Collapse reserved directory markers

<!-- test:linux-folder-reserved -->
```php
use Orryv\XString;

$value = XString::new('.');
$result = $value->toLinuxFolderName();

#Test: self::assertSame('_', (string) $result);
```

### Whitespace-only names become underscores

<!-- test:linux-folder-whitespace -->
```php
use Orryv\XString;

$value = XString::new("   ");
$result = $value->toLinuxFolderName();

#Test: self::assertSame('_', (string) $result);
```

### Unicode characters are preserved

<!-- test:linux-folder-unicode -->
```php
use Orryv\XString;

$value = XString::new('données');
$result = $value->toLinuxFolderName();

#Test: self::assertSame('données', (string) $result);
```

### Original instance remains unchanged

<!-- test:linux-folder-immutability -->
```php
use Orryv\XString;

$value = XString::new('tmp/cache');
$value->toLinuxFolderName();

#Test: self::assertSame('tmp/cache', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toLinuxFolderName` | `public function toLinuxFolderName(): self` — Produce a Linux-safe folder name by stripping control bytes, replacing slashes, and normalising empty/special names to underscores. |
