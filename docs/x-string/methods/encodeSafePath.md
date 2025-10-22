# XString::encodeSafePath()

## Table of Contents
- [XString::encodeSafePath()](#xstringencodesafepath)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Encode drive prefixes and segments](#encode-drive-prefixes-and-segments)
    - [Handle UNC-style roots](#handle-unc-style-roots)
    - [Preserve trailing separators](#preserve-trailing-separators)
    - [Escape percent signs](#escape-percent-signs-4)
    - [Control double encoding](#control-double-encoding)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function encodeSafePath(bool $double_encode = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Convert any path into a cross-platform safe representation by normalising separators to `/` and percent-encoding characters that
are unsafe on Windows, macOS, or Unix. Each segment is encoded individually so decoding restores the original spelling.

## Important notes and considerations

- **Normalises separators.** Backslashes are converted to `/` before encoding for consistency.
- **Optional double encoding.** Set `$double_encode = true` to re-encode existing `%XX` sequences in already-sanitised segments.
- **Windows rules apply.** Drive letters and UNC prefixes are treated as segments and percent-encoded if needed.
- **Round-trip safe.** Use [`decodeSafePath()`](decodeSafePath.md) to reverse the transformation.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Portable path string with unsafe characters percent-encoded. |

## Examples

### Encode drive prefixes and segments

<!-- test:safe-encode-path-drive -->
```php
use Orryv\XString;

$value = XString::new('C:\\logs\\error?.txt');
$result = $value->encodeSafePath();

#Test: self::assertSame('C%3A/logs/error%3F.txt', (string) $result);
```

### Handle UNC-style roots

<!-- test:safe-encode-path-unc -->
```php
use Orryv\XString;

$value = XString::new('\\\\server\\share\\AUX');
$result = $value->encodeSafePath();

#Test: self::assertSame('//server/share/%41UX', (string) $result);
```

### Preserve trailing separators

<!-- test:safe-encode-path-trailing -->
```php
use Orryv\XString;

$value = XString::new('workspace\\');
$result = $value->encodeSafePath();

#Test: self::assertSame('workspace/', (string) $result);
```

### Escape percent signs

<!-- test:safe-encode-path-percent -->
```php
use Orryv\XString;

$value = XString::new('reports/100% ready');
$result = $value->encodeSafePath();

#Test: self::assertSame('reports/100%25 ready', (string) $result);
```

### Control double encoding

<!-- test:safe-encode-path-double-encode-toggle -->
```php
use Orryv\XString;

$value = XString::new('Archive%202024/Logs?.txt');

$noDouble = $value->encodeSafePath();
$double = $value->encodeSafePath(true);

#Test: self::assertSame('Archive%202024/Logs%3F.txt', (string) $noDouble);
#Test: self::assertSame('Archive%252024/Logs%253F.txt', (string) $double);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::encodeSafePath` | `public function encodeSafePath(bool $double_encode = false): self` — Produce a cross-platform safe path by percent-encoding forbidden characters in each segment and normalising separators to `/`. |
