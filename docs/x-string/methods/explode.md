# XString::explode()

## Table of Contents
- [XString::explode()](#xstringexplode)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Split by comma delimiter](#split-by-comma-delimiter)
    - [Limit total segments](#limit-total-segments)
    - [Provide multiple delimiters](#provide-multiple-delimiters)
    - [Use a regular expression delimiter](#use-a-regular-expression-delimiter)
    - [Use an HTML tag delimiter](#use-an-html-tag-delimiter)
    - [Split using Newline objects](#split-using-newline-objects)
    - [Empty source produces an empty array](#empty-source-produces-an-empty-array)
    - [Works with alternate modes](#works-with-alternate-modes)
    - [Reject invalid limits](#reject-invalid-limits)
    - [Reject empty delimiters](#reject-empty-delimiters)
    - [Disallow regexes that can match the empty string](#disallow-regexes-that-can-match-the-empty-string)
    - [Surface invalid regex patterns](#surface-invalid-regex-patterns)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function explode(Newline|HtmlTag|Regex|Stringable|string|array $delimiter, ?int $limit = null): array
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✗ | Public |

## Description

Splits the current string into an ordered list of fragments using one or more delimiters. Delimiters can be plain strings,
[`Newline`](../../newline/methods/new.md) instances (with cross-platform newline normalisation), [`Regex`](../../regex/methods/new.md)
patterns, [`HtmlTag`](../../html-tag/methods/new.md) descriptors, or arrays combining any of these. The method returns a native PHP
array and never mutates the source `XString` instance.

## Important notes and considerations

- **Array delimiters.** When you provide an array, the method treats it as an OR-list and splits on whichever delimiter appears next.
- **Regex safeguards.** Regular-expression delimiters must not be able to match the empty string. Invalid regex patterns bubble up as `ValueError`.
- **Newline helpers.** `Newline` objects are canonicalised so that requesting `"\r\n"` delimiters on a string containing `"\n"` still works.
- **Trailing delimiters.** When the string ends with a delimiter, an empty string is included as the final segment (mirrors `explode()`).

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$delimiter` | `Newline\|HtmlTag\|Regex\|Stringable\|string\|array` | — | One or more delimiters. Arrays are treated as logical OR. |
| `$limit` | `?int` | `null` | Optional maximum number of segments. The final element contains the remainder once the limit is reached. |

## Returns

| Return Type | Description |
| --- | --- |
| `list<string>` | Ordered list of fragments obtained after removing the delimiters. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | `$limit` is lower than 1, any delimiter normalises to an empty string, or a regex delimiter matches the empty string. |
| `ValueError` | A provided `Regex` delimiter is syntactically invalid. |

## Examples

### Split by comma delimiter

<!-- test:explode-basic -->
```php
use Orryv\XString;

$xstring = XString::new('alpha,beta,gamma');
$parts = $xstring->explode(',');

#Test: self::assertSame(['alpha', 'beta', 'gamma'], $parts);
#Test: self::assertSame('alpha,beta,gamma', (string) $xstring);
```

### Limit total segments

<!-- test:explode-limit -->
```php
use Orryv\XString;

$date = XString::new('2024-01-01-UTC');
$parts = $date->explode('-', limit: 3);

#Test: self::assertSame(['2024', '01', '01-UTC'], $parts);
```

### Provide multiple delimiters

<!-- test:explode-multiple-delimiters -->
```php
use Orryv\XString;

$tokens = XString::new('one|two/three');
$parts = $tokens->explode(['|', '/']);

#Test: self::assertSame(['one', 'two', 'three'], $parts);
```

### Use a regular expression delimiter

<!-- test:explode-regex -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$slug = XString::new('foo--bar--baz');
$parts = $slug->explode(Regex::new('/--/'));

#Test: self::assertSame(['foo', 'bar', 'baz'], $parts);
```

### Use an HTML tag delimiter

<!-- test:explode-html-tag -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;

$html = XString::new('Hello<br>World<br />!');
$parts = $html->explode(HtmlTag::new('br', true));

#Test: self::assertSame(['Hello', 'World', '!'], $parts);
```

### Split using Newline objects

<!-- test:explode-newline -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$log = XString::new("first\nsecond\nthird");
$parts = $log->explode(Newline::new("\r\n"));

#Test: self::assertSame(['first', 'second', 'third'], $parts);
```

### Empty source produces an empty array

<!-- test:explode-empty -->
```php
use Orryv\XString;

$result = XString::new('')->explode(',');

#Test: self::assertSame([], $result);
```

### Works with alternate modes

<!-- test:explode-mode -->
```php
use Orryv\XString;

$xstring = XString::new('Å-ß-ç')->withMode('bytes');
$parts = $xstring->explode('-');

#Test: self::assertSame(['Å', 'ß', 'ç'], $parts);
```

### Reject invalid limits

<!-- test:explode-invalid-limit -->
```php
use InvalidArgumentException;
use Orryv\XString;

$xstring = XString::new('one,two');

#Test: $this->expectException(InvalidArgumentException::class);
$xstring->explode(',', limit: 0);
```

### Reject empty delimiters

<!-- test:explode-empty-delimiter -->
```php
use InvalidArgumentException;
use Orryv\XString;

$xstring = XString::new('one two');

#Test: $this->expectException(InvalidArgumentException::class);
$xstring->explode('');
```

### Disallow regexes that can match the empty string

<!-- test:explode-empty-regex -->
```php
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\Regex;

$xstring = XString::new('value');

#Test: $this->expectException(InvalidArgumentException::class);
$xstring->explode(Regex::new('/\s*/'));
```

### Surface invalid regex patterns

<!-- test:explode-invalid-regex -->
```php
use Orryv\XString;
use Orryv\XString\Regex;
use ValueError;

$xstring = XString::new('value');

#Test: $this->expectException(ValueError::class);
$xstring->explode(Regex::new('/[unbalanced/'));
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::explode` | `public function explode(Newline\|HtmlTag\|Regex\|Stringable\|string\|array $delimiter, ?int $limit = null): array` — Split the string into fragments using string, newline, regex, or HTML-tag delimiters without mutating the original instance. |
