# XString::removePrefix()

## Table of Contents
- [XString::removePrefix()](#xstringremoveprefix)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Remove a simple string prefix](#remove-a-simple-string-prefix)
    - [Remove the first matching prefix from a list](#remove-the-first-matching-prefix-from-a-list)
    - [Remove HTML tag prefixes with helpers](#remove-html-tag-prefixes-with-helpers)
    - [Remove regex-based prefixes](#remove-regex-based-prefixes)
    - [Remove newline prefixes](#remove-newline-prefixes)
    - [Leave the string unchanged when no prefix matches](#leave-the-string-unchanged-when-no-prefix-matches)
    - [Original instance remains unchanged](#original-instance-remains-unchanged)
    - [Reject empty candidate lists](#reject-empty-candidate-lists)
    - [Reject nested candidate arrays](#reject-nested-candidate-arrays)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function removePrefix(Newline|HtmlTag|Regex|Stringable|string|array<Newline|HtmlTag|Regex|Stringable|string> $prefix): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Removes the specified prefix from the beginning of the string when present. Accepts the same rich search types used throughout
`XString`, including strings, `HtmlTag` helpers, `Newline` values, `Regex` patterns, or arrays of those values. Only the first
matching prefix is removed; if no match is found the string is returned unchanged.

## Important notes and considerations

- **Multiple candidate support.** Provide an array to remove the first matching prefix from several options.
- **Helper-aware.** `HtmlTag` and `Newline` inputs use the same matching logic as other methods, including canonical newline
  handling.
- **Immutable.** Returns a new `XString`; the original instance is never modified.

## Parameters

| Name | Type | Description |
| --- | --- | --- |
| `$prefix` | `Newline\|HtmlTag\|Regex\|Stringable\|string\|array<Newline\|HtmlTag\|Regex\|Stringable\|string>` | Prefix candidate(s) to remove. Arrays act as OR lists and must not be empty or nested. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` with the matched prefix removed, or the original value when no candidate matched. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$prefix` is empty, provided as an empty array, or contains nested arrays. |
| `ValueError` | The supplied `Regex` pattern is invalid. |

## Examples

### Remove a simple string prefix

<!-- test:remove-prefix-string -->
```php
use Orryv\XString;

$value = XString::new('prefix-value');
$result = $value->removePrefix('prefix-');

#Test: self::assertSame('value', (string) $result);
```

### Remove the first matching prefix from a list

<!-- test:remove-prefix-array -->
```php
use Orryv\XString;

$value = XString::new('https://example.com');
$result = $value->removePrefix(['http://', 'https://']);

#Test: self::assertSame('example.com', (string) $result);
```

### Remove HTML tag prefixes with helpers

<!-- test:remove-prefix-htmltag -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;

$value = XString::new('<p>Hello</p>');
$result = $value->removePrefix(HtmlTag::new('p'));

#Test: self::assertSame('Hello</p>', (string) $result);
```

### Remove regex-based prefixes

<!-- test:remove-prefix-regex -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$value = XString::new('123-Report');
$result = $value->removePrefix(Regex::new('/^\d+-/'));

#Test: self::assertSame('Report', (string) $result);
```

### Remove newline prefixes

<!-- test:remove-prefix-newline -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$value = XString::new("\r\nAgenda");
$result = $value->removePrefix(Newline::new("\r\n"));

#Test: self::assertSame('Agenda', (string) $result);
```

### Leave the string unchanged when no prefix matches

<!-- test:remove-prefix-no-match -->
```php
use Orryv\XString;

$value = XString::new('data');
$result = $value->removePrefix('prefix-');

#Test: self::assertSame('data', (string) $result);
```

### Original instance remains unchanged

<!-- test:remove-prefix-immutable -->
```php
use Orryv\XString;

$original = XString::new('token');
$original->removePrefix('pre');

#Test: self::assertSame('token', (string) $original);
```

### Reject empty candidate lists

<!-- test:remove-prefix-empty-array -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('content');

#Test: $this->expectException(InvalidArgumentException::class);
$value->removePrefix([]);
```

### Reject nested candidate arrays

<!-- test:remove-prefix-nested-array -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('content');

#Test: $this->expectException(InvalidArgumentException::class);
$value->removePrefix([['pre']]);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::removePrefix` | `public function removePrefix(Newline\|HtmlTag\|Regex\|Stringable\|string\|array<Newline\|HtmlTag\|Regex\|Stringable\|string> $prefix): self` — Remove the first matching prefix candidate without mutating the original value. |
