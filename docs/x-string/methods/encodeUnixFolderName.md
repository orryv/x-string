# XString::encodeUnixFolderName()

## Table of Contents
- [XString::encodeUnixFolderName()](#xstringencodeunixfoldername)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Escape embedded separators](#escape-embedded-separators)
    - [Escape literal percent signs](#escape-literal-percent-signs-1)
    - [Encode leading null bytes](#encode-leading-null-bytes)
    - [Leave Unicode intact](#leave-unicode-intact)
    - [Control double encoding](#control-double-encoding)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function encodeUnixFolderName(bool $double_encode = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Percent-encode the few characters that Unix directories cannot contain in their names: forward slashes, null bytes, and literal
percent signs. All other characters remain untouched so that decoding reproduces the original folder name precisely.

## Important notes and considerations

- **Minimal escaping.** Only `/`, `%`, and `\0` are encoded because Unix allows all other bytes.
- **Optional double encoding.** Provide `$double_encode = true` to re-encode existing `%XX` sequences if necessary.
- **Supports multibyte input.** UTF-8 and other multi-byte characters are left alone.
- **Designed for round-trips.** Combine with [`decodeUnixFolderName()`](decodeUnixFolderName.md) to recover the original name.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Folder name with Unix-forbidden bytes percent-encoded. |

## Examples

### Escape embedded separators

<!-- test:unix-encode-folder-slash -->
```php
use Orryv\XString;

$value = XString::new('config/nginx');
$result = $value->encodeUnixFolderName();

#Test: self::assertSame('config%2Fnginx', (string) $result);
```

### Escape literal percent signs

<!-- test:unix-encode-folder-percent -->
```php
use Orryv\XString;

$value = XString::new('cache%data');
$result = $value->encodeUnixFolderName();

#Test: self::assertSame('cache%25data', (string) $result);
```

### Encode leading null bytes

<!-- test:unix-encode-folder-null -->
```php
use Orryv\XString;

$value = XString::new("\0tmp");
$result = $value->encodeUnixFolderName();

#Test: self::assertSame('%00tmp', (string) $result);
```

### Leave Unicode intact

<!-- test:unix-encode-folder-unicode -->
```php
use Orryv\XString;

$value = XString::new('データ');
$result = $value->encodeUnixFolderName();

#Test: self::assertSame('データ', (string) $result);
```

### Control double encoding

<!-- test:unix-encode-folder-name-double-encode-toggle -->
```php
use Orryv\XString;

$value = XString::new('Backups%202024/reports');

$noDouble = $value->encodeUnixFolderName();
$double = $value->encodeUnixFolderName(true);

#Test: self::assertSame('Backups%202024%2Freports', (string) $noDouble);
#Test: self::assertSame('Backups%252024%2Freports', (string) $double);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::encodeUnixFolderName` | `public function encodeUnixFolderName(bool $double_encode = false): self` — Percent-encode `/`, `%`, and null bytes inside Unix folder names for safe round-tripping. |
