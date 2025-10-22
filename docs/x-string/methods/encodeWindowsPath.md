# XString::encodeWindowsPath()

## Table of Contents
- [XString::encodeWindowsPath()](#xstringencodewindowspath)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Encode forbidden characters in segments](#encode-forbidden-characters-in-segments)
    - [Handle reserved device names safely](#handle-reserved-device-names-safely)
    - [Encode trailing spaces in folder segments](#encode-trailing-spaces-in-folder-segments)
    - [Escape literal percent signs within segments](#escape-literal-percent-signs-within-segments)
    - [Control double encoding](#control-double-encoding)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function encodeWindowsPath(bool $double_encode = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Normalise a path to Windows-friendly form by converting `/` separators to `\\` and percent-encoding characters that are not
allowed in Windows path segments. Drive prefixes and UNC roots are preserved while each component is encoded individually so the
result can be decoded back to the original path string.

## Important notes and considerations

- **Per-segment encoding.** Each component between separators is encoded using [`encodeWindowsFileName()`](encodeWindowsFileName.md).
- **Optional double encoding.** Pass `$double_encode = true` to re-escape already-encoded `%XX` sequences inside path segments; by default they are left unchanged.
- **Separators preserved.** Existing `\\` runs are maintained; `/` is canonicalised to `\\` before encoding.
- **Reserved names protected.** Device names such as `CON` or `AUX` inside the path are percent-encoded to avoid collisions.
- **Trailing whitespace encoded.** Segments ending with spaces or periods are percent-encoded so Windows keeps them intact.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Windows-safe path string with percent-encoded segments. |

## Examples

### Encode forbidden characters in segments

<!-- test:windows-encode-path-forbidden -->
```php
use Orryv\XString;

$value = XString::new('C:\\logs\\error?.txt');
$result = $value->encodeWindowsPath();

#Test: self::assertSame('C:\\logs\\error%3F.txt', (string) $result);
```

### Handle reserved device names safely

<!-- test:windows-encode-path-reserved -->
```php
use Orryv\XString;

$value = XString::new('\\\\server\\share\\aux');
$result = $value->encodeWindowsPath();

#Test: self::assertSame('\\\\server\\share\\%61ux', (string) $result);
```

### Encode trailing spaces in folder segments

<!-- test:windows-encode-path-trailing -->
```php
use Orryv\XString;

$value = XString::new('C:\\data \\');
$result = $value->encodeWindowsPath();

#Test: self::assertSame('C:\\data%20\\', (string) $result);
```

### Escape literal percent signs within segments

<!-- test:windows-encode-path-percent -->
```php
use Orryv\XString;

$value = XString::new('D:\\reports\\100% ready');
$result = $value->encodeWindowsPath();

#Test: self::assertSame('D:\\reports\\100%25 ready', (string) $result);
```

### Control double encoding

<!-- test:windows-encode-path-double-encode-toggle -->
```php
use Orryv\XString;

$value = XString::new('C:\\Archive%202024\\Logs?.txt');

$noDouble = $value->encodeWindowsPath();
$double = $value->encodeWindowsPath(true);

#Test: self::assertSame('C:\\Archive%202024\\Logs%3F.txt', (string) $noDouble);
#Test: self::assertSame('C:\\Archive%252024\\Logs%253F.txt', (string) $double);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::encodeWindowsPath` | `public function encodeWindowsPath(bool $double_encode = false): self` — Percent-encode forbidden Windows path characters segment by segment while preserving drive and UNC prefixes. |
