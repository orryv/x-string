# XString::toUnixFileName()

## Table of Contents
- [XString::toUnixFileName()](#xstringtounixfilename)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Replace forward slashes](#replace-forward-slashes)
    - [Collapse empty or special names](#collapse-empty-or-special-names)
    - [Whitespace-only names become underscores](#whitespace-only-names-become-underscores)
    - [Unicode characters are preserved](#unicode-characters-are-preserved-1)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toUnixFileName(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Return a filename that is safe on Unix-style filesystems (Linux, BSD, etc.) by removing the null byte, replacing directory separators,
and collapsing special names (`''`, `'.'`, `'..'`) into underscores.

## Important notes and considerations

- **Slash replacement.** Any `/` characters are converted to `_` because they are not allowed in filenames.
- **Control removal.** ASCII control characters are stripped entirely.
- **Whitespace guard.** Names that normalise to empty (after trimming) become `_`.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Unix-safe filename. |

## Examples

### Replace forward slashes

<!-- test:unix-filename-slashes -->
```php
use Orryv\XString;

$value = XString::new('logs/error.log');
$result = $value->toUnixFileName();

#Test: self::assertSame('logs_error.log', (string) $result);
```

### Collapse empty or special names

<!-- test:unix-filename-special -->
```php
use Orryv\XString;

$value = XString::new('..');
$result = $value->toUnixFileName();

#Test: self::assertSame('_', (string) $result);
```

### Whitespace-only names become underscores

<!-- test:unix-filename-whitespace -->
```php
use Orryv\XString;

$value = XString::new("   ");
$result = $value->toUnixFileName();

#Test: self::assertSame('_', (string) $result);
```

### Unicode characters are preserved

<!-- test:unix-filename-unicode -->
```php
use Orryv\XString;

$value = XString::new('résumé.txt');
$result = $value->toUnixFileName();

#Test: self::assertSame('résumé.txt', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toUnixFileName` | `public function toUnixFileName(): self` — Produce a Unix-safe filename by stripping control bytes, replacing slashes, and normalising empty/special names to underscores. |
