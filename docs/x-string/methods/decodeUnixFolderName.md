# XString::decodeUnixFolderName()

## Table of Contents
- [XString::decodeUnixFolderName()](#xstringdecodeunixfoldername)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Decode embedded separators](#decode-embedded-separators)
    - [Decode escaped percent signs](#decode-escaped-percent-signs-3)
    - [Recover null bytes](#recover-null-bytes)
    - [Leave Unicode untouched](#leave-unicode-untouched)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function decodeUnixFolderName(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Decode percent escapes emitted by [`encodeUnixFolderName()`](encodeUnixFolderName.md). `%2F`, `%25`, `%00`, and any other `%XX`
sequence are converted back to their literal characters so that folder names containing embedded slashes, percent signs, or null
bytes round-trip accurately.

## Important notes and considerations

- **Idempotent on plain strings.** If no `%XX` escapes are present the string is returned unchanged.
- **UTF-8 friendly.** Only escaped bytes are transformed; other characters remain untouched.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Decoded Unix folder name. |

## Examples

### Decode embedded separators

<!-- test:unix-decode-folder-slash -->
```php
use Orryv\XString;

$value = XString::new('config%2Fnginx');
$result = $value->decodeUnixFolderName();

#Test: self::assertSame('config/nginx', (string) $result);
```

### Decode escaped percent signs

<!-- test:unix-decode-folder-percent -->
```php
use Orryv\XString;

$value = XString::new('cache%25data');
$result = $value->decodeUnixFolderName();

#Test: self::assertSame('cache%data', (string) $result);
```

### Recover null bytes

<!-- test:unix-decode-folder-null -->
```php
use Orryv\XString;

$value = XString::new('%00tmp');
$result = $value->decodeUnixFolderName();

#Test: self::assertSame("\0tmp", (string) $result);
```

### Leave Unicode untouched

<!-- test:unix-decode-folder-unicode -->
```php
use Orryv\XString;

$value = XString::new('データ');
$result = $value->decodeUnixFolderName();

#Test: self::assertSame('データ', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::decodeUnixFolderName` | `public function decodeUnixFolderName(): self` — Decode `%XX` escapes in Unix folder names to restore the literal characters. |
