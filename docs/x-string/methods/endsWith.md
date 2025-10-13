# XString::endsWith()

## Table of Contents
- [XString::endsWith()](#xstringendswith)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Check a plain-string suffix](#check-a-plain-string-suffix)
    - [Supply alternatives through arrays](#supply-alternatives-through-arrays)
    - [Enforce suffix rules with regular expressions](#enforce-suffix-rules-with-regular-expressions)
    - [Work with HTML fragments](#work-with-html-fragments)
    - [Handle cross-platform newline endings](#handle-cross-platform-newline-endings)
    - [Reject empty search values](#reject-empty-search-values)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function endsWith(HtmlTag|Newline|Regex|Stringable|string|array $search): bool
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✗ | Public |

## Description

Determines whether the string ends with any of the provided candidates. Accepts plain strings, `Stringable` inputs,
[`HtmlTag`](../../html-tag/methods/new.md) descriptors, [`Newline`](../../newline/methods/new.md) helpers and
[`Regex`](../../x-string/methods/match.md#technical-details) patterns.

Array inputs are treated as OR-lists: the method returns `true` as soon as one candidate matches. The original `XString`
instance remains unchanged.

## Important notes and considerations

- **Structured suffix checks.** Use `HtmlTag` and `Newline` helpers for HTML- and newline-aware suffix tests.
- **Regex validation.** Patterns are executed with `preg_match_all`; invalid expressions raise `ValueError`.
- **Array inputs are flat.** Nested arrays are not supported and result in an exception.

## Parameters

| Name | Type | Description |
| --- | --- | --- |
| `$search` | `HtmlTag\|Newline\|Regex\|Stringable\|string\|array` | One or more candidates to test at the end of the string. |

## Returns

| Return Type | Description |
| --- | --- |
| `bool` | `true` when the string ends with any candidate, `false` otherwise. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | The candidates list is empty, nested arrays are provided, or a search value normalises to an empty string. |
| `ValueError` | A provided regular expression is invalid. |

## Examples

### Check a plain-string suffix

<!-- test:ends-with-basic -->
```php
use Orryv\XString;

$file = XString::new('archive.tar.gz');

#Test: self::assertTrue($file->endsWith('.gz'));
#Test: self::assertFalse($file->endsWith('.tar'));
#Test: self::assertSame('archive.tar.gz', (string) $file);
```

### Supply alternatives through arrays

<!-- test:ends-with-array -->
```php
use Orryv\XString;

$report = XString::new('summary-final.txt');

#Test: self::assertTrue($report->endsWith(['.pdf', '.txt']));
#Test: self::assertFalse($report->endsWith(['.docx', '.xlsx']));
```

### Enforce suffix rules with regular expressions

<!-- test:ends-with-regex -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$invoice = XString::new('Invoice-2025.pdf');

#Test: self::assertTrue($invoice->endsWith(Regex::new('/\d+\.pdf$/')));
#Test: self::assertFalse($invoice->endsWith(Regex::new('/\.zip$/')));
```

### Work with HTML fragments

<!-- test:ends-with-html -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;

$markup = XString::new('<p>Hello</p>');

#Test: self::assertTrue($markup->endsWith(HtmlTag::closeTag('p')));
#Test: self::assertFalse($markup->endsWith(HtmlTag::closeTag('div')));
```

### Handle cross-platform newline endings

<!-- test:ends-with-newline -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$log = XString::new("Line 1\n");

#Test: self::assertTrue($log->endsWith(Newline::new("\r\n")));
#Test: self::assertTrue($log->endsWith(Newline::new("\n")));
```

### Reject empty search values

<!-- test:ends-with-empty -->
```php
use InvalidArgumentException;
use Orryv\XString;

$string = XString::new('content');

#Test: $this->expectException(InvalidArgumentException::class);
$string->endsWith('');
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::endsWith` | `public function endsWith(HtmlTag\|Newline\|Regex\|Stringable\|string\|array $search): bool` — Test whether the string terminates with any candidate, including structured HTML, newline helpers or regular expressions. |
