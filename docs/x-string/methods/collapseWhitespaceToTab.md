# XString::collapseWhitespaceToTab()

## Table of Contents
- [XString::collapseWhitespaceToTab()](#xstringcollapsewhitespacetotab)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Convert words to single tab separators](#convert-words-to-single-tab-separators)
    - [Collapse mixed whitespace to tabs](#collapse-mixed-whitespace-to-tabs)
    - [Keep empty strings unchanged](#keep-empty-strings-unchanged)
    - [Immutability check](#immutability-check)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function collapseWhitespaceToTab(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Replaces every run of whitespace characters with a single tab character (`"\t"`). This is convenient when normalising data into
column-like tab-separated formats regardless of how the source whitespace was arranged.

## Important notes and considerations

- **All whitespace becomes tabs.** Spaces, carriage returns, and newlines are all collapsed into a single tab.
- **Consecutive runs collapse once.** No matter how many whitespace characters appear in sequence, the result is exactly one tab.
- **Immutable.** The original string is never modified.

## Parameters

This method does not take parameters.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` whose whitespace runs are replaced by single tabs. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Convert words to single tab separators

<!-- test:collapse-whitespace-to-tab-basic -->
```php
use Orryv\XString;

$list = XString::new("apple  banana\ncherry");
$result = $list->collapseWhitespaceToTab();
#Test: self::assertSame("apple\tbanana\tcherry", (string) $result);
```

### Collapse mixed whitespace to tabs

<!-- test:collapse-whitespace-to-tab-mixed -->
```php
use Orryv\XString;

$text = XString::new("value1\r\n  value2\t\tvalue3");
$result = $text->collapseWhitespaceToTab();
#Test: self::assertSame("value1\tvalue2\tvalue3", (string) $result);
#Test: self::assertSame("value1\r\n  value2\t\tvalue3", (string) $text);
```

### Keep empty strings unchanged

<!-- test:collapse-whitespace-to-tab-empty -->
```php
use Orryv\XString;

$empty = XString::new('');
$result = $empty->collapseWhitespaceToTab();
#Test: self::assertSame('', (string) $result);
```

### Immutability check

<!-- test:collapse-whitespace-to-tab-immutability -->
```php
use Orryv\XString;

$value = XString::new(" a \n b ");
$collapsed = $value->collapseWhitespaceToTab();
#Test: self::assertSame(" a \n b ", (string) $value);
#Test: self::assertSame("\ta\tb\t", (string) $collapsed);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::collapseWhitespaceToTab` | `public function collapseWhitespaceToTab(): self` — Collapse every whitespace run into a single tab without mutating the source string. |
