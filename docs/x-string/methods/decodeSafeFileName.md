# XString::decodeSafeFileName()

## Table of Contents
- [XString::decodeSafeFileName()](#xstringdecodesafefilename)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Restore forbidden characters](#restore-forbidden-characters-1)
    - [Recover reserved device names](#recover-reserved-device-names-2)
    - [Bring back trailing whitespace](#bring-back-trailing-whitespace-1)
    - [Decode percent signs](#decode-percent-signs-1)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function decodeSafeFileName(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Decode percent-encoded filenames created by [`encodeSafeFileName()`](encodeSafeFileName.md). All `%XX` sequences are converted
back to their byte values so cross-platform safe names return to their original characters.

## Important notes and considerations

- **Generic `%` decoding.** Any valid `%` escape is translated back, not just ones produced by the encoder.
- **No validation.** The method does not sanitise the result; it merely reverses percent encoding.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Decoded filename restored from its portable form. |

## Examples

### Restore forbidden characters

<!-- test:safe-decode-filename-forbidden -->
```php
use Orryv\XString;

$value = XString::new('Report%3F.txt');
$result = $value->decodeSafeFileName();

#Test: self::assertSame('Report?.txt', (string) $result);
```

### Recover reserved device names

<!-- test:safe-decode-filename-reserved -->
```php
use Orryv\XString;

$value = XString::new('%43ON');
$result = $value->decodeSafeFileName();

#Test: self::assertSame('CON', (string) $result);
```

### Bring back trailing whitespace

<!-- test:safe-decode-filename-trailing -->
```php
use Orryv\XString;

$value = XString::new('log%20%2E');
$result = $value->decodeSafeFileName();

#Test: self::assertSame('log .', (string) $result);
```

### Decode percent signs

<!-- test:safe-decode-filename-percent -->
```php
use Orryv\XString;

$value = XString::new('Invoice 100%25 complete');
$result = $value->decodeSafeFileName();

#Test: self::assertSame('Invoice 100% complete', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::decodeSafeFileName` | `public function decodeSafeFileName(): self` — Decode `%XX` escapes in cross-platform filenames. |
