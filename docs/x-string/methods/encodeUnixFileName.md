# XString::encodeUnixFileName()

## Table of Contents
- [XString::encodeUnixFileName()](#xstringencodeunixfilename)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Escape forward slashes](#escape-forward-slashes)
    - [Escape literal percent signs](#escape-literal-percent-signs)
    - [Encode null bytes safely](#encode-null-bytes-safely)
    - [Leave other characters untouched](#leave-other-characters-untouched)
    - [Control double encoding](#control-double-encoding)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function encodeUnixFileName(bool $double_encode = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Percent-encode the few characters that Unix-like filesystems reject inside filenames: the forward slash, the null byte, and the
percent sign (to keep escapes unambiguous). Everything else—including spaces, colons, and Unicode characters—remains unchanged so
that decoding reproduces the original string exactly.

## Important notes and considerations

- **Minimal encoding.** Only `/`, `%`, and `\0` are escaped because other characters are valid on Unix.
- **Optional double encoding.** Existing `%XX` sequences remain untouched unless you pass `$double_encode = true`.
- **UTF-8 safe.** Multibyte characters are left untouched.
- **Round-trip guarantee.** Strings passed through `decodeUnixFileName()` return to their original spelling.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Filename with Unix-forbidden bytes percent-encoded. |

## Examples

### Escape forward slashes

<!-- test:unix-encode-filename-slash -->
```php
use Orryv\XString;

$value = XString::new('data/report.csv');
$result = $value->encodeUnixFileName();

#Test: self::assertSame('data%2Freport.csv', (string) $result);
```

### Escape literal percent signs

<!-- test:unix-encode-filename-percent -->
```php
use Orryv\XString;

$value = XString::new('config%test');
$result = $value->encodeUnixFileName();

#Test: self::assertSame('config%25test', (string) $result);
```

### Encode null bytes safely

<!-- test:unix-encode-filename-null -->
```php
use Orryv\XString;

$value = XString::new("file\0name");
$result = $value->encodeUnixFileName();

#Test: self::assertSame('file%00name', (string) $result);
```

### Leave other characters untouched

<!-- test:unix-encode-filename-unicode -->
```php
use Orryv\XString;

$value = XString::new('résumé.txt');
$result = $value->encodeUnixFileName();

#Test: self::assertSame('résumé.txt', (string) $result);
```

### Control double encoding

<!-- test:unix-encode-filename-double-encode-toggle -->
```php
use Orryv\XString;

$value = XString::new('Revenue%202024/report');

$noDouble = $value->encodeUnixFileName();
$double = $value->encodeUnixFileName(true);

#Test: self::assertSame('Revenue%202024%2Freport', (string) $noDouble);
#Test: self::assertSame('Revenue%252024%2Freport', (string) $double);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::encodeUnixFileName` | `public function encodeUnixFileName(bool $double_encode = false): self` — Percent-encode `/`, `%`, and null bytes so Unix filenames can be round-tripped safely. |
