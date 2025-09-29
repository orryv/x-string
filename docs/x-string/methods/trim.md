# XString::trim()

## Table of Contents
- [XString::trim()](#xstringtrim)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Trim surrounding whitespace characters](#trim-surrounding-whitespace-characters)
    - [Disable newline trimming](#disable-newline-trimming)
    - [Disabling all trimming options returns original string](#disabling-all-trimming-options-returns-original-string)
    - [Trimming collapses whitespace-only strings to empty](#trimming-collapses-whitespace-only-strings-to-empty)
    - [Immutability check](#immutability-check)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function trim(bool $newline = true, bool $space = true, bool $tab = true): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Returns a new immutable `XString` instance with leading and trailing whitespace characters removed. The method lets you
control which categories of characters are stripped: newlines (`\r` and `\n`), regular spaces, and tab characters. By
changing the boolean flags you can tailor the trimming behaviour for different contexts such as preserving line breaks
while still removing indentation.

## Important notes and considerations

- **Immutability.** The original instance remains unchanged; a new instance is returned with the trimmed value.
- **Configurable whitespace.** Disable trimming of newlines, spaces or tabs individually by passing `false` for that
  parameter.
- **Mode & encoding preserved.** The resulting `XString` keeps the same mode and encoding settings as the source
  instance.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$newline` | `bool` | `true` | Whether to trim newline characters (`\r`, `\n`). |
| `$space` | `bool` | `true` | Whether to trim regular space characters. |
| `$tab` | `bool` | `true` | Whether to trim horizontal tab characters. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` with matching mode/encoding and the trimmed value. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Trim surrounding whitespace characters

<!-- test:trim-basic -->
```php
use Orryv\XString;

$xstring = XString::new("\t  Hello World!\r\n");
$result = $xstring->trim();
#Test: self::assertSame('Hello World!', (string) $result);
```

### Disable newline trimming

<!-- test:trim-disable-newline -->
```php
use Orryv\XString;

$xstring = XString::new("Line with trailing newline\n");
$result = $xstring->trim(newline: false);
#Test: self::assertSame("Line with trailing newline\n", (string) $result);
```

### Disabling all trimming options returns original string

<!-- test:trim-disabled -->
```php
use Orryv\XString;

$xstring = XString::new("  keep my spaces  ");
$result = $xstring->trim(newline: false, space: false, tab: false);
#Test: self::assertSame('  keep my spaces  ', (string) $result);
```

### Trimming collapses whitespace-only strings to empty

<!-- test:trim-whitespace-only -->
```php
use Orryv\XString;

$xstring = XString::new("\t\n  \r");
$result = $xstring->trim();
#Test: self::assertSame('', (string) $result);
```

### Immutability check

<!-- test:trim-immutability -->
```php
use Orryv\XString;

$xstring = XString::new("  Mutable?  ");
$trimmed = $xstring->trim();
#Test: self::assertSame('  Mutable?  ', (string) $xstring);
#Test: self::assertSame('Mutable?', (string) $trimmed);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::trim` | `public function trim(bool $newline = true, bool $space = true, bool $tab = true): self` — Trim leading and trailing whitespace with configurable newline/space/tab handling. |
