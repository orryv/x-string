# XString::normalize()

## Table of Contents
- [XString::normalize()](#xstringnormalize)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Convert decomposed accents to NFC](#convert-decomposed-accents-to-nfc)
    - [Normalize to canonical decomposition (NFD)](#normalize-to-canonical-decomposition-nfd)
    - [Apply compatibility normalization (NFKC)](#apply-compatibility-normalization-nfkc)
    - [Immutability check](#immutability-check-2)
    - [Invalid normalization form throws an exception](#invalid-normalization-form-throws-an-exception)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function normalize(int $form = Normalizer::FORM_C): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Normalizes the internal string using PHP's `Normalizer` extension. By default the string is converted to Normalization
Form C (NFC). You can select any other normalization form supported by the extension (NFD, NFKC, NFKD, etc.).

## Important notes and considerations

- **Requires intl extension.** The method depends on PHP's `intl` extension. A `RuntimeException` is thrown if it is missing.
- **Immutability.** Returns a new `XString`; the original instance remains untouched.
- **Error handling.** Passing an unsupported normalization form results in an `InvalidArgumentException`.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$form` | `int` | `Normalizer::FORM_C` | Unicode normalization form constant to apply. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` containing the normalized string while preserving mode and encoding. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `RuntimeException` | The intl `Normalizer` class is unavailable or normalization fails. |
| `InvalidArgumentException` | An invalid normalization form is provided. |

## Examples

### Convert decomposed accents to NFC

<!-- test:normalize-nfc -->
```php
use Orryv\XString;
use Normalizer;

$xstring = XString::new("Cafe\u{0301}");
$result = $xstring->normalize();
#Test: self::assertSame('Café', (string) $result);
#Test: self::assertTrue(Normalizer::isNormalized((string) $result, Normalizer::FORM_C));
```

### Normalize to canonical decomposition (NFD)

<!-- test:normalize-nfd -->
```php
use Orryv\XString;
use Normalizer;

$xstring = XString::new('Ångström');
$result = $xstring->normalize(Normalizer::FORM_D);
#Test: self::assertSame("A\u{030A}ngstro\u{0308}m", (string) $result);
#Test: self::assertTrue(Normalizer::isNormalized((string) $result, Normalizer::FORM_D));
```

### Apply compatibility normalization (NFKC)

<!-- test:normalize-nfkc -->
```php
use Orryv\XString;
use Normalizer;

$xstring = XString::new("Å");
$result = $xstring->normalize(Normalizer::FORM_KC);
#Test: self::assertSame('Å', (string) $result);
```

### Immutability check

<!-- test:normalize-immutability -->
```php
use Orryv\XString;

$xstring = XString::new("e\u{0301}");
$normalized = $xstring->normalize();
#Test: self::assertSame("e\u{0301}", (string) $xstring);
#Test: self::assertSame('é', (string) $normalized);
```

### Invalid normalization form throws an exception

<!-- test:normalize-invalid -->
```php
use InvalidArgumentException;
use Orryv\XString;

$xstring = XString::new('data');
#Test: $this->expectException(InvalidArgumentException::class);
$xstring->normalize(-1);
```

## One-line API table entry

| Method | Version | Signature & Description |
| --- | --- | --- |
| `normalize` | 1.0 | `public function normalize(int $form = Normalizer::FORM_C): self` — Normalize the string using the specified Unicode normalization form. |
