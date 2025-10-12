# XString::strip()

## Table of Contents
- [XString::strip()](#xstringstrip)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Remove a substring](#remove-a-substring)
    - [Strip multiple values at once](#strip-multiple-values-at-once)
    - [Limit removals](#limit-removals)
    - [Remove from the end first](#remove-from-the-end-first)
    - [Strip HTML tags via HtmlTag helper](#strip-html-tags-via-htmltag-helper)
    - [Zero limit keeps the original](#zero-limit-keeps-the-original)
    - [Reject empty search terms](#reject-empty-search-terms)
    - [Original instance remains unchanged](#original-instance-remains-unchanged)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function strip(Newline|HtmlTag|Regex|string|array<Newline|HtmlTag|Regex|string> $search, null|int $limit = null, $reversed = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Removes occurrences of one or more search values from the current string. It leverages `replace()` internally with an empty
replacement, honouring limits and reversed traversal just like `replace()`.

## Important notes and considerations

- **Supports advanced search types.** Accepts strings, arrays, `Newline`, and `HtmlTag` instances, matching `replace()`'s
  capabilities.
- **Limit handling.** Provide `$limit` to control how many removals occur. A value of `null` means “no limit”.
- **Reverse traversal.** Set `$reversed` to `true` to start removing matches from the end of the string.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$search` | `Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string>` | — | Value(s) to remove from the string. |
| `$limit` | `null\|int` | `null` | Maximum number of removals. `null` performs all removals. |
| `$reversed` | `bool` | `false` | When `true`, remove matches starting from the end. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` with the requested substrings removed. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$limit` is negative or `$search` contains empty strings. |

## Examples

### Remove a substring

<!-- test:strip-basic -->
```php
use Orryv\XString;

$value = XString::new('Hello World!');
$result = $value->strip('World');

#Test: self::assertSame('Hello !', (string) $result);
```

### Strip multiple values at once

<!-- test:strip-multiple -->
```php
use Orryv\XString;

$result = XString::new('lorem ipsum dolor')->strip(['lorem', 'dolor']);

#Test: self::assertSame(' ipsum ', (string) $result);
```

### Limit removals

<!-- test:strip-limit -->
```php
use Orryv\XString;

$value = XString::new('foo bar foo bar foo');
$result = $value->strip('foo', 2);

#Test: self::assertSame(' bar  bar foo', (string) $result);
```

### Remove from the end first

<!-- test:strip-reversed -->
```php
use Orryv\XString;

$value = XString::new('one two two two');
$result = $value->strip('two', 1, true);

#Test: self::assertSame('one two two ', (string) $result);
```

### Strip HTML tags via HtmlTag helper

<!-- test:strip-htmltag -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;

$value = XString::new('<strong>bold</strong> text');
$result = $value->strip([HtmlTag::new('strong'), HtmlTag::closeTag('strong')]);

#Test: self::assertSame('bold text', (string) $result);
```

### Strip using a Regex pattern

<!-- test:strip-regex -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$value = XString::new('IDs: #42, #100, #7');
$result = $value->strip(Regex::new('/#\d+/'));

#Test: self::assertSame('IDs: , , ', (string) $result);
```

### Zero limit keeps the original

<!-- test:strip-zero-limit -->
```php
use Orryv\XString;

$value = XString::new('keep me');
$result = $value->strip('keep', 0);

#Test: self::assertSame('keep me', (string) $result);
```

### Reject empty search terms

<!-- test:strip-empty-search -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('content');

#Test: $this->expectException(InvalidArgumentException::class);
$value->strip('');
```

### Original instance remains unchanged

<!-- test:strip-immutable -->
```php
use Orryv\XString;

$original = XString::new('remove me once');
$original->strip('once');

#Test: self::assertSame('remove me once', (string) $original);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::strip` | `public function strip(Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string> $search, null\|int $limit = null, $reversed = false): self` — Remove occurrences of one or more values without mutating the original instance. |
