# XString::ltrim()

## Table of Contents
- [XString::ltrim()](#xstringltrim)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Left-trim mixed whitespace](#left-trim-mixed-whitespace)
    - [Preserve leading newline when disabled](#preserve-leading-newline-when-disabled)
    - [Trailing whitespace is untouched](#trailing-whitespace-is-untouched)
    - [Immutability check](#immutability-check)
    - [No-op when all options disabled](#no-op-when-all-options-disabled)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function ltrim(bool $newline = true, bool $space = true, bool $tab = true): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Creates a new immutable `XString` with leading whitespace removed. Similar to [`trim()`](./trim.md), you control which
categories are stripped by toggling the boolean flags for newlines, spaces, and tabs. Only the left-hand side of the
string is affected; trailing characters remain unchanged.

## Important notes and considerations

- **Immutability.** A new `XString` instance is returned, the original remains untouched.
- **Configurable whitespace.** Disable trimming of newline, space, or tab characters individually.
- **Mode & encoding preserved.** The resulting instance keeps the original mode and encoding.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$newline` | `bool` | `true` | Whether to remove leading newline characters (`\r`, `\n`). |
| `$space` | `bool` | `true` | Whether to remove leading spaces. |
| `$tab` | `bool` | `true` | Whether to remove leading tab characters. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` with the configured whitespace removed from the start. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Left-trim mixed whitespace

<!-- test:ltrim-basic -->
```php
use Orryv\XString;

$xstring = XString::new("\n\t  Hello World!  ");
$result = $xstring->ltrim();
#Test: self::assertSame('Hello World!  ', (string) $result);
```

### Preserve leading newline when disabled

<!-- test:ltrim-disable-newline -->
```php
use Orryv\XString;

$xstring = XString::new("\nTabbed line");
$result = $xstring->ltrim(newline: false);
#Test: self::assertSame("\nTabbed line", (string) $result);
```

### Trailing whitespace is untouched

<!-- test:ltrim-trailing-untouched -->
```php
use Orryv\XString;

$xstring = XString::new("  keep tail   \t");
$result = $xstring->ltrim();
#Test: self::assertSame("keep tail   \t", (string) $result);
```

### Immutability check

<!-- test:ltrim-immutability -->
```php
use Orryv\XString;

$xstring = XString::new("   example   ");
$trimmed = $xstring->ltrim();
#Test: self::assertSame('   example   ', (string) $xstring);
#Test: self::assertSame('example   ', (string) $trimmed);
```

### No-op when all options disabled

<!-- test:ltrim-disabled -->
```php
use Orryv\XString;

$xstring = XString::new("\t preserve me");
$result = $xstring->ltrim(newline: false, space: false, tab: false);
#Test: self::assertSame("\t preserve me", (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::ltrim` | `public function ltrim(bool $newline = true, bool $space = true, bool $tab = true): self` — Trim configurable whitespace from the start of the string. |
