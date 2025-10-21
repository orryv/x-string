# XString::encodeHtmlEntities()

## Table of Contents
- [XString::encodeHtmlEntities()](#xstringencodehtmlentities)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Encode special HTML characters](#encode-special-html-characters)
    - [Preserve existing entities by default](#preserve-existing-entities-by-default)
    - [Enable double encoding when required](#enable-double-encoding-when-required)
    - [Specify custom flags and encoding](#specify-custom-flags-and-encoding)
    - [Original instance remains unchanged](#original-instance-remains-unchanged)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function encodeHtmlEntities(
    int $flags = ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401,
    ?string $encoding = null,
    bool $double_encode = false
): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Convert the underlying value into an HTML entity encoded string via [`htmlentities`](https://www.php.net/htmlentities).
The method defaults to encoding both single and double quotes while substituting unknown bytes. Double encoding is disabled by
default so existing entities are preserved. You can customise the behaviour with standard `htmlentities()` flags, override the
target encoding, or opt into double encoding explicitly when needed.

## Important notes and considerations

- **Respects the active encoding.** When `$encoding` is omitted the current instance encoding is reused.
- **Handles transcoding automatically.** Overrides temporarily convert the value so manual encoding swaps aren't necessary.
- **Customise encoding semantics.** Provide flags/encoding to align with HTML5, XML, or legacy HTML behaviours.
- **Double encoding disabled by default.** Existing entities remain untouched unless `$double_encode` is `true`.
- **Immutable operation.** Returns a new `XString`—the original value is never modified.

## Parameters

| Parameter | Type | Default | Description |
| --- | --- | --- | --- |
| `$flags` | `int` | `ENT_QUOTES \| ENT_SUBSTITUTE \| ENT_HTML401` | Flags forwarded to `htmlentities()`.
| `$encoding` | `?string` | `null` | Target encoding label. Defaults to the current instance encoding.
| `$double_encode` | `bool` | `false` | Whether to encode existing entities again (same semantics as `htmlentities()`). |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | New `XString` instance containing the encoded HTML string. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | Propagated if `$encoding` normalisation fails (empty string). |

## Examples

### Encode special HTML characters

<!-- test:encode-html-entities-basic -->
```php
use Orryv\XString;

$value = XString::new('<span>Me & You</span>');
$result = $value->encodeHtmlEntities();

#Test: self::assertSame('&lt;span&gt;Me &amp; You&lt;/span&gt;', (string) $result);
```

### Preserve existing entities by default

<!-- test:encode-html-entities-double-encode -->
```php
use Orryv\XString;

$value = XString::new('Already &amp; escaped');
$result = $value->encodeHtmlEntities();

#Test: self::assertSame('Already &amp; escaped', (string) $result);
```

### Enable double encoding when required

<!-- test:encode-html-entities-force-double -->
```php
use Orryv\XString;

$value = XString::new('Already &amp; escaped');
$result = $value->encodeHtmlEntities(ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, null, true);

#Test: self::assertSame('Already &amp;amp; escaped', (string) $result);
```

### Specify custom flags and encoding

Override the encoding directly—`XString` will handle any required conversions.

<!-- test:encode-html-entities-flags -->
```php
use Orryv\XString;

$value = XString::new("Café");
$result = $value->encodeHtmlEntities(ENT_NOQUOTES | ENT_SUBSTITUTE, 'ISO-8859-1');

#Test: self::assertSame('Caf&eacute;', (string) $result);
```

### Original instance remains unchanged

<!-- test:encode-html-entities-immutability -->
```php
use Orryv\XString;

$value = XString::new('Rock & Roll');
$value->encodeHtmlEntities();

#Test: self::assertSame('Rock & Roll', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::encodeHtmlEntities` | `public function encodeHtmlEntities(int $flags = ENT_QUOTES \| ENT_SUBSTITUTE \| ENT_HTML401, ?string $encoding = null, bool $double_encode = false): self` — Encode the string into HTML entities with fine-grained control over flags, encoding, and whether to double encode existing entities. |
