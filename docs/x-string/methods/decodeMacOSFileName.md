# XString::decodeMacOSFileName()

## Table of Contents
- [XString::decodeMacOSFileName()](#xstringdecodemacosfilename)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Restore forward slashes](#restore-forward-slashes)
    - [Restore colon characters](#restore-colon-characters)
    - [Restore percent signs](#restore-percent-signs)
    - [Recover null bytes](#recover-null-bytes-2)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function decodeMacOSFileName(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Decode percent escapes produced by [`encodeMacOSFileName()`](encodeMacOSFileName.md), translating `%2F`, `%3A`, `%25`, `%00`, and
any other `%XX` sequence back to their literal characters so macOS filenames can be restored losslessly.

## Important notes and considerations

- **Generic decoding.** Any `%` followed by two hex digits is converted back to the corresponding byte.
- **No sanitisation.** The method does not validate the decoded string; it only reverses percent encoding.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Decoded macOS filename. |

## Examples

### Restore forward slashes

<!-- test:mac-decode-filename-slash -->
```php
use Orryv\XString;

$value = XString::new('data%2Freport.csv');
$result = $value->decodeMacOSFileName();

#Test: self::assertSame('data/report.csv', (string) $result);
```

### Restore colon characters

<!-- test:mac-decode-filename-colon -->
```php
use Orryv\XString;

$value = XString::new('audio%3Amix');
$result = $value->decodeMacOSFileName();

#Test: self::assertSame('audio:mix', (string) $result);
```

### Restore percent signs

<!-- test:mac-decode-filename-percent -->
```php
use Orryv\XString;

$value = XString::new('config%25test');
$result = $value->decodeMacOSFileName();

#Test: self::assertSame('config%test', (string) $result);
```

### Recover null bytes

<!-- test:mac-decode-filename-null -->
```php
use Orryv\XString;

$value = XString::new('file%00name');
$result = $value->decodeMacOSFileName();

#Test: self::assertSame("file\0name", (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::decodeMacOSFileName` | `public function decodeMacOSFileName(): self` — Decode `%XX` sequences in macOS-safe filenames. |
