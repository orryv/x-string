# XString::ensureSuffix()

## Table of Contents
- [XString::ensureSuffix()](#xstringensuresuffix)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Append a missing suffix](#append-a-missing-suffix)
    - [Do not duplicate existing suffixes](#do-not-duplicate-existing-suffixes)
    - [Append HTML closing tags with helpers](#append-html-closing-tags-with-helpers)
    - [Ensure newline suffixes](#ensure-newline-suffixes)
    - [Reject empty suffixes](#reject-empty-suffixes)
    - [Original instance remains unchanged](#original-instance-remains-unchanged-2)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function ensureSuffix(Newline|HtmlTag|string $suffix): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Ensures that the string ends with the specified suffix. If the suffix is already present the string is returned unchanged;
otherwise the suffix is appended. Works with plain strings, `HtmlTag` instances (useful for ensuring closing tags), and
`Newline` helpers for line endings.

## Important notes and considerations

- **Type-aware checks.** Uses the same logic as `endsWith()` so helper objects like `HtmlTag` and `Newline` behave consistently.
- **Immutable.** Returns a new `XString`; the original remains untouched.

## Parameters

| Name | Type | Description |
| --- | --- | --- |
| `$suffix` | `Newline\|HtmlTag\|string` | The suffix that should terminate the string. Must not be empty. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` with the suffix appended if it was missing. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$suffix` normalises to an empty string. |

## Examples

### Append a missing suffix

<!-- test:ensure-suffix-add -->
```php
use Orryv\XString;

$value = XString::new('report');
$result = $value->ensureSuffix('.pdf');

#Test: self::assertSame('report.pdf', (string) $result);
```

### Do not duplicate existing suffixes

<!-- test:ensure-suffix-existing -->
```php
use Orryv\XString;

$value = XString::new('report.pdf');
$result = $value->ensureSuffix('.pdf');

#Test: self::assertSame('report.pdf', (string) $result);
```

### Append HTML closing tags with helpers

<!-- test:ensure-suffix-htmltag -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;

$value = XString::new('<strong>important');
$result = $value->ensureSuffix(HtmlTag::closeTag('strong'));

#Test: self::assertSame('<strong>important</strong>', (string) $result);
```

### Ensure newline suffixes

<!-- test:ensure-suffix-newline -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$value = XString::new("Line");
$result = $value->ensureSuffix(Newline::new("\r\n"));

#Test: self::assertSame("Line\r\n", (string) $result);
```

### Reject empty suffixes

<!-- test:ensure-suffix-empty -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('data');

#Test: $this->expectException(InvalidArgumentException::class);
$value->ensureSuffix('');
```

### Original instance remains unchanged

<!-- test:ensure-suffix-immutable -->
```php
use Orryv\XString;

$original = XString::new('value');
$original->ensureSuffix(';');

#Test: self::assertSame('value', (string) $original);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::ensureSuffix` | `public function ensureSuffix(Newline\|HtmlTag\|string $suffix): self` — Guarantee that a string ends with the specified suffix without mutating the original instance. |
