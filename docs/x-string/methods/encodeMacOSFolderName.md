# XString::encodeMacOSFolderName()

## Table of Contents
- [XString::encodeMacOSFolderName()](#xstringencodemacosfoldername)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Escape embedded separators](#escape-embedded-separators-1)
    - [Escape colon characters](#escape-colon-characters)
    - [Escape percent signs](#escape-percent-signs-1)
    - [Leave Unicode intact](#leave-unicode-intact-1)
    - [Control double encoding](#control-double-encoding)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function encodeMacOSFolderName(bool $double_encode = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Percent-encode macOS-forbidden characters inside folder names—forward slashes, colons, percent signs, and null bytes—while
leaving all other characters untouched. Decoding reproduces the original directory name exactly.

## Important notes and considerations

- **Colon handling.** `:` is escaped to `%3A` to satisfy HFS+/APFS.
- **Optional double encoding.** Use `$double_encode = true` to re-encode already escaped sequences.
- **Matches Unix behaviour.** `/`, `%`, and `\0` follow the same rules as Unix encoding.
- **Round-trip safe.** Use with [`decodeMacOSFolderName()`](decodeMacOSFolderName.md) to restore the original name.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | macOS-safe folder name with forbidden characters encoded. |

## Examples

### Escape embedded separators

<!-- test:mac-encode-folder-slash -->
```php
use Orryv\XString;

$value = XString::new('config/nginx');
$result = $value->encodeMacOSFolderName();

#Test: self::assertSame('config%2Fnginx', (string) $result);
```

### Escape colon characters

<!-- test:mac-encode-folder-colon -->
```php
use Orryv\XString;

$value = XString::new('cache:tmp');
$result = $value->encodeMacOSFolderName();

#Test: self::assertSame('cache%3Atmp', (string) $result);
```

### Escape percent signs

<!-- test:mac-encode-folder-percent -->
```php
use Orryv\XString;

$value = XString::new('cache%data');
$result = $value->encodeMacOSFolderName();

#Test: self::assertSame('cache%25data', (string) $result);
```

### Leave Unicode intact

<!-- test:mac-encode-folder-unicode -->
```php
use Orryv\XString;

$value = XString::new('データ');
$result = $value->encodeMacOSFolderName();

#Test: self::assertSame('データ', (string) $result);
```

### Control double encoding

<!-- test:mac-encode-folder-name-double-encode-toggle -->
```php
use Orryv\XString;

$value = XString::new('Projects%202024:Specs');

$noDouble = $value->encodeMacOSFolderName();
$double = $value->encodeMacOSFolderName(true);

#Test: self::assertSame('Projects%202024%3ASpecs', (string) $noDouble);
#Test: self::assertSame('Projects%252024%253ASpecs', (string) $double);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::encodeMacOSFolderName` | `public function encodeMacOSFolderName(bool $double_encode = false): self` — Percent-encode `/`, `:`, `%`, and null bytes inside macOS folder names. |
