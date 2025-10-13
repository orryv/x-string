# XString::countOccurrences()

## Table of Contents
- [XString::countOccurrences()](#xstringcountoccurrences)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Count repeated plain substrings](#count-repeated-plain-substrings)
    - [Add counts from multiple candidates](#add-counts-from-multiple-candidates)
    - [Use regular expressions to count patterns](#use-regular-expressions-to-count-patterns)
    - [Inspect HTML fragments](#inspect-html-fragments)
    - [Normalise newline styles before counting](#normalise-newline-styles-before-counting)
    - [Return zero when nothing matches](#return-zero-when-nothing-matches)
    - [Reject empty search values](#reject-empty-search-values)
    - [Disallow regexes that can match an empty string](#disallow-regexes-that-can-match-an-empty-string)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function countOccurrences(HtmlTag|Newline|Regex|Stringable|string|array $search): int
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✗ | Public |

## Description

Counts how many times the given candidates occur in the string. Supports plain strings, `Stringable` inputs,
[`HtmlTag`](../../html-tag/methods/new.md) descriptors, [`Newline`](../../newline/methods/new.md) helpers and
[`Regex`](../../x-string/methods/match.md#technical-details) patterns.

When an array of candidates is provided the counts are added together. The method never mutates the underlying string.

## Important notes and considerations

- **Structured counting.** HTML tags and newline helpers are counted using dedicated matchers.
- **Regex safety.** Patterns that can match an empty string are rejected to avoid unbounded counts.
- **Array inputs are flat.** Nested arrays are not supported and raise an exception.

## Parameters

| Name | Type | Description |
| --- | --- | --- |
| `$search` | `HtmlTag\|Newline\|Regex\|Stringable\|string\|array` | One or more candidates whose occurrences should be counted. |

## Returns

| Return Type | Description |
| --- | --- |
| `int` | The total number of matches across all candidates. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | The candidate list is empty, nested arrays are provided, a candidate normalises to an empty string, or a regex can match an empty string. |
| `ValueError` | A provided regular expression is invalid. |

## Examples

### Count repeated plain substrings

<!-- test:count-occurrences-basic -->
```php
use Orryv\XString;

$text = XString::new('banana bread banana');

#Test: self::assertSame(2, $text->countOccurrences('banana'));
#Test: self::assertSame('banana bread banana', (string) $text);
```

### Add counts from multiple candidates

<!-- test:count-occurrences-array -->
```php
use Orryv\XString;

$palette = XString::new('Colors: red, blue, red, green');

#Test: self::assertSame(3, $palette->countOccurrences(['red', 'blue']));
#Test: self::assertSame(0, $palette->countOccurrences(['yellow']));
```

### Use regular expressions to count patterns

<!-- test:count-occurrences-regex -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$list = XString::new('IDs: A-10, B-20, C-30');

#Test: self::assertSame(3, $list->countOccurrences(Regex::new('/[A-Z]-\d+/')));
```

### Inspect HTML fragments

<!-- test:count-occurrences-html -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;

$html = XString::new('<ul><li>One</li><li>Two</li><li>Three</li></ul>');

#Test: self::assertSame(3, $html->countOccurrences(HtmlTag::new('li')));
#Test: self::assertSame(0, $html->countOccurrences(HtmlTag::new('section')));
```

### Normalise newline styles before counting

<!-- test:count-occurrences-newline -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$lines = XString::new("Line 1\nLine 2\n");

#Test: self::assertSame(2, $lines->countOccurrences(Newline::new("\r\n")));
#Test: self::assertSame(2, $lines->countOccurrences(Newline::new("\n")));
```

### Return zero when nothing matches

<!-- test:count-occurrences-none -->
```php
use Orryv\XString;

$text = XString::new('abc');

#Test: self::assertSame(0, $text->countOccurrences('z'));
```

### Reject empty search values

<!-- test:count-occurrences-empty -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('content');

#Test: $this->expectException(InvalidArgumentException::class);
$value->countOccurrences('');
```

### Disallow regexes that can match an empty string

<!-- test:count-occurrences-empty-regex -->
```php
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\Regex;

$value = XString::new('anything');

#Test: $this->expectException(InvalidArgumentException::class);
$value->countOccurrences(Regex::new('/^/m'));
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::countOccurrences` | `public function countOccurrences(HtmlTag\|Newline\|Regex\|Stringable\|string\|array $search): int` — Count how often the provided candidates occur in the string, supporting structured inputs and regular expressions while guarding against empty matches. |
