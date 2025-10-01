# XString::collapseWhitespace()

## Table of Contents
- [XString::collapseWhitespace()](#xstringcollapsewhitespace)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Collapse consecutive spaces](#collapse-consecutive-spaces)
    - [Normalize mixed spaces and tabs](#normalize-mixed-spaces-and-tabs)
    - [Collapse multiple blank lines](#collapse-multiple-blank-lines)
    - [Collapse mixed whitespace sequences](#collapse-mixed-whitespace-sequences)
    - [Prefer CRLF when newline styles mix](#prefer-crlf-when-newline-styles-mix)
    - [Disable collapsing for all whitespace](#disable-collapsing-for-all-whitespace)
    - [Empty strings remain unchanged](#empty-strings-remain-unchanged)
    - [Immutability check](#immutability-check)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function collapseWhitespace($space = true, $tab = true, $newline = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Reduces runs of selected whitespace characters to a single instance, returning a new immutable `XString`. By default spaces
and tabs are collapsed while newlines are preserved. Toggle the boolean flags to control which character classes are
normalized so that you can keep intentional blank lines while still tightening indentation or spacing.

## Important notes and considerations

- **Per-category collapsing.** Each flag controls collapsing for that specific character: spaces (`' '`), horizontal tabs
  (`"\t"`), and newlines (`"\r"`, `"\n"`, `"\r\n"`). Only consecutive occurrences of an enabled category are reduced.
- **Newline preservation by default.** Leave `$newline` as `false` to keep existing blank lines intact. Set it to `true` to
  coalesce multiple blank lines into a single newline while preferring Windows-style `"\r\n"` if any combined block contains it.
- **Dedicated helpers.** Use `collapseWhitespaceToSpace()`, `collapseWhitespaceToTab()`, or `collapseWhitespaceToNewline()` when
  you need to normalise all whitespace into a specific character in one step.
- **Immutable.** Returns a new instance; the original value is never modified.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$space` | `bool` | `true` | Collapse runs of regular space characters. |
| `$tab` | `bool` | `true` | Collapse runs of horizontal tab characters. |
| `$newline` | `bool` | `false` | Collapse repeated newline sequences. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` with the requested whitespace collapsing applied. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Collapse consecutive spaces

<!-- test:collapse-whitespace-spaces -->
```php
use Orryv\XString;

$text = XString::new('Multiple    spaces   here');
$result = $text->collapseWhitespace();
#Test: self::assertSame('Multiple spaces here', (string) $result);
#Test: self::assertSame('Multiple    spaces   here', (string) $text);
```

### Normalize mixed spaces and tabs

<!-- test:collapse-whitespace-tabs -->
```php
use Orryv\XString;

$text = XString::new("Tabs\t\t\tand  spaces");
$result = $text->collapseWhitespace(space: true, tab: true, newline: false);
#Test: self::assertSame("Tabs\tand spaces", (string) $result);
```

### Collapse multiple blank lines

<!-- test:collapse-whitespace-newlines -->
```php
use Orryv\XString;

$text = XString::new("Line 1\n\n\nLine 2\r\n\r\nLine 3");
$result = $text->collapseWhitespace(newline: true);
#Test: self::assertSame("Line 1\nLine 2\r\nLine 3", (string) $result);
```

### Collapse mixed whitespace sequences

<!-- test:collapse-whitespace-mixed -->
```php
use Orryv\XString;

$text = XString::new("\n\n\t\t  ");
$result = $text->collapseWhitespace(newline: true);
#Test: self::assertSame("\n\t ", (string) $result);
```

### Prefer CRLF when newline styles mix

<!-- test:collapse-whitespace-crlf -->
```php
use Orryv\XString;

$text = XString::new("\n\r\n");
$result = $text->collapseWhitespace(newline: true);
#Test: self::assertSame("\r\n", (string) $result);
```

### Disable collapsing for all whitespace

<!-- test:collapse-whitespace-disabled -->
```php
use Orryv\XString;

$text = XString::new("Keep\n\n  everything\t\t as-is");
$result = $text->collapseWhitespace(space: false, tab: false, newline: false);
#Test: self::assertSame("Keep\n\n  everything\t\t as-is", (string) $result);
```

### Empty strings remain unchanged

<!-- test:collapse-whitespace-empty -->
```php
use Orryv\XString;

$text = XString::new('');
$result = $text->collapseWhitespace();
#Test: self::assertSame('', (string) $result);
```

### Immutability check

<!-- test:collapse-whitespace-immutability -->
```php
use Orryv\XString;

$text = XString::new("Original\t\tvalue");
$collapsed = $text->collapseWhitespace();
#Test: self::assertSame("Original\t\tvalue", (string) $text);
#Test: self::assertSame("Original\tvalue", (string) $collapsed);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::collapseWhitespace` | `public function collapseWhitespace($space = true, $tab = true, $newline = false): self` — Collapse repeated whitespace (spaces, tabs, optional newlines) into single characters without mutating the original string. |
