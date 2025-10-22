# XString::encodeMacOSPath()

## Table of Contents
- [XString::encodeMacOSPath()](#xstringencodemacopath)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Encode colons inside segments](#encode-colons-inside-segments)
    - [Escape percent signs inside segments](#escape-percent-signs-inside-segments-1)
    - [Preserve trailing slashes](#preserve-trailing-slashes-1)
    - [Encode null bytes within segments](#encode-null-bytes-within-segments-1)
    - [Control double encoding](#control-double-encoding)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function encodeMacOSPath(bool $double_encode = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Percent-encode macOS-forbidden characters within each path segment (`/`, `:`, `%`, and `\0`) while leaving `/` separators in
place. The encoded path can be decoded back to the original string without ambiguity.

## Important notes and considerations

- **Segment encoding mirrors filenames.** Each component is encoded using the same rules as [`encodeMacOSFileName()`](encodeMacOSFileName.md).
- **Optional double encoding.** Pass `$double_encode = true` to re-encode already escaped `%XX` sequences inside segments.
- **Separators untouched.** `/` separators remain as separators.
- **Round-trip safe.** Pair with [`decodeMacOSPath()`](decodeMacOSPath.md) to restore the original path.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | macOS-safe path with forbidden segment characters percent-encoded. |

## Examples

### Encode colons inside segments

<!-- test:mac-encode-path-colon -->
```php
use Orryv\XString;

$value = XString::new('Applications/Audio:Mix');
$result = $value->encodeMacOSPath();

#Test: self::assertSame('Applications/Audio%3AMix', (string) $result);
```

### Escape percent signs inside segments

<!-- test:mac-encode-path-percent -->
```php
use Orryv\XString;

$value = XString::new('Volumes/data%/raw');
$result = $value->encodeMacOSPath();

#Test: self::assertSame('Volumes/data%25/raw', (string) $result);
```

### Preserve trailing slashes

<!-- test:mac-encode-path-trailing -->
```php
use Orryv\XString;

$value = XString::new('/Users/');
$result = $value->encodeMacOSPath();

#Test: self::assertSame('/Users/', (string) $result);
```

### Encode null bytes within segments

<!-- test:mac-encode-path-null -->
```php
use Orryv\XString;

$value = XString::new("/tmp/" . "\0" . "cache");
$result = $value->encodeMacOSPath();

#Test: self::assertSame('/tmp/%00cache', (string) $result);
```

### Control double encoding

<!-- test:mac-encode-path-double-encode-toggle -->
```php
use Orryv\XString;

$value = XString::new('Projects%202024/Design:Draft');

$noDouble = $value->encodeMacOSPath();
$double = $value->encodeMacOSPath(true);

#Test: self::assertSame('Projects%202024/Design%3ADraft', (string) $noDouble);
#Test: self::assertSame('Projects%252024/Design%253ADraft', (string) $double);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::encodeMacOSPath` | `public function encodeMacOSPath(bool $double_encode = false): self` — Percent-encode `/`, `:`, `%`, and null bytes inside macOS path segments while leaving separators intact. |
