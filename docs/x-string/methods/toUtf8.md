# XString::toUtf8()

## Table of Contents
- [XString::toUtf8()](#xstringtoutf8)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Convert ISO-8859-1 text to UTF-8](#convert-iso-8859-1-text-to-utf-8)
    - [Auto-detect the source encoding](#auto-detect-the-source-encoding)
    - [Empty encoding names are rejected](#empty-encoding-names-are-rejected)
    - [Unknown encodings trigger runtime errors](#unknown-encodings-trigger-runtime-errors)
    - [The original instance remains unchanged](#the-original-instance-remains-unchanged)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toUtf8(null|string $from_encoding = null): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Converts the underlying value to UTF-8. When `$from_encoding` is omitted the method attempts to detect a suitable source
encoding using `mb_detect_encoding()` (falling back to `iconv()` heuristics). The returned instance always stores UTF-8 bytes and
keeps the original mode (bytes/codepoints/graphemes).

## Important notes and considerations

- **Immutable operation.** A new `XString` instance is returned.
- **Detection order.** When auto-detecting, the method favours the current internal encoding, then UTF-8, ISO-8859-1 and ASCII.
- **Extension requirements.** Either `mbstring` or `iconv` must be available; otherwise a `RuntimeException` is thrown.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$from_encoding` | `null\|string` | `null` | Explicit source encoding. When `null`, detection heuristics are used. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | New `XString` instance containing the UTF-8 encoded bytes. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | `$from_encoding` is an empty string. |
| `RuntimeException` | The conversion fails or required extensions are missing. |

## Examples

### Convert ISO-8859-1 text to UTF-8

<!-- test:to-utf8-latin1 -->
```php
use Orryv\XString;

$latin1 = iconv('UTF-8', 'ISO-8859-1', 'Café déjà vu');
$value = XString::new($latin1);
$result = $value->toUtf8('ISO-8859-1');

#Test: self::assertSame('Café déjà vu', (string) $result);
#Test: self::assertNotSame($value, $result);
```

### Auto-detect the source encoding

<!-- test:to-utf8-detect -->
```php
use Orryv\XString;

$source = iconv('UTF-8', 'ISO-8859-1', 'Mañana será otro día');
$value = XString::new($source);
$result = $value->toUtf8();

#Test: self::assertSame('Mañana será otro día', (string) $result);
```

### Empty encoding names are rejected

<!-- test:to-utf8-empty -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('text');

#Test: $this->expectException(InvalidArgumentException::class);
$value->toUtf8('');
```

### Unknown encodings trigger runtime errors

<!-- test:to-utf8-runtime -->
```php
use Orryv\XString;
use RuntimeException;

$value = XString::new('sample');

#Test: $this->expectException(RuntimeException::class);
$value->toUtf8('INVALID-ENCODING');
```

### The original instance remains unchanged

<!-- test:to-utf8-immutability -->
```php
use Orryv\XString;

$value = XString::new('Übermäßig');
$value->toUtf8('UTF-8');

#Test: self::assertSame('Übermäßig', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toUtf8` | `public function toUtf8(null\|string $from_encoding = null): self` — Convert the value to UTF-8, optionally providing the original encoding. |
