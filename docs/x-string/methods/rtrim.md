# XString::rtrim()

## Table of Contents
- [XString::rtrim()](#xstringrtrim)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Right-trim mixed whitespace](#right-trim-mixed-whitespace)
    - [Preserve trailing newline when disabled](#preserve-trailing-newline-when-disabled)
    - [Leading whitespace is untouched](#leading-whitespace-is-untouched)
    - [Immutability check](#immutability-check)
    - [Empty string remains empty](#empty-string-remains-empty)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function rtrim(bool $newline = true, bool $space = true, bool $tab = true): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Produces a new immutable `XString` with trailing whitespace removed. Like [`trim()`](./trim.md) and [`ltrim()`](./ltrim.md),
you can decide which whitespace categories (newlines, spaces, tabs) are stripped by toggling the corresponding boolean
flags. Only the right-hand side of the string is affected.

## Important notes and considerations

- **Immutability.** The original instance is never modified; a new `XString` is returned.
- **Configurable whitespace.** Toggle trimming of newline, space, and tab characters independently.
- **Mode & encoding preserved.** The new instance inherits the original mode/encoding configuration.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$newline` | `bool` | `true` | Whether to remove trailing newline characters (`\r`, `\n`). |
| `$space` | `bool` | `true` | Whether to remove trailing spaces. |
| `$tab` | `bool` | `true` | Whether to remove trailing tab characters. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` with the configured whitespace removed from the end. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Right-trim mixed whitespace

<!-- test:rtrim-basic -->
```php
use Orryv\XString;

$xstring = XString::new("  Hello World!\n\t");
$result = $xstring->rtrim();
#Test: self::assertSame('  Hello World!', (string) $result);
```

### Preserve trailing newline when disabled

<!-- test:rtrim-disable-newline -->
```php
use Orryv\XString;

$xstring = XString::new("Line ending\n");
$result = $xstring->rtrim(newline: false);
#Test: self::assertSame("Line ending\n", (string) $result);
```

### Leading whitespace is untouched

<!-- test:rtrim-leading-untouched -->
```php
use Orryv\XString;

$xstring = XString::new("  keep head\t   ");
$result = $xstring->rtrim();
#Test: self::assertSame('  keep head', (string) $result);
```

### Immutability check

<!-- test:rtrim-immutability -->
```php
use Orryv\XString;

$xstring = XString::new("trim me   ");
$trimmed = $xstring->rtrim();
#Test: self::assertSame('trim me   ', (string) $xstring);
#Test: self::assertSame('trim me', (string) $trimmed);
```

### Empty string remains empty

<!-- test:rtrim-empty -->
```php
use Orryv\XString;

$xstring = XString::new('');
$result = $xstring->rtrim();
#Test: self::assertSame('', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::rtrim` | `public function rtrim(bool $newline = true, bool $space = true, bool $tab = true): self` — Trim configurable whitespace from the end of the string. |
