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
    - [Insert newline even when the body is empty](#insert-newline-even-when-the-body-is-empty)
    - [Original tag remains unchanged](#original-tag-remains-unchanged)
    - [Reject calling on a self-closing tag](#reject-calling-on-a-self-closing-tag)
    - [Reject calling on a closing tag](#reject-calling-on-a-closing-tag)
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

Return an immutable clone of the current `HtmlTag` that automatically appends the matching closing tag when cast to a string.
When `$append_newline` is `true` (the default), a platform-specific newline is inserted between the body and the closing tag to
aid readability.

## Important notes and considerations

- **Immutable clone.** The original instance is never mutated.
- **Body optional.** `withEndTag()` can be used with or without body content and still emits well-formed markup.
- **Optional newline.** Pass `false` to `$append_newline` for inline content; keep `true` to add a trailing newline.
- **Self-closing & closing tags unsupported.** Calling on those tag types raises an `InvalidArgumentException`.

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
| `InvalidArgumentException` | The tag is self-closing or already represents a closing tag. |

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

### Insert newline even when the body is empty

<!-- test:html-tag-with-end-tag-empty-body -->
```php
use Orryv\XString\HtmlTag;

$tag = HtmlTag::new('div')->withEndTag();
#Test: self::assertSame('<div>' . PHP_EOL . '</div>', (string) $tag);
```

### Original tag remains unchanged

<!-- test:html-tag-with-end-tag-immutable -->
```php
use Orryv\XString\HtmlTag;

$original = HtmlTag::new('article');
$clone = $original->withEndTag(false);
#Test: self::assertSame('<article>', (string) $original);
#Test: self::assertSame('<article></article>', (string) $clone);
```

### Reject calling on a self-closing tag

<!-- test:html-tag-with-end-tag-self-closing -->
```php
use Orryv\XString\HtmlTag;
use InvalidArgumentException;

$tag = HtmlTag::new('img', true);
#Test: $this->expectException(InvalidArgumentException::class);
$tag->withEndTag();
```

### Reject calling on a closing tag

<!-- test:html-tag-with-end-tag-closing -->
```php
use Orryv\XString\HtmlTag;
use InvalidArgumentException;

$closing = HtmlTag::closeTag('div');
#Test: $this->expectException(InvalidArgumentException::class);
$closing->withEndTag();
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `HtmlTag::withEndTag` | `public function withEndTag(bool $append_newline = true): self` — Append the matching closing tag, optionally inserting a trailing newline for readability. |
