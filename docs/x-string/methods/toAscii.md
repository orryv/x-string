# XString::toAscii()

## Table of Contents
- [XString::toAscii()](#xstringtoascii)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Basic transliteration to ASCII](#basic-transliteration-to-ascii)
    - [Provide the source encoding explicitly](#provide-the-source-encoding-explicitly)
    - [Extended Latin letters are approximated](#extended-latin-letters-are-approximated)
    - [Empty encoding names raise an exception](#empty-encoding-names-raise-an-exception)
    - [Unknown encodings surface runtime errors](#unknown-encodings-surface-runtime-errors)
    - [Original instance stays untouched](#original-instance-stays-untouched)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toAscii(null|string $from_encoding = null): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Converts the string to ASCII using `ASCII//TRANSLIT`, approximating characters that are not part of the 7-bit ASCII range. When
no `$from_encoding` is provided the method attempts to determine it automatically.

## Important notes and considerations

- **Immutable result.** A cloned `XString` is returned.
- **Transliteration.** Unsupported characters are approximated or replaced with placeholders when no close match exists.
- **Extension requirements.** Either `mbstring` or `iconv` must be present for conversion.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$from_encoding` | `null\|string` | `null` | Source encoding. `null` triggers detection heuristics. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | New `XString` instance containing ASCII bytes. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | `$from_encoding` is empty. |
| `RuntimeException` | Conversion failed or required extensions are missing. |

## Examples

### Basic transliteration to ASCII

<!-- test:to-ascii-basic -->
```php
use Orryv\XString;

$value = XString::new('Český Krumlov');
$result = $value->toAscii();

#Test: self::assertSame('Cesky Krumlov', (string) $result);
```

### Provide the source encoding explicitly

<!-- test:to-ascii-explicit -->
```php
use Orryv\XString;

$source = iconv('UTF-8', 'ISO-8859-1', 'Málaga, España');
$value = XString::new($source);
$result = $value->toAscii('ISO-8859-1');

#Test: self::assertSame('Malaga, Espana', (string) $result);
```

### Extended Latin letters are approximated

<!-- test:to-ascii-placeholder -->
```php
use Orryv\XString;

$value = XString::new('ÆØÅ på ferie');
$result = $value->toAscii();

#Test: self::assertSame('AEOA pa ferie', (string) $result);
```

### Empty encoding names raise an exception

<!-- test:to-ascii-empty -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('text');

#Test: $this->expectException(InvalidArgumentException::class);
$value->toAscii('');
```

### Unknown encodings surface runtime errors

<!-- test:to-ascii-runtime -->
```php
use Orryv\XString;
use RuntimeException;

$value = XString::new('example');

#Test: $this->expectException(RuntimeException::class);
$value->toAscii('UNKNOWN');
```

### Original instance stays untouched

<!-- test:to-ascii-immutability -->
```php
use Orryv\XString;

$value = XString::new('À la carte');
$value->toAscii();

#Test: self::assertSame('À la carte', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toAscii` | `public function toAscii(null\|string $from_encoding = null): self` — Convert the value to ASCII using transliteration when necessary. |
