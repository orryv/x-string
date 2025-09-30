# HtmlTag::withEndTag()

## Table of Contents
- [HtmlTag::withEndTag()](#htmltagwithendtag)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Append a closing tag with a trailing newline](#append-a-closing-tag-with-a-trailing-newline)
    - [Inline closing tag without a newline](#inline-closing-tag-without-a-newline)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function withEndTag(bool $append_newline = true): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Return a clone of the current `HtmlTag` that automatically appends the matching closing tag when converted to a string. When the
optional `$append_newline` flag is `true` (the default), a platform-specific newline is inserted between the body and the closing
tag to aid readability.

## Important notes and considerations

- **Immutable clone.** The method never mutates the original tag instance.
- **Body optional.** `withEndTag()` can be used with or without calling [`withBody()`](with-body.md). An empty body still produces
  well-formed `<tag></tag>` markup.
- **Newline control.** Pass `false` to `$append_newline` to omit the trailing newline when rendering inline content.
- **Self-closing / closing tags.** Attempting to call this method on a closing or self-closing tag raises an
  `InvalidArgumentException`.

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$append_newline` | `true` | `bool` | When `true`, append `PHP_EOL` between the body and closing tag. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | New `HtmlTag` instance that renders with a closing tag. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | The tag is closing or self-closing. |

## Examples

### Append a closing tag with a trailing newline

<!-- test:html-tag-with-end-tag-default -->
```php
use Orryv\XString\HtmlTag;

$tag = HtmlTag::new('p')
    ->withBody('Hello there')
    ->withEndTag();
#Test: self::assertSame("<p>Hello there" . PHP_EOL . "</p>", (string) $tag);
```

### Inline closing tag without a newline

<!-- test:html-tag-with-end-tag-inline -->
```php
use Orryv\XString\HtmlTag;

$tag = HtmlTag::new('span')
    ->withBody('Inline content')
    ->withEndTag(false);
#Test: self::assertSame('<span>Inline content</span>', (string) $tag);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `HtmlTag::withEndTag` | `public function withEndTag(bool $append_newline = true): self` — Append the matching closing tag,
optionally inserting a trailing newline for block-level readability. |
