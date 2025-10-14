# XString::split()

## Table of Contents
- [XString::split()](#xstringsplit)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Split a string by a single delimiter](#split-a-string-by-a-single-delimiter)
    - [Limit the number of splits](#limit-the-number-of-splits)
    - [Use multiple delimiter candidates](#use-multiple-delimiter-candidates)
    - [Split with a regex delimiter](#split-with-a-regex-delimiter)
    - [Split using newline helpers](#split-using-newline-helpers)
    - [Split using HTML tag helpers](#split-using-html-tag-helpers)
    - [Mode changes do not affect the result](#mode-changes-do-not-affect-the-result)
    - [Empty strings return an empty list](#empty-strings-return-an-empty-list)
    - [Reject invalid limits](#reject-invalid-limits)
    - [Reject empty delimiter lists](#reject-empty-delimiter-lists)
    - [Reject empty delimiter fragments](#reject-empty-delimiter-fragments)
    - [Reject regex delimiters that match empty strings](#reject-regex-delimiters-that-match-empty-strings)
    - [Invalid regex patterns bubble up as errors](#invalid-regex-patterns-bubble-up-as-errors-3)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function split(Newline|HtmlTag|Regex|Stringable|string|array<Newline|HtmlTag|Regex|Stringable|string> $delimiter, ?int $limit = null): array
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Splits the string into an array of substrings using the provided delimiter(s). Supports strings, `HtmlTag` helpers, `Newline` values, `Regex` patterns, or arrays of those values. When `$limit` is provided, at most `$limit - 1` splits are performed and the remainder is returned as the final element.

## Important notes and considerations

- **Multiple delimiter support.** Provide an array to split on the earliest matching delimiter each time.
- **Helper-aware.** `HtmlTag`, `Newline`, and `Regex` inputs use the same specialised matching logic as other `XString` methods.
- **Immutable.** Returns a plain array while leaving the original `XString` untouched.

## Parameters

| Name | Type | Description |
| --- | --- | --- |
| `$delimiter` | `Newline\|HtmlTag\|Regex\|Stringable\|string\|array<Newline\|HtmlTag\|Regex\|Stringable\|string>` | Delimiter candidate(s). Arrays act as OR lists and must not be empty or nested. |
| `$limit` | `?int` | Optional maximum number of pieces to return. Must be `null` or an integer ≥ 1. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `array<int, string>` | ✓ | List of substrings after splitting on the delimiter(s). |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$limit` is provided and < 1, `$delimiter` is an empty array, a candidate normalises to an empty string, or a regex delimiter matches an empty string. |
| `ValueError` | A `Regex` delimiter is invalid. |

## Examples

### Split a string by a single delimiter

<!-- test:split-basic-string -->
```php
use Orryv\XString;

$value = XString::new('first,second,third');
$result = $value->split(',');

#Test: self::assertSame(['first', 'second', 'third'], $result);
```

### Limit the number of splits

<!-- test:split-limit -->
```php
use Orryv\XString;

$value = XString::new('a,b,c,d');
$result = $value->split(',', 3);

#Test: self::assertSame(['a', 'b', 'c,d'], $result);
```

### Use multiple delimiter candidates

<!-- test:split-multiple-delimiters -->
```php
use Orryv\XString;

$value = XString::new('one,two;three');
$result = $value->split([',', ';']);

#Test: self::assertSame(['one', 'two', 'three'], $result);
```

### Split with a regex delimiter

<!-- test:split-regex -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$value = XString::new("word1  word2\tword3");
$result = $value->split(Regex::new('/\s+/'));

#Test: self::assertSame(['word1', 'word2', 'word3'], $result);
```

### Split using newline helpers

<!-- test:split-newline -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$value = XString::new("first\nsecond");
$result = $value->split(Newline::new("\r\n"));

#Test: self::assertSame(['first', 'second'], $result);
```

### Split using HTML tag helpers

<!-- test:split-htmltag -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;

$value = XString::new('first<br>second<br>third');
$result = $value->split(HtmlTag::new('br', true));

#Test: self::assertSame(['first', 'second', 'third'], $result);
```

### Mode changes do not affect the result

<!-- test:split-mode -->
```php
use Orryv\XString;

$value = XString::new('ä|ö|ü')->withMode('bytes');
$result = $value->split('|');

#Test: self::assertSame(['ä', 'ö', 'ü'], $result);
```

### Empty strings return an empty list

<!-- test:split-empty-string -->
```php
use Orryv\XString;

$value = XString::new('');
$result = $value->split(',');

#Test: self::assertSame([], $result);
```

### Reject invalid limits

<!-- test:split-invalid-limit -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('a,b');

#Test: $this->expectException(InvalidArgumentException::class);
$value->split(',', 0);
```

### Reject empty delimiter lists

<!-- test:split-empty-array -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('a,b');

#Test: $this->expectException(InvalidArgumentException::class);
$value->split([]);
```

### Reject empty delimiter fragments

<!-- test:split-empty-fragment -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('a,b');

#Test: $this->expectException(InvalidArgumentException::class);
$value->split('');
```

### Reject regex delimiters that match empty strings

<!-- test:split-regex-empty-match -->
```php
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\Regex;

$value = XString::new('abc');

#Test: $this->expectException(InvalidArgumentException::class);
$value->split(Regex::new('/a*/'));
```

### Invalid regex patterns bubble up as errors

<!-- test:split-invalid-regex -->
```php
use Orryv\XString;
use Orryv\XString\Regex;
use ValueError;

$value = XString::new('abc');

#Test: $this->expectException(ValueError::class);
$value->split(Regex::new('/[a-z+/'));
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::split` | `public function split(Newline\|HtmlTag\|Regex\|Stringable\|string\|array<Newline\|HtmlTag\|Regex\|Stringable\|string> $delimiter, ?int $limit = null): array` — Split the string into parts using strings, helpers, or regex delimiters without mutating the original value. |
