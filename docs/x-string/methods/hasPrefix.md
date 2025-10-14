# XString::hasPrefix()

## Table of Contents
- [XString::hasPrefix()](#xstringhasprefix)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Detect a simple string prefix](#detect-a-simple-string-prefix)
    - [Return false when the prefix is absent](#return-false-when-the-prefix-is-absent)
    - [Accept multiple candidate prefixes](#accept-multiple-candidate-prefixes)
    - [Work with HTML tag helpers](#work-with-html-tag-helpers)
    - [Use regex patterns for complex prefixes](#use-regex-patterns-for-complex-prefixes)
    - [Recognise newline helpers](#recognise-newline-helpers)
    - [Original instance remains unchanged](#original-instance-remains-unchanged-1)
    - [Reject empty candidate lists](#reject-empty-candidate-lists-1)
    - [Reject nested candidate arrays](#reject-nested-candidate-arrays-1)
    - [Reject empty prefix fragments](#reject-empty-prefix-fragments)
    - [Invalid regex patterns bubble up as errors](#invalid-regex-patterns-bubble-up-as-errors-1)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function hasPrefix(Newline|HtmlTag|Regex|Stringable|string|array<Newline|HtmlTag|Regex|Stringable|string> $prefix): bool
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Checks whether the string starts with any of the provided prefix candidates. Supports strings, `HtmlTag` helpers, `Newline` values, `Regex` patterns, or arrays of those values. This is a convenience alias for `startsWith()`.

## Important notes and considerations

- **Array candidates act as OR.** The method returns `true` when any candidate matches.
- **Helper-aware.** `HtmlTag` and `Newline` helpers use the same specialised matching logic as other affixing methods.
- **Immutable.** Returns a boolean result without modifying the underlying `XString` instance.

## Parameters

| Name | Type | Description |
| --- | --- | --- |
| `$prefix` | `Newline\|HtmlTag\|Regex\|Stringable\|string\|array<Newline\|HtmlTag\|Regex\|Stringable\|string>` | Prefix candidate(s) to check. Arrays act as OR lists and must not be empty or nested. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `bool` | ✓ | `true` when any candidate matches the beginning of the string; otherwise `false`. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$prefix` is empty, provided as an empty array, contains nested arrays, or a candidate normalises to an empty string. |
| `ValueError` | A `Regex` candidate is invalid. |

## Examples

### Detect a simple string prefix

<!-- test:has-prefix-string -->
```php
use Orryv\XString;

$value = XString::new('prefix-value');
$result = $value->hasPrefix('prefix-');

#Test: self::assertTrue($result);
#Test: self::assertSame('prefix-value', (string) $value);
```

### Return false when the prefix is absent

<!-- test:has-prefix-missing -->
```php
use Orryv\XString;

$value = XString::new('data');
$result = $value->hasPrefix('prefix-');

#Test: self::assertFalse($result);
```

### Accept multiple candidate prefixes

<!-- test:has-prefix-array -->
```php
use Orryv\XString;

$value = XString::new('https://example.com');
$result = $value->hasPrefix(['ftp://', 'https://']);

#Test: self::assertTrue($result);
```

### Work with HTML tag helpers

<!-- test:has-prefix-htmltag -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;

$value = XString::new('<strong>alert');
$result = $value->hasPrefix(HtmlTag::new('strong'));

#Test: self::assertTrue($result);
```

### Use regex patterns for complex prefixes

<!-- test:has-prefix-regex -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$value = XString::new('#12345');
$result = $value->hasPrefix(Regex::new('/^#[0-9]+/'));

#Test: self::assertTrue($result);
```

### Recognise newline helpers

<!-- test:has-prefix-newline -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$value = XString::new("\nSummary");
$result = $value->hasPrefix(Newline::new("\r\n"));

#Test: self::assertTrue($result);
```

### Original instance remains unchanged

<!-- test:has-prefix-immutable -->
```php
use Orryv\XString;

$original = XString::new('value');
$original->hasPrefix('val');

#Test: self::assertSame('value', (string) $original);
```

### Reject empty candidate lists

<!-- test:has-prefix-empty-array -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('data');

#Test: $this->expectException(InvalidArgumentException::class);
$value->hasPrefix([]);
```

### Reject nested candidate arrays

<!-- test:has-prefix-nested-array -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('data');

#Test: $this->expectException(InvalidArgumentException::class);
$value->hasPrefix([['val']]);
```

### Reject empty prefix fragments

<!-- test:has-prefix-empty-fragment -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('data');

#Test: $this->expectException(InvalidArgumentException::class);
$value->hasPrefix('');
```

### Invalid regex patterns bubble up as errors

<!-- test:has-prefix-invalid-regex -->
```php
use Orryv\XString;
use Orryv\XString\Regex;
use ValueError;

$value = XString::new('value');

#Test: $this->expectException(ValueError::class);
$value->hasPrefix(Regex::new('/[a-z+/'));
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::hasPrefix` | `public function hasPrefix(Newline\|HtmlTag\|Regex\|Stringable\|string\|array<Newline\|HtmlTag\|Regex\|Stringable\|string> $prefix): bool` — Check whether the string starts with any candidate prefix without mutating the original value. |
