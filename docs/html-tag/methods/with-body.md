# HtmlTag::withBody()

## Table of Contents
- [HtmlTag::withBody()](#htmltagwithbody)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Attach a simple body fragment](#attach-a-simple-body-fragment)
    - [Combine multiple fragments](#combine-multiple-fragments)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function withBody(HtmlTag|Newline|Regex|Stringable|string|array $body): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Return a clone of the current `HtmlTag` with additional body content appended after the opening angle bracket. The body may be
provided as strings, other adapters (`HtmlTag`, `Newline`, `Regex`), any `Stringable` implementation, or arrays mixing these
fragment types. The supplied fragments are concatenated in the order they are provided.

## Important notes and considerations

- **Immutable clone.** Calling `withBody()` never mutates the original instance.
- **Self-closing / closing tags.** Attempting to attach a body to a self-closing or closing tag raises an
  `InvalidArgumentException`.
- **Chaining is cumulative.** Repeated calls append to the existing body, allowing incremental construction of complex markup.
- **Closing tags.** Pair `withBody()` with [`withEndTag()`](with-end-tag.md) when you need to emit the closing tag automatically.

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$body` | — | `HtmlTag\|Newline\|Regex\|Stringable\|string\|array<HtmlTag\|Newline\|Regex\|Stringable\|string>` | Fragment or
fragments to append inside the tag. Arrays are flattened and concatenated. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | New `HtmlTag` instance containing the appended body content. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | The tag is self-closing or closing, or the provided fragments are invalid. |

## Examples

### Attach a simple body fragment

<!-- test:html-tag-with-body-basic -->
```php
use Orryv\XString\HtmlTag;

$tag = HtmlTag::new('span')->withBody('Hello world');
#Test: self::assertSame('<span>Hello world', (string) $tag);
```

### Combine multiple fragments

<!-- test:html-tag-with-body-multiple -->
```php
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;

$tag = HtmlTag::new('div')
    ->withBody('Hello ')
    ->withBody([
        HtmlTag::new('strong')->withBody('World')->withEndTag(false),
        Newline::new(),
    ]);
#Test: self::assertSame('<div>Hello <strong>World</strong>' . PHP_EOL, (string) $tag);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `HtmlTag::withBody` | `public function withBody(HtmlTag\|Newline\|Regex\|Stringable\|string\|array $body): self` — Append body
fragments to an opening tag without mutating the original instance. |
