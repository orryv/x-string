# XString::toLinuxPath()

## Table of Contents
- [XString::toLinuxPath()](#xstringtolinuxpath)
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
public function toLinuxPath(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Build a Linux/Unix safe path by splitting on `/` or `\`, sanitising each segment with [`toLinuxFileName()`](toLinuxFileName.md),
and rejoining with forward slashes. Absolute paths retain their leading `/` and optional trailing slash.

## Important notes and considerations

- **Segment sanitisation.** Each component is cleaned individually—special names (`''`, `'.'`, `'..'`) become `_`.
- **Separator normalisation.** All backslashes collapse into `/` and repeated separators are reduced to one.
- **Control character removal.** Null bytes and ASCII control characters are dropped.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Linux-compatible path string. |

## Examples

### Normalise mixed separators

<!-- test:linux-path-mixed -->
```php
use Orryv\XString;

$value = XString::new('logs\\2024/errors');
$result = $value->toLinuxPath();

#Test: self::assertSame('logs/2024/errors', (string) $result);
```

### Sanitise special segments

<!-- test:linux-path-special -->
```php
use Orryv\XString;

$value = XString::new('/etc/../passwd');
$result = $value->toLinuxPath();

#Test: self::assertSame('/etc/_/passwd', (string) $result);
```

### Preserve absolute roots and trailing slashes

<!-- test:linux-path-trailing -->
```php
use Orryv\XString;

$value = XString::new('/var/log/');
$result = $value->toLinuxPath();

#Test: self::assertSame('/var/log/', (string) $result);
```

### Fallback for empty values

<!-- test:linux-path-empty -->
```php
use Orryv\XString;

$value = XString::new('   ');
$result = $value->toLinuxPath();

#Test: self::assertSame('_', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toLinuxPath` | `public function toLinuxPath(): self` — Normalise a path for Linux by cleaning each segment and rejoining with forward slashes, preserving absolute roots and trailing separators. |
