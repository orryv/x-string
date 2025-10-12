# XString::contains()

## Table of Contents
- [XString::contains()](#xstringcontains)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Search for a simple substring](#search-for-a-simple-substring)
    - [Provide alternatives via arrays](#provide-alternatives-via-arrays)
    - [Match using newline helpers](#match-using-newline-helpers)
    - [Leverage regular expressions](#leverage-regular-expressions)
    - [Locate HTML tags](#locate-html-tags)
    - [Reject empty search terms](#reject-empty-search-terms)
    - [Surface invalid regex patterns](#surface-invalid-regex-patterns)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function contains(HtmlTag|Newline|Regex|Stringable|string|array $search): bool
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✗ | Public |

## Description

Checks whether the string contains any of the supplied search terms. Strings, `Stringable` objects, [`Newline`](../../newline/methods/new.md)
helpers, [`HtmlTag`](../../html-tag/methods/new.md) instances and [`Regex`](../../x-string/methods/match.md#technical-details) patterns are all supported.
When an array is supplied it is treated as an OR-condition: the method returns `true` as soon as any member matches.

## Important notes and considerations

- **Multi-type aware.** Understands plain strings, newline helpers, HTML tags and regular expressions.
- **Array inputs are OR-based.** Nested arrays are not supported; supply a flat list of candidates instead.
- **Respects newline canonicalisation.** A Windows `\r\n` search will still match Unix `\n` line endings.
- **Non-mutating.** The original `XString` instance is never modified.

## Parameters

| Name | Type | Description |
| --- | --- | --- |
| `$search` | `HtmlTag\|Newline\|Regex\|Stringable\|string\|array` | Candidate or list of candidates to test. |

## Returns

| Return Type | Description |
| --- | --- |
| `bool` | `true` when any candidate is found, `false` otherwise. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | When the search list is empty, a nested array is provided, or a candidate normalises to an empty string. |
| `ValueError` | When a provided regex pattern is invalid. |

## Examples

### Search for a simple substring

<!-- test:contains-basic -->
```php
use Orryv\XString;

$message = XString::new('Status: processing order #42');

#Test: self::assertTrue($message->contains('order'));
#Test: self::assertFalse($message->contains('shipped'));
#Test: self::assertSame('Status: processing order #42', (string) $message);
```

### Provide alternatives via arrays

<!-- test:contains-array -->
```php
use Orryv\XString;

$body = XString::new('Choose blue or green.');

#Test: self::assertTrue($body->contains(['red', 'green']));
#Test: self::assertFalse($body->contains(['cyan', 'magenta']));
```

### Match using newline helpers

<!-- test:contains-newline -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$log = XString::new("First line\nSecond line");

#Test: self::assertTrue($log->contains(Newline::new("\r\n")));
#Test: self::assertTrue($log->contains(Newline::new("\n")));
```

### Leverage regular expressions

<!-- test:contains-regex -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$text = XString::new('Ticket #123 is open');

#Test: self::assertTrue($text->contains(Regex::new('/#\d+/')));
#Test: self::assertFalse($text->contains(Regex::new('/#99/')));
```

### Locate HTML tags

<!-- test:contains-html -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;

$html = XString::new('<article><section class="intro">Welcome</section></article>');

$section = HtmlTag::new('section')->withClass('intro');

#Test: self::assertTrue($html->contains($section));
#Test: self::assertFalse($html->contains(HtmlTag::new('aside')));
```

### Reject empty search terms

<!-- test:contains-empty -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('content');

#Test: $this->expectException(InvalidArgumentException::class);
$value->contains('');
```

### Surface invalid regex patterns

<!-- test:contains-invalid-regex -->
```php
use Orryv\XString;
use Orryv\XString\Regex;
use ValueError;

$value = XString::new('sample');

#Test: $this->expectException(ValueError::class);
$value->contains(Regex::new('/[unclosed/'));
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::contains` | `public function contains(HtmlTag\|Newline\|Regex\|Stringable\|string\|array $search): bool` — Check if any of the given search candidates occur within the string, supporting newline helpers, HTML tags, and regex patterns. |
