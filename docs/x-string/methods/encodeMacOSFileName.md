# XString::encodeMacOSFileName()

## Table of Contents
- [XString::encodeMacOSFileName()](#xstringencodemacosfilename)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Escape forward slashes](#escape-forward-slashes-1)
    - [Escape colons reserved by HFS+/APFS](#escape-colons-reserved-by-hfsapfs)
    - [Escape percent signs](#escape-percent-signs)
    - [Encode null bytes](#encode-null-bytes)
    - [Control double encoding](#control-double-encoding)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function encodeMacOSFileName(bool $double_encode = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Percent-encode characters that macOS disallows inside file names: forward slashes, colons, percent signs, and the null byte. All
other characters remain untouched so the encoded string can be decoded back to the original filename without loss.

## Important notes and considerations

- **Colon support.** HFS+/APFS treat `:` as a forbidden character, so it is escaped to `%3A`.
- **Optional double encoding.** Pass `$double_encode = true` to re-encode existing `%XX` sequences when necessary.
- **Matches Unix rules.** `/`, `%`, and `\0` are encoded just like [`encodeUnixFileName()`](encodeUnixFileName.md).
- **Round-trip guarantee.** Pair with [`decodeMacOSFileName()`](decodeMacOSFileName.md) to restore the original value.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | macOS-safe filename with forbidden characters percent-encoded. |

## Examples

### Escape forward slashes

<!-- test:mac-encode-filename-slash -->
```php
use Orryv\XString;

$value = XString::new('data/report.csv');
$result = $value->encodeMacOSFileName();

#Test: self::assertSame('data%2Freport.csv', (string) $result);
```

### Escape colons reserved by HFS+/APFS

<!-- test:mac-encode-filename-colon -->
```php
use Orryv\XString;

$value = XString::new('audio:mix');
$result = $value->encodeMacOSFileName();

#Test: self::assertSame('audio%3Amix', (string) $result);
```

### Escape percent signs

<!-- test:mac-encode-filename-percent -->
```php
use Orryv\XString;

$value = XString::new('config%test');
$result = $value->encodeMacOSFileName();

#Test: self::assertSame('config%25test', (string) $result);
```

### Encode null bytes

<!-- test:mac-encode-filename-null -->
```php
use Orryv\XString;

$value = XString::new("file\0name");
$result = $value->encodeMacOSFileName();

#Test: self::assertSame('file%00name', (string) $result);
```

### Control double encoding

<!-- test:mac-encode-filename-double-encode-toggle -->
```php
use Orryv\XString;

$value = XString::new('Invoices%202024:Final');

$noDouble = $value->encodeMacOSFileName();
$double = $value->encodeMacOSFileName(true);

#Test: self::assertSame('Invoices%202024%3AFinal', (string) $noDouble);
#Test: self::assertSame('Invoices%252024%253AFinal', (string) $double);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::encodeMacOSFileName` | `public function encodeMacOSFileName(bool $double_encode = false): self` — Percent-encode `/`, `:`, `%`, and null bytes for macOS filenames. |
