# XString::urlDecode()

## Table of Contents
- [XString::urlDecode()](#xstringurldecode)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Decode a classic query string fragment](#decode-a-classic-query-string-fragment)
    - [Raw mode preserves literal plus signs](#raw-mode-preserves-literal-plus-signs)
    - [UTF-8 sequences decode back to characters](#utf-8-sequences-decode-back-to-characters)
    - [Invalid percent sequences are left untouched](#invalid-percent-sequences-are-left-untouched)
    - [Empty strings stay empty](#empty-strings-stay-empty-2)
    - [Original instance remains unchanged](#original-instance-remains-unchanged-2)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function urlDecode(bool $raw = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Decodes percent-encoded strings produced by `urlEncode()` or PHP's URL functions. Default behaviour matches `urldecode()`, where `+` is converted to a space. Enable raw mode to call `rawurldecode()` instead and leave literal plus signs intact.

## Important notes and considerations

- **Mirrors urlEncode.** Use the same `$raw` flag you encoded with to round-trip values predictably.
- **Graceful with malformed input.** Invalid percent triplets remain unchanged rather than raising an exception.
- **Immutable result.** Returns a brand-new `XString` without altering the original value.

## Parameters

| Parameter | Type | Default | Description |
| --- | --- | --- | --- |
| `$raw` | `bool` | `false` | When `true`, use `rawurldecode()` (RFC 3986). Otherwise use `urldecode()` where `+` decodes to a space. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Decoded text as a new `XString` instance. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Decode a classic query string fragment

<!-- test:url-decode-basic -->
```php
use Orryv\XString;

$result = XString::new('hello+world%21')->urlDecode();

#Test: self::assertSame('hello world!', (string) $result);
```

### Raw mode preserves literal plus signs

<!-- test:url-decode-raw -->
```php
use Orryv\XString;

$value = XString::new('a+b%20c')->withMode('codepoints');
$result = $value->urlDecode(true);

#Test: self::assertSame('a+b c', (string) $result);
```

### UTF-8 sequences decode back to characters

<!-- test:url-decode-utf8 -->
```php
use Orryv\XString;

$result = XString::new('caf%C3%A9')->urlDecode();

#Test: self::assertSame('café', (string) $result);
```

### Invalid percent sequences are left untouched

<!-- test:url-decode-invalid -->
```php
use Orryv\XString;

$result = XString::new('price%ZZtag')->urlDecode();

#Test: self::assertSame('price%ZZtag', (string) $result);
```

### Empty strings stay empty

<!-- test:url-decode-empty -->
```php
use Orryv\XString;

$result = XString::new('')->urlDecode();

#Test: self::assertSame('', (string) $result);
```

### Original instance remains unchanged

<!-- test:url-decode-immutable -->
```php
use Orryv\XString;

$value = XString::new('immutable%3F');
$value->urlDecode();

#Test: self::assertSame('immutable%3F', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::urlDecode` | `public function urlDecode(bool $raw = false): self` — Decode percent-encoded strings, supporting both classic and raw URL decoding modes. |
