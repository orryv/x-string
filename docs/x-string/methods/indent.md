# XString::indent()

## Table of Contents
- [XString::indent()](#xstringindent)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Indent every line with spaces](#indent-every-line-with-spaces)
    - [Indent only the first line](#indent-only-the-first-line)
    - [Indent using tab characters](#indent-using-tab-characters)
    - [Leave text unchanged when no indentation is requested](#leave-text-unchanged-when-no-indentation-is-requested)
    - [Indenting an empty string is a no-op](#indenting-an-empty-string-is-a-no-op)
    - [Immutability check](#immutability-check)
    - [Invalid parameters throw an exception](#invalid-parameters-throw-an-exception)
    - [Indent the last lines with a negative limit](#indent-the-last-lines-with-a-negative-limit)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function indent(int $spaces = 2, int $tabs = 0, int $lines = 0): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Adds a prefix of spaces and/or tabs to the start of each line. When `$lines` is greater than zero the indentation is
applied to only the specified number of leading lines, while negative values count from the bottom. A value of `0`
processes every line. The method keeps the existing newline characters intact.

## Important notes and considerations

- **Immutability.** Returns a new `XString` instance without mutating the original.
- **Line-sensitive limit.** Use a positive `$lines` value to indent from the top, a negative value to indent starting from the
  bottom, and `0` (default) to process every line.
- **Whitespace composition.** Combine `$spaces` and `$tabs` to create mixed indentation sequences (spaces are added before tabs).

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$spaces` | `int` | `2` | Number of spaces to prepend to each selected line. Must be `>= 0`. |
| `$tabs` | `int` | `0` | Number of tab characters to prepend to each selected line. Must be `>= 0`. |
| `$lines` | `int` | `0` | Number of lines to indent. `0` means “all lines.” Positive values count from the top; negative values count from the end. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` containing the indented content using the same mode and encoding. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$spaces` or `$tabs` is negative. |

## Examples

### Indent every line with spaces

<!-- test:indent-basic -->
```php
use Orryv\XString;

$xstring = XString::new("first line\nsecond line");
$result = $xstring->indent(spaces: 4);
#Test: self::assertSame("    first line\n    second line", (string) $result);
```

### Indent only the first line

<!-- test:indent-line-limit -->
```php
use Orryv\XString;

$xstring = XString::new("alpha\nbeta\ngamma");
$result = $xstring->indent(spaces: 2, lines: 1);
#Test: self::assertSame("  alpha\nbeta\ngamma", (string) $result);
```

### Indent using tab characters

<!-- test:indent-tabs -->
```php
use Orryv\XString;

$xstring = XString::new("item 1\nitem 2");
$result = $xstring->indent(spaces: 0, tabs: 1);
#Test: self::assertSame("\titem 1\n\titem 2", (string) $result);
```

### Leave text unchanged when no indentation is requested

<!-- test:indent-noop -->
```php
use Orryv\XString;

$xstring = XString::new("left alone");
$result = $xstring->indent(spaces: 0, tabs: 0);
#Test: self::assertSame('left alone', (string) $result);
```

### Indenting an empty string is a no-op

<!-- test:indent-empty -->
```php
use Orryv\XString;

$xstring = XString::new('');
$result = $xstring->indent(spaces: 2);
#Test: self::assertSame('', (string) $result);
```

### Immutability check

<!-- test:indent-immutability -->
```php
use Orryv\XString;

$xstring = XString::new("line A\nline B");
$indented = $xstring->indent(spaces: 2, tabs: 1);
#Test: self::assertSame("line A\nline B", (string) $xstring);
#Test: self::assertSame("  \tline A\n  \tline B", (string) $indented);
```

### Invalid parameters throw an exception

<!-- test:indent-invalid-params -->
```php
use InvalidArgumentException;
use Orryv\XString;

$xstring = XString::new('oops');
#Test: $this->expectException(InvalidArgumentException::class);
$xstring->indent(spaces: -1);
```

### Indent the last lines with a negative limit

<!-- test:indent-negative-lines -->
```php
use Orryv\XString;

$xstring = XString::new("first\nsecond\nthird\nfourth");
$result = $xstring->indent(spaces: 2, lines: -2);
#Test: self::assertSame("first\nsecond\n  third\n  fourth", (string) $result);
```

## One-line API table entry

| Method | Version | Signature & Description |
| --- | --- | --- |
| `indent` | 1.0 | `public function indent(int $spaces = 2, int $tabs = 0, int $lines = 0): self` — Prefix the first `$lines` (or all) with the requested indentation. |
