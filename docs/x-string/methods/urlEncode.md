# XString::urlEncode()

## Table of Contents
- [XString::urlEncode()](#xstringurlencode)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Encode spaces using application/x-www-form-urlencoded](#encode-spaces-using-applicationx-www-form-urlencoded)
    - [Raw mode keeps plus signs](#raw-mode-keeps-plus-signs)
    - [UTF-8 characters are percent-encoded](#utf-8-characters-are-percent-encoded)
    - [Safe characters stay untouched in grapheme mode](#safe-characters-stay-untouched-in-grapheme-mode)
    - [Empty strings stay empty](#empty-strings-stay-empty-1)
    - [Original instance remains unchanged](#original-instance-remains-unchanged-1)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function urlEncode(bool $raw = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Percent-encodes the string for safe transport inside URLs. By default it mimics `urlencode()` (suitable for query strings), converting spaces to `+`. Opt into RFC 3986 raw encoding by passing `$raw = true` to use `rawurlencode()` semantics.

## Important notes and considerations

- **Two encoding modes.** Default behaviour matches `application/x-www-form-urlencoded`, while raw mode preserves literal `+` and encodes spaces as `%20`.
- **Limited safe characters.** Only alphanumerics plus `-`, `_`, and `.` stay untouched in classic mode—characters like `~` are percent-encoded.
- **Encoding preservation.** The resulting `XString` keeps the same encoding metadata as the original instance.
- **Immutability.** Always returns a new `XString`; the source instance is untouched.

## Parameters

| Parameter | Type | Default | Description |
| --- | --- | --- | --- |
| `$raw` | `bool` | `false` | When `true`, use `rawurlencode()` (RFC 3986). Otherwise use `urlencode()` (query-string safe). |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | URL-encoded representation of the original string. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Encode spaces using application/x-www-form-urlencoded

<!-- test:url-encode-basic -->
```php
use Orryv\XString;

$result = XString::new('hello world')->urlEncode();

#Test: self::assertSame('hello+world', (string) $result);
```

### Raw mode keeps plus signs

<!-- test:url-encode-raw -->
```php
use Orryv\XString;

$result = XString::new('a+b c')->urlEncode(true);

#Test: self::assertSame('a%2Bb%20c', (string) $result);
```

### UTF-8 characters are percent-encoded

<!-- test:url-encode-utf8 -->
```php
use Orryv\XString;

$result = XString::new('café')->urlEncode();

#Test: self::assertSame('caf%C3%A9', (string) $result);
```

### Safe characters stay untouched in grapheme mode

<!-- test:url-encode-grapheme-mode -->
```php
use Orryv\XString;

$value = XString::new('alpha-123_.-')->withMode('graphemes');
$result = $value->urlEncode();

#Test: self::assertSame('alpha-123_.-', (string) $result);
```

### Empty strings stay empty

<!-- test:url-encode-empty -->
```php
use Orryv\XString;

$result = XString::new('')->urlEncode();

#Test: self::assertSame('', (string) $result);
```

### Original instance remains unchanged

<!-- test:url-encode-immutable -->
```php
use Orryv\XString;

$value = XString::new('keep me safe');
$value->urlEncode();

#Test: self::assertSame('keep me safe', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::urlEncode` | `public function urlEncode(bool $raw = false): self` — Percent-encode the string for URLs using classic (`urlencode`) or raw (`rawurlencode`) semantics. |
