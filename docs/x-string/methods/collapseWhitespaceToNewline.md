# XString::collapseWhitespaceToNewline()

## Table of Contents
- [XString::collapseWhitespaceToNewline()](#xstringcollapsewhitespacetonewline)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Convert whitespace to newline separators](#convert-whitespace-to-newline-separators)
    - [Collapse multiple blank lines](#collapse-multiple-blank-lines)
    - [Normalise carriage-return line endings](#normalise-carriage-return-line-endings)
    - [Empty strings stay empty](#empty-strings-stay-empty)
    - [Immutability check](#immutability-check)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function collapseWhitespaceToNewline(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Replaces each run of whitespace characters with a single newline (`"\n"`). This is useful when you need to normalise user input
into newline-delimited records regardless of the original spacing or line-ending style.

## Important notes and considerations

- **Uniform newline output.** Every whitespace run becomes exactly one `"\n"`, even if the input contained Windows `"\r\n"`
  sequences.
- **Runs collapse to one newline.** Consecutive or mixed whitespace characters result in a single newline between segments.
- **Immutable.** The source instance is never modified.

## Parameters

This method does not take parameters.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` where whitespace runs are replaced with single newline characters. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Convert whitespace to newline separators

<!-- test:collapse-whitespace-to-newline-basic -->
```php
use Orryv\XString;

$values = XString::new("first second\tthird");
$result = $values->collapseWhitespaceToNewline();
#Test: self::assertSame("first\nsecond\nthird", (string) $result);
```

### Collapse multiple blank lines

<!-- test:collapse-whitespace-to-newline-multiple -->
```php
use Orryv\XString;

$text = XString::new("line1\n\n\nline2");
$result = $text->collapseWhitespaceToNewline();
#Test: self::assertSame("line1\nline2", (string) $result);
```

### Normalise carriage-return line endings

<!-- test:collapse-whitespace-to-newline-crlf -->
```php
use Orryv\XString;

$text = XString::new("a\r\nb\r\n\rc");
$result = $text->collapseWhitespaceToNewline();
#Test: self::assertSame("a\nb\nc", (string) $result);
#Test: self::assertSame("a\r\nb\r\n\rc", (string) $text);
```

### Empty strings stay empty

<!-- test:collapse-whitespace-to-newline-empty -->
```php
use Orryv\XString;

$text = XString::new('');
$result = $text->collapseWhitespaceToNewline();
#Test: self::assertSame('', (string) $result);
```

### Immutability check

<!-- test:collapse-whitespace-to-newline-immutability -->
```php
use Orryv\XString;

$value = XString::new(" head \t tail ");
$collapsed = $value->collapseWhitespaceToNewline();
#Test: self::assertSame(" head \t tail ", (string) $value);
#Test: self::assertSame("\nhead\ntail\n", (string) $collapsed);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::collapseWhitespaceToNewline` | `public function collapseWhitespaceToNewline(): self` — Replace every whitespace run with a single newline while leaving the original string untouched. |
