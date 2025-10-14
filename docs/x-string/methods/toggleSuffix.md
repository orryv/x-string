# XString::toggleSuffix()

## Table of Contents
- [XString::toggleSuffix()](#xstringtogglesuffix)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Add a suffix when it is missing](#add-a-suffix-when-it-is-missing)
    - [Remove an existing suffix](#remove-an-existing-suffix)
    - [Toggle HTML tag suffixes](#toggle-html-tag-suffixes)
    - [Toggle newline suffixes](#toggle-newline-suffixes)
    - [Only the first matching suffix is removed](#only-the-first-matching-suffix-is-removed)
    - [Original instance remains unchanged](#original-instance-remains-unchanged)
    - [Reject empty suffixes](#reject-empty-suffixes)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toggleSuffix(Newline|HtmlTag|string $suffix): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Toggles the presence of a suffix. If the string already ends with the provided suffix it is removed; otherwise, the suffix is appended. Supports plain strings plus `HtmlTag` and `Newline` helpers for specialised matching.

## Important notes and considerations

- **Two-way operation.** Uses the same detection rules as `endsWith()`/`removeSuffix()` to decide whether to add or remove the suffix.
- **Helper-aware.** Works seamlessly with `HtmlTag` and `Newline` helpers, including canonical newline matching.
- **Immutable.** Returns a new `XString`; the original value is never modified.

## Parameters

| Name | Type | Description |
| --- | --- | --- |
| `$suffix` | `Newline\|HtmlTag\|string` | Suffix to toggle. Must not normalise to an empty string. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` with the suffix added or removed. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$suffix` normalises to an empty string. |

## Examples

### Add a suffix when it is missing

<!-- test:toggle-suffix-add -->
```php
use Orryv\XString;

$value = XString::new('document');
$result = $value->toggleSuffix('.pdf');

#Test: self::assertSame('document.pdf', (string) $result);
```

### Remove an existing suffix

<!-- test:toggle-suffix-remove -->
```php
use Orryv\XString;

$value = XString::new('document.pdf');
$result = $value->toggleSuffix('.pdf');

#Test: self::assertSame('document', (string) $result);
```

### Toggle HTML tag suffixes

<!-- test:toggle-suffix-htmltag -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;

$value = XString::new('<strong>alert</strong>');
$result = $value->toggleSuffix(HtmlTag::closeTag('strong'));

#Test: self::assertSame('<strong>alert', (string) $result);
```

### Toggle newline suffixes

<!-- test:toggle-suffix-newline -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$value = XString::new('Summary');
$result = $value->toggleSuffix(Newline::new("\r\n"));

#Test: self::assertSame("Summary\r\n", (string) $result);
```

### Only the first matching suffix is removed

<!-- test:toggle-suffix-single-removal -->
```php
use Orryv\XString;

$value = XString::new('value;;');
$result = $value->toggleSuffix(';');

#Test: self::assertSame('value;', (string) $result);
```

### Original instance remains unchanged

<!-- test:toggle-suffix-immutable -->
```php
use Orryv\XString;

$original = XString::new('draft');
$original->toggleSuffix('.md');

#Test: self::assertSame('draft', (string) $original);
```

### Reject empty suffixes

<!-- test:toggle-suffix-empty -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('data');

#Test: $this->expectException(InvalidArgumentException::class);
$value->toggleSuffix('');
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toggleSuffix` | `public function toggleSuffix(Newline\|HtmlTag\|string $suffix): self` — Toggle a suffix on or off without mutating the original instance. |
