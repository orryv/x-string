# XString::toUnixPath()

## Table of Contents
- [XString::toUnixPath()](#xstringtounixpath)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Normalise mixed separators](#normalise-mixed-separators-1)
    - [Sanitise special segments](#sanitise-special-segments)
    - [Preserve absolute roots and trailing slashes](#preserve-absolute-roots-and-trailing-slashes)
    - [Fallback for empty values](#fallback-for-empty-values)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toUnixPath(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Build a Unix-safe path by splitting on `/` or `\`, sanitising each segment with [`toUnixFileName()`](toUnixFileName.md),
and rejoining with forward slashes. Absolute paths retain their leading `/` and optional trailing slash.

## Important notes and considerations

- **Segment sanitisation.** Each component is cleaned individually—special names (`''`, `'.'`, `'..'`) become `_`.
- **Separator normalisation.** All backslashes collapse into `/` and repeated separators are reduced to one.
- **Control character removal.** Null bytes and ASCII control characters are dropped.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Unix-compatible path string. |

## Examples

### Normalise mixed separators

<!-- test:unix-path-mixed -->
```php
use Orryv\XString;

$value = XString::new('logs\\2024/errors');
$result = $value->toUnixPath();

#Test: self::assertSame('logs/2024/errors', (string) $result);
```

### Sanitise special segments

<!-- test:unix-path-special -->
```php
use Orryv\XString;

$value = XString::new('/etc/../passwd');
$result = $value->toUnixPath();

#Test: self::assertSame('/etc/_/passwd', (string) $result);
```

### Preserve absolute roots and trailing slashes

<!-- test:unix-path-trailing -->
```php
use Orryv\XString;

$value = XString::new('/var/log/');
$result = $value->toUnixPath();

#Test: self::assertSame('/var/log/', (string) $result);
```

### Fallback for empty values

<!-- test:unix-path-empty -->
```php
use Orryv\XString;

$value = XString::new('   ');
$result = $value->toUnixPath();

#Test: self::assertSame('_', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toUnixPath` | `public function toUnixPath(): self` — Normalise a path for Unix by cleaning each segment and rejoining with forward slashes, preserving absolute roots and trailing separators. |
