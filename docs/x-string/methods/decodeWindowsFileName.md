# XString::decodeWindowsFileName()

## Table of Contents
- [XString::decodeWindowsFileName()](#xstringdecodewindowsfilename)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Restore forbidden characters](#restore-forbidden-characters)
    - [Recover reserved device names](#recover-reserved-device-names)
    - [Bring back trailing spaces and periods](#bring-back-trailing-spaces-and-periods)
    - [Decode escaped percent signs](#decode-escaped-percent-signs)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function decodeWindowsFileName(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Decode percent-encoded Windows filenames previously produced by [`encodeWindowsFileName()`](encodeWindowsFileName.md). All `%XX`
sequences are converted back to their byte values, restoring reserved characters, device names, and trailing whitespace exactly
as they appeared before encoding.

## Important notes and considerations

- **Generic `%` decoding.** Any `%` followed by two hex digits is transformed back into its byte representation.
- **Symmetric with encode.** Strings produced by `encodeWindowsFileName()` round-trip through this method without loss.
- **No sanitisation.** The method only decodes; it does not validate or modify characters that were not percent-encoded.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Original Windows filename restored from percent-encoded form. |

## Examples

### Restore forbidden characters

<!-- test:windows-decode-filename-forbidden -->
```php
use Orryv\XString;

$value = XString::new('Report%3F.txt');
$result = $value->decodeWindowsFileName();

#Test: self::assertSame('Report?.txt', (string) $result);
```

### Recover reserved device names

<!-- test:windows-decode-filename-reserved -->
```php
use Orryv\XString;

$value = XString::new('%43ON');
$result = $value->decodeWindowsFileName();

#Test: self::assertSame('CON', (string) $result);
```

### Bring back trailing spaces and periods

<!-- test:windows-decode-filename-trailing -->
```php
use Orryv\XString;

$value = XString::new('log%20%2E');
$result = $value->decodeWindowsFileName();

#Test: self::assertSame('log .', (string) $result);
```

### Decode escaped percent signs

<!-- test:windows-decode-filename-percent -->
```php
use Orryv\XString;

$value = XString::new('Invoice 100%25 complete');
$result = $value->decodeWindowsFileName();

#Test: self::assertSame('Invoice 100% complete', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::decodeWindowsFileName` | `public function decodeWindowsFileName(): self` — Decode `%XX` sequences in Windows-safe filenames to recover the original characters. |
