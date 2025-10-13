# XString::equals()

## Table of Contents
- [XString::equals()](#xstringequals)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Perform a case-sensitive comparison](#perform-a-case-sensitive-comparison)
    - [Opt-in to case-insensitive comparisons](#opt-in-to-case-insensitive-comparisons)
    - [Compare against multiple alternatives](#compare-against-multiple-alternatives)
    - [Match the full string with a regular expression](#match-the-full-string-with-a-regular-expression)
    - [Normalise newline representations](#normalise-newline-representations)
    - [Compare against generated HTML tags](#compare-against-generated-html-tags)
    - [Empty candidate lists are not allowed](#empty-candidate-lists-are-not-allowed)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function equals(HtmlTag|Newline|Regex|Stringable|string|array $string, bool $case_sensitive = true): bool
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✗ | Public |

## Description

Compares the string with one or more candidates. Accepts plain strings, `Stringable` values, [`HtmlTag`](../../html-tag/methods/new.md)
representations, [`Newline`](../../newline/methods/new.md) helpers and [`Regex`](../../x-string/methods/match.md#technical-details)
patterns (which must match the entire string).

When `$case_sensitive` is `false`, comparisons are performed using a lowercased representation respecting the instance's encoding.
Array inputs are treated as OR-lists — the first matching candidate returns `true`.

## Important notes and considerations

- **Regexes must match the full string.** Partial matches return `false`.
- **Whitespace helpers.** `Newline` helpers are normalised so `\r\n` and `\n` compare equal.
- **Immutable behaviour.** `equals()` never alters the original `XString` instance.

## Parameters

| Name | Type | Description |
| --- | --- | --- |
| `$string` | `HtmlTag\|Newline\|Regex\|Stringable\|string\|array` | Candidate or candidates to compare against. |
| `$case_sensitive` | `bool` | When `false`, comparisons are performed case-insensitively. |

## Returns

| Return Type | Description |
| --- | --- |
| `bool` | `true` when any candidate matches, `false` otherwise. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | The candidate list is empty, nested arrays are provided, or a candidate normalises to an empty string. |
| `ValueError` | A provided regular expression is invalid. |

## Examples

### Perform a case-sensitive comparison

<!-- test:equals-basic -->
```php
use Orryv\XString;

$status = XString::new('Ready');

#Test: self::assertTrue($status->equals('Ready'));
#Test: self::assertFalse($status->equals('ready'));
#Test: self::assertSame('Ready', (string) $status);
```

### Opt-in to case-insensitive comparisons

<!-- test:equals-case-insensitive -->
```php
use Orryv\XString;

$status = XString::new('Ready');

#Test: self::assertTrue($status->equals('ready', case_sensitive: false));
#Test: self::assertFalse($status->equals('waiting', case_sensitive: false));
```

### Compare against multiple alternatives

<!-- test:equals-array -->
```php
use Orryv\XString;

$env = XString::new('ENV=production');

#Test: self::assertTrue($env->equals(['env=prod', 'ENV=production']));
#Test: self::assertFalse($env->equals(['ENV=staging', 'ENV=testing']));
```

### Match the full string with a regular expression

<!-- test:equals-regex -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$invoice = XString::new('Invoice-12345');

#Test: self::assertTrue($invoice->equals(Regex::new('/^Invoice-\d+$/')));
#Test: self::assertFalse($invoice->equals(Regex::new('/Invoice/')));
```

### Normalise newline representations

<!-- test:equals-newline -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$newline = XString::new("\n");

#Test: self::assertTrue($newline->equals(Newline::new("\r\n")));
#Test: self::assertTrue($newline->equals(Newline::new("\n")));
```

### Compare against generated HTML tags

<!-- test:equals-html -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;

$html = XString::new('<p>Hello</p>');
$tag = HtmlTag::new('p')->withBody('Hello')->withEndTag(false);

#Test: self::assertTrue($html->equals($tag));
#Test: self::assertFalse($html->equals(HtmlTag::new('p')->withBody('Hello')));
```

### Empty candidate lists are not allowed

<!-- test:equals-empty-array -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('anything');

#Test: $this->expectException(InvalidArgumentException::class);
$value->equals([]);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::equals` | `public function equals(HtmlTag\|Newline\|Regex\|Stringable\|string\|array $string, bool $case_sensitive = true): bool` — Compare the string against one or more candidates, optionally case-insensitively, supporting HTML, newline helpers and whole-string regular expressions. |
