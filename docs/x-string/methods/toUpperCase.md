# XString::toUpperCase()

## Table of Contents
- [XString::toUpperCase()](#xstringtouppercase)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Alias uppercases ASCII text](#alias-uppercases-ascii-text)
    - [Alias respects multibyte characters](#alias-respects-multibyte-characters)
    - [Alias keeps the original instance untouched](#alias-keeps-the-original-instance-untouched)
    - [Alias works for instances in byte mode](#alias-works-for-instances-in-byte-mode)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toUpperCase(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

`toUpperCase()` is a convenience alias that delegates straight to [`toUpper()`](./toUpper.md). It returns a brand-new immutable
`XString` whose contents are converted to uppercase while preserving the original encoding and logical mode.

## Important notes and considerations

- **Alias only.** All behaviour (including encoding handling and error conditions) is inherited from `toUpper()`.
- **Encoding aware.** Multibyte characters are uppercased through `mb_strtoupper()` when available.
- **Immutable result.** The original instance is never modified; a clone with uppercase contents is returned.

## Parameters

This method does not accept parameters.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new immutable `XString` containing the uppercase transformation. |

## Thrown exceptions

This method does not throw additional exceptions beyond those documented for [`toUpper()`](./toUpper.md).

## Examples

### Alias uppercases ASCII text

<!-- test:to-upper-case-basic -->
```php
use Orryv\XString;

$xstring = XString::new('alias');
$result = $xstring->toUpperCase();
#Test: self::assertSame('ALIAS', (string) $result);
```

### Alias respects multibyte characters

<!-- test:to-upper-case-multibyte -->
```php
use Orryv\XString;

$xstring = XString::new('żółć');
$result = $xstring->toUpperCase();
#Test: self::assertSame('ŻÓŁĆ', (string) $result);
```

### Alias keeps the original instance untouched

<!-- test:to-upper-case-immutable -->
```php
use Orryv\XString;

$xstring = XString::new('stay lowercase');
$upper = $xstring->toUpperCase();
#Test: self::assertSame('stay lowercase', (string) $xstring);
#Test: self::assertSame('STAY LOWERCASE', (string) $upper);
```

### Alias works for instances in byte mode

<!-- test:to-upper-case-byte-mode -->
```php
use Orryv\XString;

$xstring = XString::new('mode')->withMode('bytes');
$result = $xstring->toUpperCase();
#Test: self::assertSame('MODE', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toUpperCase` | `public function toUpperCase(): self` — Alias of `toUpper()` returning an immutable uppercase clone. |
