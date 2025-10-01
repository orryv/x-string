# XString::outdent()

## Table of Contents
- [XString::outdent()](#xstringoutdent)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Remove leading spaces from each line](#remove-leading-spaces-from-each-line)
    - [Outdent a mix of spaces and tabs](#outdent-a-mix-of-spaces-and-tabs)
    - [Limit the number of processed lines](#limit-the-number-of-processed-lines)
    - [Lines without indentation remain unchanged](#lines-without-indentation-remain-unchanged)
    - [Immutability check](#immutability-check-1)
    - [Invalid parameters throw an exception](#invalid-parameters-throw-an-exception-1)
    - [Outdent the last lines with a negative limit](#outdent-the-last-lines-with-a-negative-limit)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function outdent(int $spaces = 2, int $tabs = 0, int $lines = 0): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Removes up to the specified number of leading spaces and/or tabs from the start of each selected line. The `$lines`
argument lets you restrict how many lines are adjusted from the top (positive values) or bottom (negative values).
Excess removal requests beyond the available
whitespace are ignored gracefully.

## Important notes and considerations

- **Immutability.** Returns a fresh `XString`; the original is untouched.
- **Selective processing.** Positive `$lines` values process from the top, negative values count backwards from the end, and `0`
  (default) touches every line.
- **Partial removal.** The method removes spaces and tabs independently and stops once a non-whitespace character is reached.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$spaces` | `int` | `2` | Maximum number of leading spaces to remove per line. Must be `>= 0`. |
| `$tabs` | `int` | `0` | Maximum number of leading tab characters to remove per line. Must be `>= 0`. |
| `$lines` | `int` | `0` | Number of lines to process. `0` means “all lines.” Positive values process from the top; negative values process from the end. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` where the requested indentation has been removed. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$spaces` or `$tabs` is negative. |

## Examples

### Remove leading spaces from each line

<!-- test:outdent-basic -->
```php
use Orryv\XString;

$xstring = XString::new("    apple\n    banana");
$result = $xstring->outdent(spaces: 4);
#Test: self::assertSame("apple\nbanana", (string) $result);
```

### Outdent a mix of spaces and tabs

<!-- test:outdent-mixed -->
```php
use Orryv\XString;

$xstring = XString::new("  \tsection\n  \tcontent");
$result = $xstring->outdent(spaces: 2, tabs: 1);
#Test: self::assertSame("section\ncontent", (string) $result);
```

### Limit the number of processed lines

<!-- test:outdent-limit -->
```php
use Orryv\XString;

$xstring = XString::new("    first\n    second\n    third");
$result = $xstring->outdent(spaces: 4, lines: 2);
#Test: self::assertSame("first\nsecond\n    third", (string) $result);
```

### Lines without indentation remain unchanged

<!-- test:outdent-no-change -->
```php
use Orryv\XString;

$xstring = XString::new("no indent\nalready flush");
$result = $xstring->outdent(spaces: 2);
#Test: self::assertSame("no indent\nalready flush", (string) $result);
```

### Immutability check

<!-- test:outdent-immutability -->
```php
use Orryv\XString;

$xstring = XString::new("    keep\n    change");
$outdented = $xstring->outdent(spaces: 2);
#Test: self::assertSame("    keep\n    change", (string) $xstring);
#Test: self::assertSame("  keep\n  change", (string) $outdented);
```

### Invalid parameters throw an exception

<!-- test:outdent-invalid -->
```php
use InvalidArgumentException;
use Orryv\XString;

$xstring = XString::new('fail');
#Test: $this->expectException(InvalidArgumentException::class);
$xstring->outdent(tabs: -1);
```

### Outdent the last lines with a negative limit

<!-- test:outdent-negative-lines -->
```php
use Orryv\XString;

$xstring = XString::new("    keep\n    trim\n    trim more");
$result = $xstring->outdent(spaces: 4, lines: -2);
#Test: self::assertSame("    keep\ntrim\ntrim more", (string) $result);
```

## One-line API table entry

| Method | Version | Signature & Description |
| --- | --- | --- |
| `outdent` | 1.0 | `public function outdent(int $spaces = 2, int $tabs = 0, int $lines = 0): self` — Remove a configurable amount of leading indentation from the first `$lines` (or all) lines. |
