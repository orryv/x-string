# XString::removeSuffix()

## Table of Contents
- [XString::removeSuffix()](#xstringremovesuffix)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Remove a simple string suffix](#remove-a-simple-string-suffix)
    - [Remove the first matching suffix from a list](#remove-the-first-matching-suffix-from-a-list)
    - [Remove HTML tag suffixes with helpers](#remove-html-tag-suffixes-with-helpers)
    - [Remove regex-based suffixes](#remove-regex-based-suffixes)
    - [Remove newline suffixes](#remove-newline-suffixes)
    - [Leave the string unchanged when no suffix matches](#leave-the-string-unchanged-when-no-suffix-matches)
    - [Original instance remains unchanged](#original-instance-remains-unchanged)
    - [Reject empty candidate lists](#reject-empty-candidate-lists)
    - [Reject nested candidate arrays](#reject-nested-candidate-arrays)
    - [Reject empty suffix fragments](#reject-empty-suffix-fragments)
    - [Invalid regex patterns bubble up as errors](#invalid-regex-patterns-bubble-up-as-errors)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function removeSuffix(Newline|HtmlTag|Regex|Stringable|string|array<Newline|HtmlTag|Regex|Stringable|string> $suffix): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Removes the specified suffix from the end of the string when present. Accepts strings, `HtmlTag` helpers, `Newline` values, `Regex` patterns, or arrays of those values. Only the first matching candidate is removed; if nothing matches, the string is returned unchanged.

## Important notes and considerations

- **Multiple candidate support.** Provide an array of suffixes to remove the first one that matches.
- **Helper-aware.** `HtmlTag` and `Newline` inputs use the same specialised matching logic as other `XString` methods, including canonical newline detection.
- **Immutable.** Returns a new `XString`; the original instance is never modified.

## Parameters

| Name | Type | Description |
| --- | --- | --- |
| `$suffix` | `Newline\|HtmlTag\|Regex\|Stringable\|string\|array<Newline\|HtmlTag\|Regex\|Stringable\|string>` | Suffix candidate(s) to remove. Arrays act as OR lists and must not be empty or nested. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` with the matching suffix removed, or the original value when no candidate matched. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$suffix` is empty, provided as an empty array, contains nested arrays, or a delimiter normalises to an empty string. |
| `ValueError` | A `Regex` delimiter is invalid. |

## Examples

### Remove a simple string suffix

<!-- test:remove-suffix-string -->
```php
use Orryv\XString;

$value = XString::new('report.txt');
$result = $value->removeSuffix('.txt');

#Test: self::assertSame('report', (string) $result);
```

### Remove the first matching suffix from a list

<!-- test:remove-suffix-array -->
```php
use Orryv\XString;

$value = XString::new('archive.tar.gz');
$result = $value->removeSuffix(['.zip', '.tar.gz']);

#Test: self::assertSame('archive', (string) $result);
```

### Remove HTML tag suffixes with helpers

<!-- test:remove-suffix-htmltag -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;

$value = XString::new('<strong>alert</strong>');
$result = $value->removeSuffix(HtmlTag::closeTag('strong'));

#Test: self::assertSame('<strong>alert', (string) $result);
```

### Remove regex-based suffixes

<!-- test:remove-suffix-regex -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$value = XString::new('Invoice 2024');
$result = $value->removeSuffix(Regex::new('/\s\d+$/'));

#Test: self::assertSame('Invoice', (string) $result);
```

### Remove newline suffixes

<!-- test:remove-suffix-newline -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$value = XString::new("Summary\n");
$result = $value->removeSuffix(Newline::new("\r\n"));

#Test: self::assertSame('Summary', (string) $result);
```

### Leave the string unchanged when no suffix matches

<!-- test:remove-suffix-no-match -->
```php
use Orryv\XString;

$value = XString::new('document.pdf');
$result = $value->removeSuffix(['.zip', '.tar']);

#Test: self::assertSame('document.pdf', (string) $result);
```

### Original instance remains unchanged

<!-- test:remove-suffix-immutable -->
```php
use Orryv\XString;

$original = XString::new('notes.md');
$original->removeSuffix('.md');

#Test: self::assertSame('notes.md', (string) $original);
```

### Reject empty candidate lists

<!-- test:remove-suffix-empty-array -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('data.csv');

#Test: $this->expectException(InvalidArgumentException::class);
$value->removeSuffix([]);
```

### Reject nested candidate arrays

<!-- test:remove-suffix-nested-array -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('data.csv');

#Test: $this->expectException(InvalidArgumentException::class);
$value->removeSuffix([['.csv']]);
```

### Reject empty suffix fragments

<!-- test:remove-suffix-empty-fragment -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('data.csv');

#Test: $this->expectException(InvalidArgumentException::class);
$value->removeSuffix('');
```

### Invalid regex patterns bubble up as errors

<!-- test:remove-suffix-invalid-regex -->
```php
use Orryv\XString;
use Orryv\XString\Regex;
use ValueError;

$value = XString::new('log-01');

#Test: $this->expectException(ValueError::class);
$value->removeSuffix(Regex::new('/[a-z+/'));
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::removeSuffix` | `public function removeSuffix(Newline\|HtmlTag\|Regex\|Stringable\|string\|array<Newline\|HtmlTag\|Regex\|Stringable\|string> $suffix): self` — Remove the first matching suffix candidate without mutating the original value. |
