# XString::transliterate()

## Table of Contents
- [XString::transliterate()](#xstringtransliterate)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Default ASCII transliteration](#default-ascii-transliteration)
    - [Ignore unsupported characters](#ignore-unsupported-characters)
    - [Transliterate to ISO-8859-1](#transliterate-to-iso-8859-1)
    - [Invalid transliterator identifiers are rejected](#invalid-transliterator-identifiers-are-rejected)
    - [Unknown encodings trigger runtime errors](#unknown-encodings-trigger-runtime-errors)
    - [Original instance remains unchanged](#original-instance-remains-unchanged-1)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function transliterate(string $to = 'ASCII//TRANSLIT'): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Converts the underlying string to a different character set while attempting to approximate characters that do not exist in the
destination alphabet. When the [intl extension](https://www.php.net/manual/en/book.intl.php) is available, you can pass any
valid transliterator identifier (e.g. `Any-Latin; Latin-ASCII`). Otherwise, the method falls back to `iconv`, so encodings such
as `ASCII//TRANSLIT` continue to work on standard PHP installations.

## Important notes and considerations

- **Immutable operation.** A brand-new `XString` instance is returned with the same mode and updated encoding metadata.
- **Encoding metadata.** When using `iconv`, the stored encoding is set to the base part of `$to` (e.g. `ASCII` for
  `ASCII//TRANSLIT`). Transliterator identifiers keep the original encoding.
- **Extension requirements.** Passing transliterator IDs without `//` requires the intl extension. Fallbacks will raise a
  `RuntimeException` if `iconv` is not available.
- **Validation.** Empty targets and unknown transliterator identifiers raise `InvalidArgumentException`.
- **Raw bytes.** The returned bytes are in the requested encoding. Convert them back to UTF-8 (e.g. via `iconv`) when you need
  to make assertions in a UTF-8 test suite.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$to` | `string` | `'ASCII//TRANSLIT'` | Target encoding or transliterator identifier. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | New `XString` instance containing the transliterated value. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | `$to` is empty or refers to an unknown transliterator identifier. |
| `RuntimeException` | Required extensions are unavailable or the conversion fails. |

## Examples

### Default ASCII transliteration

<!-- test:transliterate-default -->
```php
use Orryv\XString;

$title = XString::new('façade déjà vu');
$result = $title->transliterate();

#Test: self::assertSame('facade deja vu', (string) $result);
#Test: self::assertNotSame($title, $result);
```

### Ignore unsupported characters

<!-- test:transliterate-ignore -->
```php
use Orryv\XString;

$value = XString::new('Smörgåsbord 🍣');
$result = $value->transliterate('ASCII//TRANSLIT//IGNORE');

#Test: self::assertSame('Smorgasbord ?', (string) $result);
```

### Transliterate to ISO-8859-1

<!-- test:transliterate-iso -->
```php
use Orryv\XString;

$value = XString::new('Zażółć gęślą jaźń');
$result = $value->transliterate('ISO-8859-1//TRANSLIT');
$utf8View = iconv('ISO-8859-1', 'UTF-8', (string) $result);

#Test: self::assertSame('Zazólc gesla jazn', $utf8View);
```

### Invalid transliterator identifiers are rejected

<!-- test:transliterate-invalid-id -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('пример');

#Test: $this->expectException(InvalidArgumentException::class);
$value->transliterate('Unknown-ID');
```

### Unknown encodings trigger runtime errors

<!-- test:transliterate-invalid-encoding -->
```php
use Orryv\XString;
use InvalidArgumentException;

$value = XString::new('text');

#Test: $this->expectException(InvalidArgumentException::class);
$value->transliterate('INVALID-ENCODING');
```

### Original instance remains unchanged

<!-- test:transliterate-immutability -->
```php
use Orryv\XString;

$value = XString::new('über cool');
$value->transliterate();

#Test: self::assertSame('über cool', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::transliterate` | `public function transliterate(string $to = 'ASCII//TRANSLIT'): self` — Convert the string to another character set (optionally using ICU transliterator IDs) while approximating characters that cannot be represented exactly. |
