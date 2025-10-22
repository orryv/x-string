# XString::decodeWindowsFolderName()

## Table of Contents
- [XString::decodeWindowsFolderName()](#xstringdecodewindowsfoldername)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Recreate literal slashes](#recreate-literal-slashes)
    - [Recover reserved device names](#recover-reserved-device-names-1)
    - [Restore trailing whitespace](#restore-trailing-whitespace)
    - [Decode control characters](#decode-control-characters)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function decodeWindowsFolderName(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Reverse the percent-encoding performed by [`encodeWindowsFolderName()`](encodeWindowsFolderName.md). Every `%XX` sequence is
converted back to its original byte so folder names containing slashes, device identifiers, control characters, or trailing
whitespace can be restored exactly as they were before encoding.

## Important notes and considerations

- **No validation.** The method only decodes percent sequences; it does not enforce Windows rules on the result.
- **Symmetric behaviour.** Values produced by `encodeWindowsFolderName()` round-trip through this method without data loss.
- **Generic decoding.** Any valid `%` hex escape is decoded, even if it was not produced by the encoder.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Decoded folder name with the original characters restored. |

## Examples

### Recreate literal slashes

<!-- test:windows-decode-folder-slash -->
```php
use Orryv\XString;

$value = XString::new('config%2Fapp');
$result = $value->decodeWindowsFolderName();

#Test: self::assertSame('config/app', (string) $result);
```

### Recover reserved device names

<!-- test:windows-decode-folder-reserved -->
```php
use Orryv\XString;

$value = XString::new('%41UX');
$result = $value->decodeWindowsFolderName();

#Test: self::assertSame('AUX', (string) $result);
```

### Restore trailing whitespace

<!-- test:windows-decode-folder-trailing -->
```php
use Orryv\XString;

$value = XString::new('data%20%2E');
$result = $value->decodeWindowsFolderName();

#Test: self::assertSame('data .', (string) $result);
```

### Decode control characters

<!-- test:windows-decode-folder-control -->
```php
use Orryv\XString;

$value = XString::new('cache%07');
$result = $value->decodeWindowsFolderName();

#Test: self::assertSame("cache\x07", (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::decodeWindowsFolderName` | `public function decodeWindowsFolderName(): self` — Decode `%XX` escapes in Windows-safe folder names to restore the original spelling. |
