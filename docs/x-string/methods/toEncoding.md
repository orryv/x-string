# XString::toEncoding()

## Table of Contents
- [XString::toEncoding()](#xstringtoencoding)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Convert UTF-8 to ISO-8859-1](#convert-utf-8-to-iso-8859-1)
    - [Specify the source encoding manually](#specify-the-source-encoding-manually)
    - [Automatic detection falls back to current encoding](#automatic-detection-falls-back-to-current-encoding)
    - [Empty target encodings are rejected](#empty-target-encodings-are-rejected)
    - [Unknown encodings raise runtime errors](#unknown-encodings-raise-runtime-errors)
    - [Original instance remains unchanged](#original-instance-remains-unchanged-2)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toEncoding(string $to_encoding, ?string $from_encoding = null): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Converts the string to a different character encoding. When the source encoding is not provided, the method attempts to detect
it (using `mb_detect_encoding()` when available) and falls back to the instance's current encoding. Conversion is performed via
`mb_convert_encoding()` or `iconv()`, whichever is available in the runtime.

## Important notes and considerations

- **Immutable conversion.** The original instance is never modified; a new `XString` is returned with updated encoding metadata.
- **Detection order.** Without `$from_encoding`, the detector checks the current encoding first, followed by the requested target
  and common fallbacks (`UTF-8`, `ISO-8859-1`, `ASCII`).
- **Transliteration flags.** You can pass options such as `//IGNORE` or `//TRANSLIT` as part of `$to_encoding` to influence the
  conversion behaviour.
- **Validation.** Empty `$to_encoding` values raise `InvalidArgumentException`; conversion failures surface as
  `RuntimeException`.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$to_encoding` | `string` | — | Desired destination encoding (supports `//TRANSLIT`, `//IGNORE`, etc.). |
| `$from_encoding` | `null\|string` | `null` | Source encoding. When `null`, the method attempts automatic detection. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` representing the value in the requested encoding. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | `$to_encoding` or `$from_encoding` is empty after trimming. |
| `RuntimeException` | No available extension can perform the conversion or the encoding pair is unsupported. |

## Examples

### Convert UTF-8 to ISO-8859-1

<!-- test:to-encoding-iso -->
```php
use Orryv\XString;

$value = XString::new('Café au lait');
$converted = $value->toEncoding('ISO-8859-1');

#Test: self::assertSame("Caf\xE9 au lait", (string) $converted);
```

### Specify the source encoding manually

<!-- test:to-encoding-from -->
```php
use Orryv\XString;

$utf16le = pack('v*', 0x0048, 0x0069, 0x0021); // "Hi!" in UTF-16LE
$text = XString::new($utf16le);
$converted = $text->toEncoding('UTF-8', from_encoding: 'UTF-16LE');

#Test: self::assertSame('Hi!', (string) $converted);
```

### Automatic detection falls back to current encoding

<!-- test:to-encoding-detect -->
```php
use Orryv\XString;

$value = XString::new('Grüße');
$ascii = $value->toEncoding('ASCII//TRANSLIT');

#Test: self::assertSame('Grusse', (string) $ascii);
```

### Empty target encodings are rejected

<!-- test:to-encoding-empty -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('sample');

#Test: $this->expectException(InvalidArgumentException::class);
$value->toEncoding('   ');
```

### Unknown encodings raise runtime errors

<!-- test:to-encoding-invalid -->
```php
use Orryv\XString;
use RuntimeException;

$value = XString::new('content');

#Test: $this->expectException(RuntimeException::class);
$value->toEncoding('NO-SUCH-ENCODING');
```

### Original instance remains unchanged

<!-- test:to-encoding-immutability -->
```php
use Orryv\XString;

$value = XString::new('Café');
$value->toEncoding('ASCII//TRANSLIT');

#Test: self::assertSame('Café', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toEncoding` | `public function toEncoding(string $to_encoding, ?string $from_encoding = null): self` — Convert the string to another encoding (optionally detecting the source) using `mb_convert_encoding()`/`iconv()`, keeping the original immutable. |
