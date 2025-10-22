# XString::encodeWindowsFolderName()

## Table of Contents
- [XString::encodeWindowsFolderName()](#xstringencodewindowsfoldername)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Encode slash separators inside names](#encode-slash-separators-inside-names)
    - [Escape reserved DOS devices](#escape-reserved-dos-devices)
    - [Retain trailing whitespace safely](#retain-trailing-whitespace-safely)
    - [Strip control characters by encoding](#strip-control-characters-by-encoding)
    - [Control double encoding](#control-double-encoding)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function encodeWindowsFolderName(bool $double_encode = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Percent-encode characters that Windows does not allow inside directory names. The method keeps valid characters intact while
escaping slashes, reserved device names, control codes, and trailing spaces or periods so that decoded names reproduce the
original input exactly.

## Important notes and considerations

- **Folder semantics mirror filenames.** Any characters illegal in Windows directory entries are percent-encoded.
- **Optional double encoding.** Pass `$double_encode = true` to re-escape existing `%XX` sequences; by default they are left intact to match HTML-style semantics.
- **Escapes literal percent signs.** `%` becomes `%25` so that decode can reverse the transformation reliably.
- **Device names are safe.** Names such as `AUX` or `NUL` are prefixed with encoded characters to avoid collisions.
- **Trailing whitespace encoded.** Windows strips trailing spaces and periods, so they are percent-encoded instead of trimmed.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Folder name with Windows-unsafe characters percent-encoded. |

## Examples

### Encode slash separators inside names

<!-- test:windows-encode-folder-slash -->
```php
use Orryv\XString;

$value = XString::new('config/app');
$result = $value->encodeWindowsFolderName();

#Test: self::assertSame('config%2Fapp', (string) $result);
```

### Escape reserved DOS devices

<!-- test:windows-encode-folder-reserved -->
```php
use Orryv\XString;

$value = XString::new('AUX');
$result = $value->encodeWindowsFolderName();

#Test: self::assertSame('%41UX', (string) $result);
```

### Retain trailing whitespace safely

<!-- test:windows-encode-folder-trailing -->
```php
use Orryv\XString;

$value = XString::new('data .');
$result = $value->encodeWindowsFolderName();

#Test: self::assertSame('data%20%2E', (string) $result);
```

### Strip control characters by encoding

<!-- test:windows-encode-folder-control -->
```php
use Orryv\XString;

$value = XString::new("cache\x07");
$result = $value->encodeWindowsFolderName();

#Test: self::assertSame('cache%07', (string) $result);
```

### Control double encoding

<!-- test:windows-encode-folder-name-double-encode-toggle -->
```php
use Orryv\XString;

$value = XString::new('Reports%202024?');

$noDouble = $value->encodeWindowsFolderName();
$double = $value->encodeWindowsFolderName(true);

#Test: self::assertSame('Reports%202024%3F', (string) $noDouble);
#Test: self::assertSame('Reports%252024%253F', (string) $double);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::encodeWindowsFolderName` | `public function encodeWindowsFolderName(bool $double_encode = false): self` — Percent-encode Windows forbidden folder-name characters while leaving safe characters untouched. |
