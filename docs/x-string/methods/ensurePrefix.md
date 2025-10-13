# XString::ensurePrefix()

## Table of Contents
- [XString::ensurePrefix()](#xstringensureprefix)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Add a missing string prefix](#add-a-missing-string-prefix)
    - [Do not duplicate existing prefixes](#do-not-duplicate-existing-prefixes)
    - [Add HTML tag prefixes with helpers](#add-html-tag-prefixes-with-helpers)
    - [Ensure newline prefixes](#ensure-newline-prefixes)
    - [Reject empty prefixes](#reject-empty-prefixes)
    - [Original instance remains unchanged](#original-instance-remains-unchanged-1)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function ensurePrefix(Newline|HtmlTag|string $prefix): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Guarantees that the string begins with the provided prefix. If the prefix is already present nothing changes; otherwise, the
prefix is prepended. Works with plain strings, `HtmlTag` helper instances, and `Newline` values so you can consistently enforce
structural markers such as protocols, markup wrappers, or line breaks.

## Important notes and considerations

- **Type-aware prefix detection.** Uses the same matching rules as `startsWith()`, so `HtmlTag` and `Newline` helpers honour their
  specialised matching logic.
- **Immutable.** Returns a new `XString` without modifying the original instance.

## Parameters

| Name | Type | Description |
| --- | --- | --- |
| `$prefix` | `Newline\|HtmlTag\|string` | The prefix that should be present at the beginning of the string. Must not be empty. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` with the prefix applied when it was missing. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$prefix` normalises to an empty string. |

## Examples

### Add a missing string prefix

<!-- test:ensure-prefix-add -->
```php
use Orryv\XString;

$value = XString::new('example.com');
$result = $value->ensurePrefix('https://');

#Test: self::assertSame('https://example.com', (string) $result);
```

### Do not duplicate existing prefixes

<!-- test:ensure-prefix-existing -->
```php
use Orryv\XString;

$value = XString::new('https://example.com');
$result = $value->ensurePrefix('https://');

#Test: self::assertSame('https://example.com', (string) $result);
```

### Add HTML tag prefixes with helpers

<!-- test:ensure-prefix-htmltag -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;

$value = XString::new('important');
$result = $value->ensurePrefix(HtmlTag::new('strong'));

#Test: self::assertSame('<strong>important', (string) $result);
```

### Ensure newline prefixes

<!-- test:ensure-prefix-newline -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$value = XString::new('Subject');
$result = $value->ensurePrefix(Newline::new("\r\n"));

#Test: self::assertSame("\r\nSubject", (string) $result);
```

### Reject empty prefixes

<!-- test:ensure-prefix-empty -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('data');

#Test: $this->expectException(InvalidArgumentException::class);
$value->ensurePrefix('');
```

### Original instance remains unchanged

<!-- test:ensure-prefix-immutable -->
```php
use Orryv\XString;

$original = XString::new('value');
$original->ensurePrefix('> ');

#Test: self::assertSame('value', (string) $original);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::ensurePrefix` | `public function ensurePrefix(Newline\|HtmlTag\|string $prefix): self` — Guarantee that a string begins with the specified prefix without mutating the original instance. |
