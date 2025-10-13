# XString::isValidEncoding()

## Table of Contents
- [XString::isValidEncoding()](#xstringisvalidencoding)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Validate UTF-8 content](#validate-utf-8-content)
    - [Detect invalid ASCII input](#detect-invalid-ascii-input)
    - [Default argument checks the current encoding](#default-argument-checks-the-current-encoding)
    - [Empty encoding names are rejected](#empty-encoding-names-are-rejected)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function isValidEncoding(?string $encoding = null): bool
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✗ | Public |

## Description

Determines whether the current string is valid for the provided encoding. When `$encoding` is omitted, the check is performed
against the instance's stored encoding. Utilises `mb_check_encoding()` when available and falls back to `iconv()` or simple
pattern checks for UTF-8.

## Important notes and considerations

- **Strict validation.** Passing `null` uses the internal encoding, which is kept in sync by methods such as `toEncoding()` or
  `transliterate()`.
- **Graceful fallbacks.** If neither `mb_check_encoding()` nor `iconv()` is available, UTF-8 is validated with a unicode-aware
  regular expression and other encodings are assumed valid.
- **Validation only.** The string is never modified.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$encoding` | `null\|string` | `null` | Encoding to validate against. Defaults to the instance's stored encoding when `null`. |

## Returns

| Return Type | Description |
| --- | --- |
| `bool` | `true` when the string is valid for the requested encoding, `false` otherwise. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | `$encoding` is provided but empty after trimming. |

## Examples

### Validate UTF-8 content

<!-- test:is-valid-encoding-utf8 -->
```php
use Orryv\XString;

$value = XString::new('Café');

#Test: self::assertTrue($value->isValidEncoding('UTF-8'));
```

### Detect invalid ASCII input

<!-- test:is-valid-encoding-ascii -->
```php
use Orryv\XString;

$value = XString::new('naïve');

#Test: self::assertFalse($value->isValidEncoding('ASCII'));
```

### Default argument checks the current encoding

<!-- test:is-valid-encoding-default -->
```php
use Orryv\XString;

$value = XString::new('emoji 😊');

#Test: self::assertTrue($value->isValidEncoding());
```

### Empty encoding names are rejected

<!-- test:is-valid-encoding-empty -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('data');

#Test: $this->expectException(InvalidArgumentException::class);
$value->isValidEncoding('   ');
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::isValidEncoding` | `public function isValidEncoding(?string $encoding = null): bool` — Check if the string is valid for a specific encoding (defaulting to the instance encoding) using `mb_check_encoding()`/`iconv()` fallbacks. |
