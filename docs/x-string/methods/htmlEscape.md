# XString::htmlEscape()

## Table of Contents
- [XString::htmlEscape()](#xstringhtmlescape)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Escape special characters for HTML5](#escape-special-characters-for-html5)
    - [Leave quotes untouched with ENT_NOQUOTES](#leave-quotes-untouched-with-ent_noquotes)
    - [Specify a different encoding](#specify-a-different-encoding)
    - [Empty string stays empty](#empty-string-stays-empty)
    - [Invalid encoding triggers ValueError](#invalid-encoding-triggers-valueerror)
    - [Original instance remains unchanged](#original-instance-remains-unchanged-2)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function htmlEscape(int $flags = ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, string $encoding = 'UTF-8'): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Escapes HTML special characters using PHP's `htmlspecialchars()`. The default flags escape quotes, substitute invalid byte sequences, and target the HTML5 entity set.

## Important notes and considerations

- **Immutable operation.** Returns a new `XString` and keeps the original untouched.
- **Encoding aware.** Specify the target encoding when working with non-UTF-8 data.
- **Substitution of invalid bytes.** With the default `ENT_SUBSTITUTE` flag invalid sequences are replaced with the Unicode replacement character.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$flags` | `int` | `ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5` | Bitmask of `htmlspecialchars()` flags. |
| `$encoding` | `string` | `'UTF-8'` | The character set to use when escaping. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Escaped string packaged in a new `XString`. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `ValueError` | Raised by `htmlspecialchars()` for unsupported encodings or invalid flag combinations. |

## Examples

### Escape special characters for HTML5

<!-- test:html-escape-basic -->
```php
use Orryv\XString;

$value = XString::new("<a href='/?q=1&2'>Link</a>");
$result = $value->htmlEscape();

#Test: self::assertSame('&lt;a href=&apos;/?q=1&amp;2&apos;&gt;Link&lt;/a&gt;', (string) $result);
```

### Leave quotes untouched with ENT_NOQUOTES

<!-- test:html-escape-noquotes -->
```php
use Orryv\XString;

$value = XString::new('"quoted" & <tag>');
$result = $value->htmlEscape(ENT_NOQUOTES);

#Test: self::assertSame('"quoted" &amp; &lt;tag&gt;', (string) $result);
```

### Specify a different encoding

<!-- test:html-escape-encoding -->
```php
use Orryv\XString;

$bytes = "\xA0"; // non-breaking space (valid ISO-8859-1, invalid UTF-8)
$result = XString::new($bytes)->htmlEscape(ENT_QUOTES | ENT_HTML5, 'ISO-8859-1');

#Test: self::assertSame('a0', bin2hex((string) $result));
```

### Empty string stays empty

<!-- test:html-escape-empty -->
```php
use Orryv\XString;

$result = XString::new('')->htmlEscape();

#Test: self::assertSame('', (string) $result);
```

### Invalid encoding triggers ValueError

<!-- test:html-escape-invalid -->
```php
use Orryv\XString;
use ValueError;

#Test: $this->expectException(ValueError::class);
XString::new('text')->htmlEscape(ENT_QUOTES, 'FAKE-ENCODING');
```

### Original instance remains unchanged

<!-- test:html-escape-immutability -->
```php
use Orryv\XString;

$value = XString::new('<b>bold</b>');
$value->htmlEscape();

#Test: self::assertSame('<b>bold</b>', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::htmlEscape` | `public function htmlEscape(int $flags = ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, string $encoding = 'UTF-8'): self` — Escape HTML-sensitive characters using `htmlspecialchars()`. |
