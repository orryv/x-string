# XString::decodeSafeFolderName()

## Table of Contents
- [XString::decodeSafeFolderName()](#xstringdecodesafefoldername)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Restore slashes and backslashes](#restore-slashes-and-backslashes)
    - [Restore colon characters](#restore-colon-characters-3)
    - [Bring back trailing spaces](#bring-back-trailing-spaces-2)
    - [Decode percent signs](#decode-percent-signs-2)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function decodeSafeFolderName(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Decode cross-platform safe folder names produced by [`encodeSafeFolderName()`](encodeSafeFolderName.md) by converting `%XX`
escapes back to their original characters.

## Important notes and considerations

- **Generic decoding.** Any valid `%` escape is translated back to its byte value.
- **No validation.** The method does not check that the decoded name is safe—it simply reverses encoding.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Decoded folder name restored from its portable representation. |

## Examples

### Restore slashes and backslashes

<!-- test:safe-decode-folder-slash -->
```php
use Orryv\XString;

$value = XString::new('config%2Fnginx');
$result = $value->decodeSafeFolderName();

#Test: self::assertSame('config/nginx', (string) $result);
```

### Restore colon characters

<!-- test:safe-decode-folder-colon -->
```php
use Orryv\XString;

$value = XString::new('cache%3Atmp');
$result = $value->decodeSafeFolderName();

#Test: self::assertSame('cache:tmp', (string) $result);
```

### Bring back trailing spaces

<!-- test:safe-decode-folder-trailing -->
```php
use Orryv\XString;

$value = XString::new('data%20%2E');
$result = $value->decodeSafeFolderName();

#Test: self::assertSame('data .', (string) $result);
```

### Decode percent signs

<!-- test:safe-decode-folder-percent -->
```php
use Orryv\XString;

$value = XString::new('cache%25data');
$result = $value->decodeSafeFolderName();

#Test: self::assertSame('cache%data', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::decodeSafeFolderName` | `public function decodeSafeFolderName(): self` — Decode `%XX` escapes in cross-platform folder names. |
