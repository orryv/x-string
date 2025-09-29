# XString::toLowerCase()

## Table of Contents
- [XString::toLowerCase()](#xstringtolowercase)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Alias lowercases ASCII text](#alias-lowercases-ascii-text)
    - [Alias respects multibyte characters](#alias-respects-multibyte-characters)
    - [Alias keeps the original instance untouched](#alias-keeps-the-original-instance-untouched)
    - [Alias works for instances in codepoint mode](#alias-works-for-instances-in-codepoint-mode)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toLowerCase(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

`toLowerCase()` is a thin alias that forwards to [`toLower()`](./toLower.md). It produces a new immutable `XString` containing the
lowercase representation of the string while preserving the original mode and encoding.

## Important notes and considerations

- **Alias only.** All behaviour and caveats are inherited from `toLower()`.
- **Encoding aware.** Multibyte characters are handled via `mb_strtolower()` when available.
- **Immutable result.** The original instance stays untouched; a clone is returned.

## Parameters

This method does not accept parameters.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new immutable `XString` containing the lowercase transformation. |

## Thrown exceptions

This method does not throw additional exceptions beyond those documented for [`toLower()`](./toLower.md).

## Examples

### Alias lowercases ASCII text

<!-- test:to-lower-case-basic -->
```php
use Orryv\XString;

$xstring = XString::new('ALIAS');
$result = $xstring->toLowerCase();
#Test: self::assertSame('alias', (string) $result);
```

### Alias respects multibyte characters

<!-- test:to-lower-case-multibyte -->
```php
use Orryv\XString;

$xstring = XString::new('ĞİŞ');
$result = $xstring->toLowerCase();
#Test: self::assertSame('ğiş', (string) $result);
```

### Alias keeps the original instance untouched

<!-- test:to-lower-case-immutable -->
```php
use Orryv\XString;

$xstring = XString::new('UNCHANGED');
$lower = $xstring->toLowerCase();
#Test: self::assertSame('UNCHANGED', (string) $xstring);
#Test: self::assertSame('unchanged', (string) $lower);
```

### Alias works for instances in codepoint mode

<!-- test:to-lower-case-codepoint-mode -->
```php
use Orryv\XString;

$xstring = XString::new('MIXED')->withMode('codepoints');
$result = $xstring->toLowerCase();
#Test: self::assertSame('mixed', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toLowerCase` | `public function toLowerCase(): self` — Alias of `toLower()` returning an immutable lowercase clone. |
