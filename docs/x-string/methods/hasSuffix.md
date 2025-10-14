# XString::hasSuffix()

## Table of Contents
- [XString::hasSuffix()](#xstringhassuffix)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Detect a simple string suffix](#detect-a-simple-string-suffix)
    - [Return false when the suffix is absent](#return-false-when-the-suffix-is-absent)
    - [Accept multiple candidate suffixes](#accept-multiple-candidate-suffixes)
    - [Work with HTML tag helpers](#work-with-html-tag-helpers-1)
    - [Use regex patterns for complex suffixes](#use-regex-patterns-for-complex-suffixes)
    - [Recognise newline helpers](#recognise-newline-helpers-1)
    - [Original instance remains unchanged](#original-instance-remains-unchanged-2)
    - [Reject empty candidate lists](#reject-empty-candidate-lists-2)
    - [Reject nested candidate arrays](#reject-nested-candidate-arrays-2)
    - [Reject empty suffix fragments](#reject-empty-suffix-fragments)
    - [Invalid regex patterns bubble up as errors](#invalid-regex-patterns-bubble-up-as-errors-2)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function hasSuffix(Newline|HtmlTag|Regex|Stringable|string|array<Newline|HtmlTag|Regex|Stringable|string> $suffix): bool
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Checks whether the string ends with any of the provided suffix candidates. Supports strings, `HtmlTag` helpers, `Newline` values, `Regex` patterns, or arrays of those values. This is a convenience alias for `endsWith()`.

## Important notes and considerations

- **Array candidates act as OR.** The method returns `true` when any candidate matches.
- **Helper-aware.** `HtmlTag` and `Newline` helpers use the same specialised matching logic as `removeSuffix()`.
- **Immutable.** Returns a boolean result without modifying the underlying `XString` instance.

## Parameters

| Name | Type | Description |
| --- | --- | --- |
| `$suffix` | `Newline\|HtmlTag\|Regex\|Stringable\|string\|array<Newline\|HtmlTag\|Regex\|Stringable\|string>` | Suffix candidate(s) to check. Arrays act as OR lists and must not be empty or nested. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `bool` | ✓ | `true` when any candidate matches the end of the string; otherwise `false`. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$suffix` is empty, provided as an empty array, contains nested arrays, or a candidate normalises to an empty string. |
| `ValueError` | A `Regex` candidate is invalid. |

## Examples

### Detect a simple string suffix

<!-- test:has-suffix-string -->
```php
use Orryv\XString;

$value = XString::new('report.pdf');
$result = $value->hasSuffix('.pdf');

#Test: self::assertTrue($result);
#Test: self::assertSame('report.pdf', (string) $value);
```

### Return false when the suffix is absent

<!-- test:has-suffix-missing -->
```php
use Orryv\XString;

$value = XString::new('report.pdf');
$result = $value->hasSuffix('.zip');

#Test: self::assertFalse($result);
```

### Accept multiple candidate suffixes

<!-- test:has-suffix-array -->
```php
use Orryv\XString;

$value = XString::new('archive.tar.gz');
$result = $value->hasSuffix(['.zip', '.tar.gz']);

#Test: self::assertTrue($result);
```

### Work with HTML tag helpers

<!-- test:has-suffix-htmltag -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;

$value = XString::new('<strong>alert</strong>');
$result = $value->hasSuffix(HtmlTag::closeTag('strong'));

#Test: self::assertTrue($result);
```

### Use regex patterns for complex suffixes

<!-- test:has-suffix-regex -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$value = XString::new('invoice-2024');
$result = $value->hasSuffix(Regex::new('/\d{4}$/'));

#Test: self::assertTrue($result);
```

### Recognise newline helpers

<!-- test:has-suffix-newline -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$value = XString::new("Summary\n");
$result = $value->hasSuffix(Newline::new("\r\n"));

#Test: self::assertTrue($result);
```

### Original instance remains unchanged

<!-- test:has-suffix-immutable -->
```php
use Orryv\XString;

$original = XString::new('value');
$original->hasSuffix('ue');

#Test: self::assertSame('value', (string) $original);
```

### Reject empty candidate lists

<!-- test:has-suffix-empty-array -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('data');

#Test: $this->expectException(InvalidArgumentException::class);
$value->hasSuffix([]);
```

### Reject nested candidate arrays

<!-- test:has-suffix-nested-array -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('data');

#Test: $this->expectException(InvalidArgumentException::class);
$value->hasSuffix([['.txt']]);
```

### Reject empty suffix fragments

<!-- test:has-suffix-empty-fragment -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('data');

#Test: $this->expectException(InvalidArgumentException::class);
$value->hasSuffix('');
```

### Invalid regex patterns bubble up as errors

<!-- test:has-suffix-invalid-regex -->
```php
use Orryv\XString;
use Orryv\XString\Regex;
use ValueError;

$value = XString::new('value');

#Test: $this->expectException(ValueError::class);
$value->hasSuffix(Regex::new('/[a-z+/'));
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::hasSuffix` | `public function hasSuffix(Newline\|HtmlTag\|Regex\|Stringable\|string\|array<Newline\|HtmlTag\|Regex\|Stringable\|string> $suffix): bool` — Check whether the string ends with any candidate suffix without mutating the original value. |
