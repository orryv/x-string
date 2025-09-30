# HtmlTag::withClass()

## Table of Contents
- [HtmlTag::withClass()](#htmltagwithclass)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Add a single class](#add-a-single-class)
    - [Merge multiple classes at once](#merge-multiple-classes-at-once)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function withClass(string|array ...$class_name): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Return a clone of the current `HtmlTag` with one or more CSS class names added. Provide classes as individual string arguments,
arrays, or a combination of both; string input is split on whitespace, while array input is treated as a list of individual
class names.

## Important notes and considerations

- **Immutable clone.** The original tag is never mutated.
- **Duplicates removed.** Class names are normalised (trimmed) and de-duplicated.
- **Not allowed on closing tags.** Invoking this method on a closing tag raises an `InvalidArgumentException`.

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$class_name` | — | `string\|array<int, string> ...` | One or more arguments describing the classes to append. Strings are split on whitespace; arrays are treated as lists. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | New `HtmlTag` instance with the requested class names applied. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | The tag is a closing tag, a provided class is empty, or contains internal whitespace. |

## Examples

### Add a single class

<!-- test:html-tag-with-class-single -->
```php
use Orryv\XString\HtmlTag;

$tag = HtmlTag::new('section')->withClass('hero');
#Test: self::assertSame('<section class="hero">', (string) $tag);
```

### Merge multiple classes at once

<!-- test:html-tag-with-class-multiple -->
```php
use Orryv\XString\HtmlTag;

$tag = HtmlTag::new('p')->withClass(['intro', 'lead'], 'highlight');
#Test: self::assertSame('<p class="intro lead highlight">', (string) $tag);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `HtmlTag::withClass` | `public function withClass(string|array ...$class_name): self` — Return a clone with the provided CSS classes applied. |
