# XString::encodeSafeFolderName()

## Table of Contents
- [XString::encodeSafeFolderName()](#xstringencodesafefoldername)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Escape slashes and backslashes](#escape-slashes-and-backslashes)
    - [Escape colon characters](#escape-colon-characters-2)
    - [Encode trailing spaces](#encode-trailing-spaces)
    - [Escape percent signs](#escape-percent-signs-3)
    - [Control double encoding](#control-double-encoding)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function encodeSafeFolderName(bool $double_encode = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Percent-encode characters that are unsafe on any supported filesystem (Windows, macOS, Unix) inside folder names. Forbidden
slashes, backslashes, colons, percent signs, reserved device names, and trailing whitespace are encoded so the result can be
decoded on any platform.

## Important notes and considerations

- **Cross-platform rules.** Encoding mirrors the strictest filesystem requirements (Windows + macOS).
- **Optional double encoding.** Use `$double_encode = true` to re-encode existing `%XX` sequences when necessary.
- **Reserved names encoded.** Device names are prefixed with an encoded character to avoid collisions.
- **Percent signs doubled.** `%` becomes `%25` to keep escapes unambiguous.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Portable folder name with unsafe characters percent-encoded. |

## Examples

### Escape slashes and backslashes

<!-- test:safe-encode-folder-slash -->
```php
use Orryv\XString;

$value = XString::new('config/nginx');
$result = $value->encodeSafeFolderName();

#Test: self::assertSame('config%2Fnginx', (string) $result);
```

### Escape colon characters

<!-- test:safe-encode-folder-colon -->
```php
use Orryv\XString;

$value = XString::new('cache:tmp');
$result = $value->encodeSafeFolderName();

#Test: self::assertSame('cache%3Atmp', (string) $result);
```

### Encode trailing spaces

<!-- test:safe-encode-folder-trailing -->
```php
use Orryv\XString;

$value = XString::new('data .');
$result = $value->encodeSafeFolderName();

#Test: self::assertSame('data%20%2E', (string) $result);
```

### Escape percent signs

<!-- test:safe-encode-folder-percent -->
```php
use Orryv\XString;

$value = XString::new('cache%data');
$result = $value->encodeSafeFolderName();

#Test: self::assertSame('cache%25data', (string) $result);
```

### Control double encoding

<!-- test:safe-encode-folder-name-double-encode-toggle -->
```php
use Orryv\XString;

$value = XString::new('Reports%202024?');

$noDouble = $value->encodeSafeFolderName();
$double = $value->encodeSafeFolderName(true);

#Test: self::assertSame('Reports%202024%3F', (string) $noDouble);
#Test: self::assertSame('Reports%252024%253F', (string) $double);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::encodeSafeFolderName` | `public function encodeSafeFolderName(bool $double_encode = false): self` — Percent-encode folder-name characters that are unsafe on any supported OS. |
