# XString::togglePrefix()

## Table of Contents
- [XString::togglePrefix()](#xstringtoggleprefix)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Add a prefix when it is missing](#add-a-prefix-when-it-is-missing)
    - [Remove an existing prefix](#remove-an-existing-prefix)
    - [Toggle HTML tag prefixes](#toggle-html-tag-prefixes)
    - [Toggle newline prefixes](#toggle-newline-prefixes)
    - [Only the first matching prefix is removed](#only-the-first-matching-prefix-is-removed)
    - [Original instance remains unchanged](#original-instance-remains-unchanged)
    - [Reject empty prefixes](#reject-empty-prefixes)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function togglePrefix(Newline|HtmlTag|string $prefix): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Toggles the presence of a prefix. If the string already starts with the provided prefix it is removed; otherwise, the prefix is prepended. Supports plain strings as well as `HtmlTag` and `Newline` helpers for type-aware prefix handling.

## Important notes and considerations

- **Two-way operation.** Uses the same detection rules as `startsWith()`/`removePrefix()` when deciding whether to add or remove the prefix.
- **Helper-aware.** Works seamlessly with `HtmlTag` and `Newline` helpers.
- **Immutable.** Returns a new `XString` instance; the original remains unchanged.

## Parameters

| Name | Type | Description |
| --- | --- | --- |
| `$prefix` | `Newline\|HtmlTag\|string` | Prefix to toggle. Must not normalise to an empty string. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` with the prefix added or removed. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$prefix` normalises to an empty string. |

## Examples

### Add a prefix when it is missing

<!-- test:toggle-prefix-add -->
```php
use Orryv\XString;

$value = XString::new('example.com');
$result = $value->togglePrefix('https://');

#Test: self::assertSame('https://example.com', (string) $result);
```

### Remove an existing prefix

<!-- test:toggle-prefix-remove -->
```php
use Orryv\XString;

$value = XString::new('https://example.com');
$result = $value->togglePrefix('https://');

#Test: self::assertSame('example.com', (string) $result);
```

### Toggle HTML tag prefixes

<!-- test:toggle-prefix-htmltag -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;

$value = XString::new('<strong>alert');
$result = $value->togglePrefix(HtmlTag::new('strong'));

#Test: self::assertSame('alert', (string) $result);
```

### Toggle newline prefixes

<!-- test:toggle-prefix-newline -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$value = XString::new('Title');
$result = $value->togglePrefix(Newline::new("\n"));

#Test: self::assertSame("\nTitle", (string) $result);
```

### Only the first matching prefix is removed

<!-- test:toggle-prefix-single-removal -->
```php
use Orryv\XString;

$value = XString::new('##heading');
$result = $value->togglePrefix('#');

#Test: self::assertSame('#heading', (string) $result);
```

### Original instance remains unchanged

<!-- test:toggle-prefix-immutable -->
```php
use Orryv\XString;

$original = XString::new('subject');
$original->togglePrefix('> ');

#Test: self::assertSame('subject', (string) $original);
```

### Reject empty prefixes

<!-- test:toggle-prefix-empty -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('data');

#Test: $this->expectException(InvalidArgumentException::class);
$value->togglePrefix('');
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::togglePrefix` | `public function togglePrefix(Newline\|HtmlTag\|string $prefix): self` — Toggle a prefix on or off without mutating the original instance. |
