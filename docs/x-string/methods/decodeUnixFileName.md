# XString::decodeUnixFileName()

## Table of Contents
- [XString::decodeUnixFileName()](#xstringdecodeunixfilename)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Decode forward slashes](#decode-forward-slashes)
    - [Decode escaped percent signs](#decode-escaped-percent-signs-2)
    - [Restore null bytes](#restore-null-bytes)
    - [Leave untouched characters](#leave-untouched-characters)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function decodeUnixFileName(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Decode percent sequences produced by [`encodeUnixFileName()`](encodeUnixFileName.md). The method translates `%2F`, `%25`, `%00`,
and any other `%XX` escape back to its byte value so filenames containing slashes, percent signs, or null bytes can be restored.

## Important notes and considerations

- **General `%` decoding.** Any `%` followed by two hex digits is converted back to the corresponding byte.
- **UTF-8 friendly.** Multibyte characters are returned as-is; only escaped bytes are affected.
- **Round-trip safe.** Applying `decodeUnixFileName()` to an encoded name reproduces the original input exactly.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Decoded Unix filename. |

## Examples

### Decode forward slashes

<!-- test:unix-decode-filename-slash -->
```php
use Orryv\XString;

$value = XString::new('data%2Freport.csv');
$result = $value->decodeUnixFileName();

#Test: self::assertSame('data/report.csv', (string) $result);
```

### Decode escaped percent signs

<!-- test:unix-decode-filename-percent -->
```php
use Orryv\XString;

$value = XString::new('config%25test');
$result = $value->decodeUnixFileName();

#Test: self::assertSame('config%test', (string) $result);
```

### Restore null bytes

<!-- test:unix-decode-filename-null -->
```php
use Orryv\XString;

$value = XString::new('file%00name');
$result = $value->decodeUnixFileName();

#Test: self::assertSame("file\0name", (string) $result);
```

### Leave untouched characters

<!-- test:unix-decode-filename-unicode -->
```php
use Orryv\XString;

$value = XString::new('résumé.txt');
$result = $value->decodeUnixFileName();

#Test: self::assertSame('résumé.txt', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::decodeUnixFileName` | `public function decodeUnixFileName(): self` — Decode `%XX` escapes in Unix filenames to restore the original characters. |
