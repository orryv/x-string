# XString::decodeHtmlEntities()

## Table of Contents
- [XString::decodeHtmlEntities()](#xstringdecodehtmlentities)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Decode HTML entities back to plain text](#decode-html-entities-back-to-plain-text)
    - [Decode using specific flags](#decode-using-specific-flags)
    - [Respect a custom encoding](#respect-a-custom-encoding)
    - [Original instance remains unchanged](#original-instance-remains-unchanged-1)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function decodeHtmlEntities(int $flags = ENT_QUOTES | ENT_HTML401, ?string $encoding = null): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Decode HTML entities contained within the string using [`html_entity_decode`](https://www.php.net/html-entity-decode).
The method mirrors PHP's native defaults while letting you tune the decoding flags and override the character encoding used for
conversion.

## Important notes and considerations

- **Encoding-aware.** Defaults to the current instance encoding unless explicitly overridden.
- **Selective decoding.** Use `$flags` to restrict the set of entities that are translated back to characters.
- **Immutable result.** Returns a new `XString`, leaving the original untouched.

## Parameters

| Parameter | Type | Default | Description |
| --- | --- | --- | --- |
| `$flags` | `int` | `ENT_QUOTES \| ENT_HTML401` | Flags passed through to `html_entity_decode()`.
| `$encoding` | `?string` | `null` | Encoding label used during decoding. Defaults to the current instance encoding. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | New `XString` instance containing the decoded string. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | Propagated if `$encoding` normalisation fails (empty string). |

## Examples

### Decode HTML entities back to plain text

<!-- test:decode-html-entities-basic -->
```php
use Orryv\XString;

$value = XString::new('&lt;strong&gt;Bold &amp; clear&lt;/strong&gt;');
$result = $value->decodeHtmlEntities();

#Test: self::assertSame('<strong>Bold & clear</strong>', (string) $result);
```

### Decode using specific flags

<!-- test:decode-html-entities-flags -->
```php
use Orryv\XString;

$value = XString::new('&lt;quotes&gt;&amp;&apos;');
$result = $value->decodeHtmlEntities(ENT_NOQUOTES | ENT_HTML5);

#Test: self::assertSame('<quotes>&&apos;', (string) $result);
```

### Respect a custom encoding

Convert to ISO-8859-1 before decoding, then back to UTF-8 afterwards so the override round-trips predictably.

<!-- test:decode-html-entities-encoding -->
```php
use Orryv\XString;

$value = XString::new('Espa&ntilde;a')->toEncoding('ISO-8859-1');
$result = $value
    ->decodeHtmlEntities(ENT_QUOTES | ENT_HTML401, 'ISO-8859-1')
    ->toEncoding('UTF-8');

#Test: self::assertSame('España', (string) $result);
```

### Original instance remains unchanged

<!-- test:decode-html-entities-immutability -->
```php
use Orryv\XString;

$value = XString::new('&amp;copy; 2024');
$value->decodeHtmlEntities();

#Test: self::assertSame('&amp;copy; 2024', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::decodeHtmlEntities` | `public function decodeHtmlEntities(int $flags = ENT_QUOTES \| ENT_HTML401, ?string $encoding = null): self` — Decode HTML entities into their literal characters while honouring custom flags and encodings. |
