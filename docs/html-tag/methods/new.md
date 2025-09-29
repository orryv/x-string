# HtmlTag::new()

## Table of Contents
- [HtmlTag::new()](#htmltagnew)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Create a simple opening tag](#create-a-simple-opening-tag)
    - [Generate a self-closing tag](#generate-a-self-closing-tag)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public static function new(string $tag_name, bool $self_closing = false, bool $case_sensitive = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Static factory | ✓ | Public |

## Description

Create a new immutable `HtmlTag` adapter representing an opening HTML element. The factory normalises the tag name, optionally
marks it as self-closing, and controls whether the original casing should be preserved when rendered.

## Important notes and considerations

- **Validated tag name.** Only alphanumeric tag names (including `-`, `_`, and `:`) that start with a letter are accepted.
- **Immutability.** Every chained modifier (`withClass()`, `withId()`, etc.) returns a brand-new `HtmlTag` instance.
- **Case handling.** By default tag names are normalised to lowercase. Pass `$case_sensitive = true` to keep the original casing.

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$tag_name` | — | `string` | HTML tag name (e.g. `div`, `section`). Must start with a letter and may contain `-`, `_`, or `:`. |
| `$self_closing` | `false` | `bool` | Render the tag with a trailing `/>` when `true`. |
| `$case_sensitive` | `false` | `bool` | Preserve the provided casing instead of converting the tag to lowercase. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | New `HtmlTag` representing the requested opening element. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$tag_name` is empty or contains unsupported characters. |

## Examples

### Create a simple opening tag

<!-- test:html-tag-new-basic -->
```php
use Orryv\XString\HtmlTag;

$tag = HtmlTag::new('div')->withClass('card');
#Test: self::assertSame('<div class="card">', (string) $tag);
```

### Generate a self-closing tag

<!-- test:html-tag-new-self-closing -->
```php
use Orryv\XString\HtmlTag;

$fav = HtmlTag::new('link', true)->withAttribute('rel', 'preconnect')->withAttribute('href', 'https://example.com');
#Test: self::assertSame('<link rel="preconnect" href="https://example.com" />', (string) $fav);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `HtmlTag::new` | `public static function new(string $tag_name, bool $self_closing = false, bool $case_sensitive = false): self` — Create a new HTML opening-tag adapter. |
