# XString::decodeMacOSPath()

## Table of Contents
- [XString::decodeMacOSPath()](#xstringdecodemacopath)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Restore colon characters](#restore-colon-characters-2)
    - [Restore percent signs](#restore-percent-signs-2)
    - [Preserve trailing slashes](#preserve-trailing-slashes-2)
    - [Recover null bytes](#recover-null-bytes-4)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function decodeMacOSPath(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Decode macOS path strings that were percent-encoded by [`encodeMacOSPath()`](encodeMacOSPath.md). The method decodes `%2F`, `%3A`,
`%25`, `%00`, and other `%XX` escapes within segments while leaving `/` separators untouched.

## Important notes and considerations

- **Segment-wise decoding.** Only segment contents are decoded; separators remain `/`.
- **Round-trip guarantee.** Paths encoded by `encodeMacOSPath()` return to their original spelling when decoded.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Decoded macOS path string. |

## Examples

### Restore colon characters

<!-- test:mac-decode-path-colon -->
```php
use Orryv\XString;

$value = XString::new('Applications/Audio%3AMix');
$result = $value->decodeMacOSPath();

#Test: self::assertSame('Applications/Audio:Mix', (string) $result);
```

### Restore percent signs

<!-- test:mac-decode-path-percent -->
```php
use Orryv\XString;

$value = XString::new('Volumes/data%25/raw');
$result = $value->decodeMacOSPath();

#Test: self::assertSame('Volumes/data%/raw', (string) $result);
```

### Preserve trailing slashes

<!-- test:mac-decode-path-trailing -->
```php
use Orryv\XString;

$value = XString::new('/Users/');
$result = $value->decodeMacOSPath();

#Test: self::assertSame('/Users/', (string) $result);
```

### Recover null bytes

<!-- test:mac-decode-path-null -->
```php
use Orryv\XString;

$value = XString::new('/tmp/%00cache');
$result = $value->decodeMacOSPath();

#Test: self::assertSame("/tmp/" . "\0" . "cache", (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::decodeMacOSPath` | `public function decodeMacOSPath(): self` — Decode `%XX` escapes in macOS path segments. |
