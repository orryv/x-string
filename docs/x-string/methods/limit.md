# XString::limit()

## Table of Contents
- [XString::limit()](#xstringlimit)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Truncate text with the default suffix](#truncate-text-with-the-default-suffix)
    - [Values shorter than the limit remain untouched](#values-shorter-than-the-limit-remain-untouched)
    - [Respect grapheme clusters while truncating](#respect-grapheme-clusters-while-truncating)
    - [Zero-length limits return only the suffix](#zero-length-limits-return-only-the-suffix)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function limit(int $length, HtmlTag|Newline|Stringable|string $append_string = '...'): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Returns a new immutable `XString` limited to the specified number of logical units (bytes, codepoints, or graphemes depending on the current mode). When truncation happens, the provided suffix is appended to the shortened value.

## Important notes and considerations

- **Mode-aware length.** The method honours the active mode set via [`withMode()`](./withMode.md), so grapheme clusters stay intact when working with user-facing text.
- **Suffix only on truncation.** The `$append_string` is concatenated only if the original value exceeds the requested `$length`.
- **Zero-length behaviour.** A limit of `0` returns just the suffix (or an empty string if the suffix is empty) whenever the value needs truncation.

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$length` | — | `int` | Maximum number of logical units to keep. Must be greater than or equal to `0`. |
| `$append_string` | `'...'` | `HtmlTag\|Newline\|Stringable\|string` | Suffix appended when truncation occurs. Accepts any stringable value. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` truncated to the requested limit, optionally suffixed when truncation occurs. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$length` is negative or `$append_string` cannot be converted to a string. |

## Examples

### Truncate text with the default suffix

<!-- test:limit-default-suffix -->
```php
use Orryv\XString;

$result = XString::new('Hello World')->limit(5);
#Test: self::assertSame('Hello...', (string) $result);
```

### Values shorter than the limit remain untouched

<!-- test:limit-without-truncation -->
```php
use Orryv\XString;

$result = XString::new('Hi')->limit(5);
#Test: self::assertSame('Hi', (string) $result);
```

### Respect grapheme clusters while truncating

<!-- test:limit-grapheme-aware -->
```php
use Orryv\XString;

$result = XString::new('👩‍💻 coding')->limit(2, '…');
#Test: self::assertSame('👩‍💻 …', (string) $result);
```

### Zero-length limits return only the suffix

<!-- test:limit-zero-length -->
```php
use Orryv\XString;

$result = XString::new('Content')->limit(0, '[more]');
#Test: self::assertSame('[more]', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::limit` | `public function limit(int $length, HtmlTag\|Newline\|Stringable\|string $append_string = '...'): self` — Limit the value to a maximum length and append the suffix only when truncation occurs. |
