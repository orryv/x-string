# XString::toUpper()

## Table of Contents
- [XString::toUpper()](#xstringtoupper)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Convert ASCII text to uppercase](#convert-ascii-text-to-uppercase)
    - [Uppercasing multibyte characters](#uppercasing-multibyte-characters)
    - [Alias `toUpperCase()` delegates to `toUpper()`](#alias-touppercase-delegates-to-toupper)
    - [Original instance remains unchanged](#original-instance-remains-unchanged)
    - [Uppercasing an empty string](#uppercasing-an-empty-string)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toUpper(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Returns a new immutable `XString` whose contents are converted to uppercase. The conversion respects the instance's current
encoding and mode, using `mb_strtoupper()` when available to correctly handle multibyte characters and falling back to
`strtoupper()` otherwise. The alias [`toUpperCase()`](./to-upper-case.md) simply calls this method.

## Important notes and considerations

- **Immutability.** A new `XString` is returned; the original value remains untouched.
- **Encoding aware.** The stored encoding is passed to `mb_strtoupper()` ensuring consistent results for multibyte strings.
- **Mode preserved.** The resulting instance keeps the same mode and encoding as the original.

## Parameters

This method does not accept parameters.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new immutable `XString` containing the uppercase transformation. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Convert ASCII text to uppercase

<!-- test:to-upper-basic -->
```php
use Orryv\XString;

$xstring = XString::new('hello world');
$upper = $xstring->toUpper();
#Test: self::assertSame('HELLO WORLD', (string) $upper);
```

### Uppercasing multibyte characters

<!-- test:to-upper-multibyte -->
```php
use Orryv\XString;

$xstring = XString::new('Résumé');
$upper = $xstring->toUpper();
#Test: self::assertSame('RÉSUMÉ', (string) $upper);
```

### Alias `toUpperCase()` delegates to `toUpper()`

<!-- test:to-upper-alias -->
```php
use Orryv\XString;

$xstring = XString::new('alias');
#Test: self::assertSame((string) $xstring->toUpper(), (string) $xstring->toUpperCase());
```

### Original instance remains unchanged

<!-- test:to-upper-immutability -->
```php
use Orryv\XString;

$xstring = XString::new('keep me');
$upper = $xstring->toUpper();
#Test: self::assertSame('keep me', (string) $xstring);
#Test: self::assertSame('KEEP ME', (string) $upper);
```

### Uppercasing an empty string

<!-- test:to-upper-empty -->
```php
use Orryv\XString;

$xstring = XString::new('');
$upper = $xstring->toUpper();
#Test: self::assertSame('', (string) $upper);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toUpper` | `public function toUpper(): self` — Return a new immutable instance with the contents converted to uppercase. |
