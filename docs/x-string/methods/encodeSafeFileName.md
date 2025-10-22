# XString::encodeSafeFileName()

## Table of Contents
- [XString::encodeSafeFileName()](#xstringencodesafefilename)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Escape characters forbidden on any platform](#escape-characters-forbidden-on-any-platform)
    - [Protect reserved device names](#protect-reserved-device-names-1)
    - [Encode trailing spaces and periods](#encode-trailing-spaces-and-periods)
    - [Escape percent signs](#escape-percent-signs-2)
    - [Control double encoding](#control-double-encoding)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function encodeSafeFileName(bool $double_encode = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Produce a cross-platform safe filename by percent-encoding any character that is illegal on Windows, macOS, or Unix—namely
slashes, colons, backslashes, reserved device names, control codes, percent signs, and trailing whitespace. The output can be
decoded losslessly with [`decodeSafeFileName()`](decodeSafeFileName.md).

## Important notes and considerations

- **Union of restrictions.** The method applies the strictest filesystem rules (Windows) to maximise portability.
- **Optional double encoding.** Pass `$double_encode = true` to re-encode existing `%XX` sequences when sanitising already-safe input.
- **Reserved words handled.** Device names like `CON` or `AUX` are prefixed with an encoded character to avoid collisions.
- **Percent signs escaped.** `%` becomes `%25` so decoding can distinguish literal percent signs from escape markers.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Portable filename with unsafe characters percent-encoded. |

## Examples

### Escape characters forbidden on any platform

<!-- test:safe-encode-filename-forbidden -->
```php
use Orryv\XString;

$value = XString::new('Report?.txt');
$result = $value->encodeSafeFileName();

#Test: self::assertSame('Report%3F.txt', (string) $result);
```

### Protect reserved device names

<!-- test:safe-encode-filename-reserved -->
```php
use Orryv\XString;

$value = XString::new('CON');
$result = $value->encodeSafeFileName();

#Test: self::assertSame('%43ON', (string) $result);
```

### Encode trailing spaces and periods

<!-- test:safe-encode-filename-trailing -->
```php
use Orryv\XString;

$value = XString::new('log .');
$result = $value->encodeSafeFileName();

#Test: self::assertSame('log%20%2E', (string) $result);
```

### Escape percent signs

<!-- test:safe-encode-filename-percent -->
```php
use Orryv\XString;

$value = XString::new('Invoice 100% complete');
$result = $value->encodeSafeFileName();

#Test: self::assertSame('Invoice 100%25 complete', (string) $result);
```

### Control double encoding

<!-- test:safe-encode-filename-double-encode-toggle -->
```php
use Orryv\XString;

$value = XString::new('Archive%202024?.zip');

$noDouble = $value->encodeSafeFileName();
$double = $value->encodeSafeFileName(true);

#Test: self::assertSame('Archive%202024%3F.zip', (string) $noDouble);
#Test: self::assertSame('Archive%252024%253F.zip', (string) $double);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::encodeSafeFileName` | `public function encodeSafeFileName(bool $double_encode = false): self` — Percent-encode filesystem-forbidden characters for maximum cross-platform safety. |
