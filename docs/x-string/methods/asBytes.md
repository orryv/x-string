# XString::asBytes()

## Table of Contents
- [XString::asBytes()](#xstringasbytes)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Count raw bytes of a Unicode string](#count-raw-bytes-of-a-unicode-string)
    - [Provide a custom encoding](#provide-a-custom-encoding)
    - [Empty encoding throws an exception](#empty-encoding-throws-an-exception)
    - [Immutability when switching to byte mode](#immutability-when-switching-to-byte-mode)
    - [Encoding labels are trimmed](#encoding-labels-are-trimmed)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function asBytes(string $encoding = 'UTF-8'): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

A convenience alias for [`withMode('bytes', $encoding)`](./withMode.md). It returns a new immutable `XString` configured to
interpret offsets and lengths as raw byte counts while preserving the original text.

## Important notes and considerations

- **Alias semantics.** This method delegates to `withMode()` using the `bytes` mode.
- **Encoding validation.** The supplied `$encoding` must be a non-empty string that `mbstring` recognizes.
- **Immutable clone.** The original instance is never modified.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$encoding` | `string` | `'UTF-8'` | Encoding used when multibyte functions are required. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` configured to operate in byte mode with the requested encoding. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | Thrown when `$encoding` is an empty string. |

## Examples

### Count raw bytes of a Unicode string

<!-- test:as-bytes-length -->
```php
use Orryv\XString;

$xstring = XString::new("a\u{0301}");
$bytes = $xstring->asBytes();

#Test: self::assertSame(3, $bytes->length());
#Test: self::assertSame(1, $xstring->length());
```

### Provide a custom encoding

<!-- test:as-bytes-encoding -->
```php
use Orryv\XString;

$xstring = XString::new('hello');
$iso = $xstring->asBytes('ISO-8859-1');
$upper = $iso->toUpper();

#Test: self::assertSame('HELLO', (string) $upper);
#Test: self::assertSame('hello', (string) $xstring);
```

### Empty encoding throws an exception

<!-- test:as-bytes-empty-encoding -->
```php
use InvalidArgumentException;
use Orryv\XString;

$xstring = XString::new('example');

#Test: $this->expectException(InvalidArgumentException::class);
$xstring->asBytes('');
```

### Immutability when switching to byte mode

<!-- test:as-bytes-immutable -->
```php
use Orryv\XString;

$emoji = XString::new('👩‍💻');
$bytes = $emoji->asBytes();

#Test: self::assertSame(11, $bytes->length());
#Test: self::assertSame(1, $emoji->length());
#Test: self::assertNotSame($emoji, $bytes);
```

### Encoding labels are trimmed

<!-- test:as-bytes-trim-encoding -->
```php
use Orryv\XString;

$value = XString::new('Résumé');
$bytes = $value->asBytes('  UTF-8  ');
$upper = $bytes->toUpper();

#Test: self::assertSame('RÉSUMÉ', (string) $upper);
#Test: self::assertSame('Résumé', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::asBytes` | `public function asBytes(string $encoding = 'UTF-8'): self` — Alias for `withMode('bytes', $encoding)` returning a byte-mode clone. |
