# HtmlTag::closeTag()

## Table of Contents
- [HtmlTag::closeTag()](#htmltagclosetag)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Create a matching closing tag](#create-a-matching-closing-tag)
    - [Preserve custom casing](#preserve-custom-casing)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public static function closeTag(string $tag_name, bool $case_sensitive = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Static factory | ✓ | Public |

## Description

Construct an immutable `HtmlTag` adapter that renders a closing tag (e.g. `</div>`). Useful when pairing with an opening tag
created via [`HtmlTag::new()`](./new.md).

## Important notes and considerations

- **Same validation rules.** The tag name constraints are identical to [`HtmlTag::new()`](./new.md).
- **No attributes.** Closing tags cannot carry attributes; modifier methods (`withClass()`, etc.) throw exceptions.
- **Optional casing.** Provide `$case_sensitive = true` to keep the supplied casing.
- **Alias.** [`HtmlTag::endTag()`](./with-end-tag.md) is provided as a semantic alias for this factory.

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$tag_name` | — | `string` | HTML tag name to close. |
| `$case_sensitive` | `false` | `bool` | Preserve the provided casing instead of normalising to lowercase. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Closing-tag adapter ready for string conversion. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$tag_name` is empty or contains invalid characters. |

## Examples

### Create a matching closing tag

<!-- test:html-tag-close-basic -->
```php
use Orryv\XString\HtmlTag;

$closing = HtmlTag::closeTag('article');
#Test: self::assertSame('</article>', (string) $closing);
```

### Preserve custom casing

<!-- test:html-tag-close-case -->
```php
use Orryv\XString\HtmlTag;

$closing = HtmlTag::closeTag('MyComponent', true);
#Test: self::assertSame('</MyComponent>', (string) $closing);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `HtmlTag::closeTag` | `public static function closeTag(string $tag_name, bool $case_sensitive = false): self` — Create a closing tag adapter for the given element name. |
