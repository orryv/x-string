# XString::decodeMacOSFolderName()

## Table of Contents
- [XString::decodeMacOSFolderName()](#xstringdecodemacosfoldername)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Restore separators inside names](#restore-separators-inside-names)
    - [Restore colon characters](#restore-colon-characters-1)
    - [Restore percent signs](#restore-percent-signs-1)
    - [Recover null bytes](#recover-null-bytes-3)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function decodeMacOSFolderName(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Decode percent-encoded macOS folder names produced by [`encodeMacOSFolderName()`](encodeMacOSFolderName.md), reversing `%2F`,
`%3A`, `%25`, `%00`, and any other `%XX` escape so the original directory name is restored.

## Important notes and considerations

- **No validation.** The method decodes escapes but does not enforce filesystem rules.
- **Symmetric behaviour.** Encoding followed by decoding returns the original input.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Decoded macOS folder name. |

## Examples

### Restore separators inside names

<!-- test:mac-decode-folder-slash -->
```php
use Orryv\XString;

$value = XString::new('config%2Fnginx');
$result = $value->decodeMacOSFolderName();

#Test: self::assertSame('config/nginx', (string) $result);
```

### Restore colon characters

<!-- test:mac-decode-folder-colon -->
```php
use Orryv\XString;

$value = XString::new('cache%3Atmp');
$result = $value->decodeMacOSFolderName();

#Test: self::assertSame('cache:tmp', (string) $result);
```

### Restore percent signs

<!-- test:mac-decode-folder-percent -->
```php
use Orryv\XString;

$value = XString::new('cache%25data');
$result = $value->decodeMacOSFolderName();

#Test: self::assertSame('cache%data', (string) $result);
```

### Recover null bytes

<!-- test:mac-decode-folder-null -->
```php
use Orryv\XString;

$value = XString::new('%00tmp');
$result = $value->decodeMacOSFolderName();

#Test: self::assertSame("\0tmp", (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::decodeMacOSFolderName` | `public function decodeMacOSFolderName(): self` — Decode `%XX` escapes in macOS folder names. |
