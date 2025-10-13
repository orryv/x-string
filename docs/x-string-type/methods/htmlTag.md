# XStringType::htmlTag()

## Table of Contents
- [XStringType::htmlTag()](#xstringtypehtmltag)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Create an opening tag with attributes](#create-an-opening-tag-with-attributes)
    - [Generate a self-closing tag](#generate-a-self-closing-tag)
    - [Original tag remains unchanged](#original-tag-remains-unchanged)
    - [Combine with XString to compose markup](#combine-with-xstring-to-compose-markup)
    - [Invalid tag names raise an exception](#invalid-tag-names-raise-an-exception)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public static function htmlTag(string $tag_name, bool $self_closing = false, bool $case_sensitive = false): HtmlTag
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv` | Static factory | ✓ | Public |

## Description

Create a new immutable [`HtmlTag`](../../html-tag/methods/new.md) adapter representing an opening HTML element. The helper mirrors
`HtmlTag::new()` while offering a concise, type-aware factory to use alongside other `XStringType` helpers.

## Important notes and considerations

- **Validated tag names.** The tag must start with a letter and may contain letters, digits, `-`, `_`, or `:`.
- **Self-closing support.** Pass `$self_closing = true` to emit `<tag />` markup.
- **Casing control.** Provide `$case_sensitive = true` to preserve the supplied casing; otherwise the tag name is lowercased.
- **Immutable adapters.** Modifier methods such as `withClass()` and `withAttribute()` return new instances—your original tag stays untouched.

## Parameters

| Parameter | Type | Default | Description |
| --- | --- | --- | --- |
| `$tag_name` | `string` | — | HTML tag name (e.g. `div`, `section`). Must satisfy the validation rules mentioned above. |
| `$self_closing` | `bool` | `false` | When `true`, render the tag using the self-closing `<tag />` form. |
| `$case_sensitive` | `bool` | `false` | Preserve the provided casing instead of normalising to lowercase. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `HtmlTag` | ✓ | Opening-tag adapter ready for fluent chaining. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$tag_name` is empty or contains unsupported characters. |

## Examples

### Create an opening tag with attributes

<!-- test:xstring-type-htmltag-attributes -->
```php
use Orryv\XStringType;

$tag = XStringType::htmlTag('section')
    ->withClass('hero')
    ->withAttribute('data-theme', 'dark');

#Test: self::assertSame('<section class="hero" data-theme="dark">', (string) $tag);
```

### Generate a self-closing tag

<!-- test:xstring-type-htmltag-self-closing -->
```php
use Orryv\XStringType;

$meta = XStringType::htmlTag('meta', self_closing: true)
    ->withAttribute('charset', 'utf-8');

#Test: self::assertSame('<meta charset="utf-8" />', (string) $meta);
```

### Original tag remains unchanged

<!-- test:xstring-type-htmltag-immutable -->
```php
use Orryv\XStringType;

$original = XStringType::htmlTag('div');
$modified = $original->withClass('card');

#Test: self::assertSame('<div>', (string) $original);
#Test: self::assertSame('<div class="card">', (string) $modified);
```

### Combine with XString to compose markup

<!-- test:xstring-type-htmltag-compose -->
```php
use Orryv\XString;
use Orryv\XStringType;

$markup = XString::new([
    XStringType::htmlTag('h1')->withBody('Docs Ready')->withEndTag(false),
    XStringType::htmlTag('p')->withBody('Generated from examples.')->withEndTag(false),
]);

#Test: self::assertSame('<h1>Docs Ready</h1><p>Generated from examples.</p>', (string) $markup);
```

### Invalid tag names raise an exception

<!-- test:xstring-type-htmltag-invalid -->
```php
use InvalidArgumentException;
use Orryv\XStringType;

#Test: $this->expectException(InvalidArgumentException::class);
XStringType::htmlTag('1-invalid');
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XStringType::htmlTag` | `public static function htmlTag(string $tag_name, bool $self_closing = false, bool $case_sensitive = false): HtmlTag` — Create an opening `HtmlTag` adapter configured with the requested options. |
