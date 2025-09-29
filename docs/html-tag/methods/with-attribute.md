# HtmlTag::withAttribute()

## Table of Contents
- [HtmlTag::withAttribute()](#htmltagwithattribute)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Add boolean and valued attributes](#add-boolean-and-valued-attributes)
    - [Preserve attribute casing](#preserve-attribute-casing)
    - [Class attributes merge with `withClass()`](#class-attributes-merge-with-withclass)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function withAttribute(string $attr_name, ?string $attr_value = null, bool $case_sensitive = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Return an immutable clone of the current `HtmlTag` with the specified attribute appended. The helper supports boolean attributes
(when `$attr_value` is `null`) and optional case-sensitive attribute names.

## Important notes and considerations

- **Delegates to ID/Class helpers.** When `$attr_name` is `id` or `class`, the call defers to [`withId()`](./with-id.md) / [`withClass()`](./with-class.md).
- **Immutability.** Each invocation returns a new `HtmlTag` instance.
- **Closing tags unsupported.** Invoking this method on a closing tag raises an `InvalidArgumentException`.

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$attr_name` | — | `string` | Attribute name. Must be non-empty after trimming. |
| `$attr_value` | `null` | `null\|string` | Optional attribute value. When `null`, a boolean attribute is rendered (e.g. `required`). |
| `$case_sensitive` | `false` | `bool` | Preserve the provided attribute name casing. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | New `HtmlTag` instance with the attribute applied. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | The tag is closing-only, the attribute name is empty, or `class`/`id` are supplied without a value. |

## Examples

### Add boolean and valued attributes

<!-- test:html-tag-with-attribute-basic -->
```php
use Orryv\XString\HtmlTag;

$tag = HtmlTag::new('input', true)
    ->withAttribute('type', 'email')
    ->withAttribute('required');
#Test: self::assertSame('<input type="email" required />', (string) $tag);
```

### Preserve attribute casing

<!-- test:html-tag-with-attribute-case -->
```php
use Orryv\XString\HtmlTag;

$tag = HtmlTag::new('div')->withAttribute('Data-State', 'active', true);
#Test: self::assertSame('<div Data-State="active">', (string) $tag);
```

### Class attributes merge with `withClass()`

<!-- test:html-tag-with-attribute-class -->
```php
use Orryv\XString\HtmlTag;

$tag = HtmlTag::new('div')->withClass('card')->withAttribute('class', 'primary shadow');
#Test: self::assertSame('<div class="card primary shadow">', (string) $tag);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `HtmlTag::withAttribute` | `public function withAttribute(string $attr_name, ?string $attr_value = null, bool $case_sensitive = false): self` — Return a clone with the provided attribute applied. |
