# XString::collapseWhitespaceToSpace()

## Table of Contents
- [XString::collapseWhitespaceToSpace()](#xstringcollapsewhitespacetospace)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Convert mixed whitespace into single spaces](#convert-mixed-whitespace-into-single-spaces)
    - [Collapse long runs to a single space](#collapse-long-runs-to-a-single-space)
    - [Normalise Windows newlines](#normalise-windows-newlines)
    - [Empty strings remain unchanged](#empty-strings-remain-unchanged)
    - [Immutability check](#immutability-check)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function collapseWhitespaceToSpace(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Replaces every contiguous block of whitespace characters (spaces, tabs, newlines, and carriage returns) with a single regular
space. The method returns a new immutable `XString`, leaving the original value untouched, and is useful when you want to
compress arbitrary whitespace into readable single-space separators.

## Important notes and considerations

- **All whitespace is collapsed.** Tabs, carriage returns, and Unix/Windows newlines are treated the same and replaced with a
  space.
- **Runs become one character.** Any length of whitespace — including multiple different characters in a row — becomes exactly one
  space.
- **Immutable.** Returns a new instance; the original string is never modified.

## Parameters

This method does not take parameters.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` with every whitespace run replaced by a single space. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Convert mixed whitespace into single spaces

<!-- test:collapse-whitespace-to-space-basic -->
```php
use Orryv\XString;

$paragraph = XString::new("Line 1\tLine 2\nLine 3");
$result = $paragraph->collapseWhitespaceToSpace();
#Test: self::assertSame('Line 1 Line 2 Line 3', (string) $result);
#Test: self::assertSame("Line 1\tLine 2\nLine 3", (string) $paragraph);
```

### Collapse long runs to a single space

<!-- test:collapse-whitespace-to-space-runs -->
```php
use Orryv\XString;

$text = XString::new("Too     many\t\t\tspaces    here");
$result = $text->collapseWhitespaceToSpace();
#Test: self::assertSame('Too many spaces here', (string) $result);
```

### Normalise Windows newlines

<!-- test:collapse-whitespace-to-space-crlf -->
```php
use Orryv\XString;

$text = XString::new("One\r\nTwo\r\n\nThree");
$result = $text->collapseWhitespaceToSpace();
#Test: self::assertSame('One Two Three', (string) $result);
```

### Empty strings remain unchanged

<!-- test:collapse-whitespace-to-space-empty -->
```php
use Orryv\XString;

$text = XString::new('');
$result = $text->collapseWhitespaceToSpace();
#Test: self::assertSame('', (string) $result);
```

### Immutability check

<!-- test:collapse-whitespace-to-space-immutability -->
```php
use Orryv\XString;

$value = XString::new(" \t spaced \n ");
$collapsed = $value->collapseWhitespaceToSpace();
#Test: self::assertSame(" \t spaced \n ", (string) $value);
#Test: self::assertSame(' spaced ', (string) $collapsed);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::collapseWhitespaceToSpace` | `public function collapseWhitespaceToSpace(): self` — Replace every run of whitespace with a single regular space without mutating the original value. |
