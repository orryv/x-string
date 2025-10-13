# XString::htmlUnescape()

## Table of Contents
- [XString::htmlUnescape()](#xstringhtmlunescape)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Decode escaped HTML elements](#decode-escaped-html-elements)
    - [HTML5 entities are handled](#html5-entities-are-handled)
    - [Mode preservation with bytes](#mode-preservation-with-bytes)
    - [Mixed content with ampersands](#mixed-content-with-ampersands)
    - [Empty strings stay empty](#empty-strings-stay-empty)
    - [Original instance remains unchanged](#original-instance-remains-unchanged)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function htmlUnescape(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Decodes HTML entities such as `&amp;`, `&lt;`, and their HTML5 counterparts back into literal characters. The operation is safe for already plain text strings and returns a fresh immutable `XString` instance.

## Important notes and considerations

- **HTML5 entity coverage.** Uses `html_entity_decode()` with HTML5 compatibility, so entities like `&apos;` and `&eacute;` are also unescaped.
- **Idempotent on plain text.** Calling the method on content without entities leaves the string untouched.
- **Encoding preserved.** The resulting instance keeps the same encoding and iteration mode as the original.

## Parameters

This method does not accept parameters.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | New `XString` with HTML entities converted to their literal characters. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Decode escaped HTML elements

<!-- test:html-unescape-basic -->
```php
use Orryv\XString;

$value = XString::new('&lt;div&gt;Hello&lt;/div&gt;');
$result = $value->htmlUnescape();

#Test: self::assertSame('<div>Hello</div>', (string) $result);
```

### HTML5 entities are handled

<!-- test:html-unescape-html5-entities -->
```php
use Orryv\XString;

$value = XString::new('&apos;alpha&apos; &amp; &quot;beta&quot;');
$result = $value->htmlUnescape();

#Test: self::assertSame("'alpha' & \"beta\"", (string) $result);
```

### Mode preservation with bytes

<!-- test:html-unescape-bytes-mode -->
```php
use Orryv\XString;

$value = XString::new('&lt;Caf&eacute;&gt;')->withMode('bytes');
$result = $value->htmlUnescape();

#Test: self::assertSame('<Café>', (string) $result);
```

### Mixed content with ampersands

<!-- test:html-unescape-mixed -->
```php
use Orryv\XString;

$value = XString::new('Fish &amp; Chips &amp; More');
$result = $value->htmlUnescape();

#Test: self::assertSame('Fish & Chips & More', (string) $result);
```

### Empty strings stay empty

<!-- test:html-unescape-empty -->
```php
use Orryv\XString;

$result = XString::new('')->htmlUnescape();

#Test: self::assertSame('', (string) $result);
```

### Original instance remains unchanged

<!-- test:html-unescape-immutable -->
```php
use Orryv\XString;

$value = XString::new('&lt;b&gt;bold&lt;/b&gt;');
$value->htmlUnescape();

#Test: self::assertSame('&lt;b&gt;bold&lt;/b&gt;', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::htmlUnescape` | `public function htmlUnescape(): self` — Decode HTML entities (HTML5 compatible) back into literal characters while preserving immutability. |
