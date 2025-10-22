# XString::decodeUnixPath()

## Table of Contents
- [XString::decodeUnixPath()](#xstringdecodeunixpath)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Decode percent escapes inside segments](#decode-percent-escapes-inside-segments)
    - [Respect existing separators](#respect-existing-separators)
    - [Restore null bytes](#restore-null-bytes-1)
    - [Leave unchanged paths unaffected](#leave-unchanged-paths-unaffected)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function decodeUnixPath(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Decode percent-encoded Unix path segments produced by [`encodeUnixPath()`](encodeUnixPath.md). The method leaves `/` separators in
place while translating `%XX` escapes within segments back to their literal bytes, including `%2F`, `%25`, and `%00`.

## Important notes and considerations

- **Segment-wise decoding.** Only characters inside segments are decoded; separators remain untouched.
- **Idempotent when no escapes.** Paths without `%` sequences are returned as-is.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Decoded Unix path string. |

## Examples

### Decode percent escapes inside segments

<!-- test:unix-decode-path-percent -->
```php
use Orryv\XString;

$value = XString::new('logs/data%25/report');
$result = $value->decodeUnixPath();

#Test: self::assertSame('logs/data%/report', (string) $result);
```

### Respect existing separators

<!-- test:unix-decode-path-trailing -->
```php
use Orryv\XString;

$value = XString::new('/workspace/');
$result = $value->decodeUnixPath();

#Test: self::assertSame('/workspace/', (string) $result);
```

### Restore null bytes

<!-- test:unix-decode-path-null -->
```php
use Orryv\XString;

$value = XString::new('/app/%00cache');
$result = $value->decodeUnixPath();

#Test: self::assertSame("/app/" . "\0" . "cache", (string) $result);
```

### Leave unchanged paths unaffected

<!-- test:unix-decode-path-unchanged -->
```php
use Orryv\XString;

$value = XString::new('logs/2024/errors');
$result = $value->decodeUnixPath();

#Test: self::assertSame('logs/2024/errors', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::decodeUnixPath` | `public function decodeUnixPath(): self` — Decode `%XX` escapes within Unix path segments while leaving separators intact. |
