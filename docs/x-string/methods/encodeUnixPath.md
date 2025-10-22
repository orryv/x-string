# XString::encodeUnixPath()

## Table of Contents
- [XString::encodeUnixPath()](#xstringencodeunixpath)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Leave normal segments unchanged](#leave-normal-segments-unchanged)
    - [Escape percent signs inside segments](#escape-percent-signs-inside-segments)
    - [Preserve trailing slashes](#preserve-trailing-slashes)
    - [Encode null bytes within segments](#encode-null-bytes-within-segments)
    - [Control double encoding](#control-double-encoding)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function encodeUnixPath(bool $double_encode = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Percent-encode only the characters that Unix path segments cannot contain (`/`, `%`, and null bytes) while leaving existing
separators untouched. Each component is encoded individually so decoding can restore the original path string without ambiguity.

## Important notes and considerations

- **Separators untouched.** Existing `/` separators remain separators; only segment contents are encoded.
- **Optional double encoding.** Already encoded `%XX` substrings stay untouched unless `$double_encode` is set to `true`.
- **Minimal escaping.** Only `/`, `%`, and `\0` inside segments are transformed.
- **Round-trip friendly.** Combine with [`decodeUnixPath()`](decodeUnixPath.md) to recover the original string.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Unix path with forbidden segment characters percent-encoded. |

## Examples

### Leave normal segments unchanged

<!-- test:unix-encode-path-unchanged -->
```php
use Orryv\XString;

$value = XString::new('logs/2024/errors');
$result = $value->encodeUnixPath();

#Test: self::assertSame('logs/2024/errors', (string) $result);
```

### Escape percent signs inside segments

<!-- test:unix-encode-path-percent -->
```php
use Orryv\XString;

$value = XString::new('logs/data%/report');
$result = $value->encodeUnixPath();

#Test: self::assertSame('logs/data%25/report', (string) $result);
```

### Preserve trailing slashes

<!-- test:unix-encode-path-trailing -->
```php
use Orryv\XString;

$value = XString::new('/workspace/');
$result = $value->encodeUnixPath();

#Test: self::assertSame('/workspace/', (string) $result);
```

### Encode null bytes within segments

<!-- test:unix-encode-path-null -->
```php
use Orryv\XString;

$value = XString::new("/app/" . "\0" . "cache");
$result = $value->encodeUnixPath();

#Test: self::assertSame('/app/%00cache', (string) $result);
```

### Control double encoding

<!-- test:unix-encode-path-double-encode-toggle -->
```php
use Orryv\XString;

$value = XString::new('reports%202024/summary%20draft');

$noDouble = $value->encodeUnixPath();
$double = $value->encodeUnixPath(true);

#Test: self::assertSame('reports%202024/summary%20draft', (string) $noDouble);
#Test: self::assertSame('reports%252024/summary%2520draft', (string) $double);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::encodeUnixPath` | `public function encodeUnixPath(bool $double_encode = false): self` — Percent-encode `/`, `%`, and null bytes inside Unix path segments while keeping separators intact. |
