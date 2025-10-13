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
    - [Delegated class attributes merge with `withClass()`](#delegated-class-attributes-merge-with-withclass)
    - [Delegated ID attributes reuse `withId()`](#delegated-id-attributes-reuse-withid)
    - [Original instance stays unchanged](#original-instance-stays-unchanged)
    - [Reject calling on a closing tag](#reject-calling-on-a-closing-tag)
    - [Reject empty attribute names](#reject-empty-attribute-names)
    - [Reject null class attributes](#reject-null-class-attributes)
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

Return an immutable clone of the current `HtmlTag` with the provided attribute appended. The helper supports boolean attributes
(by omitting the value) and optional case-sensitive attribute names.

## Important notes and considerations

- **Delegates for `id`/`class`.** When `$attr_name` is `id` or `class`, the call internally defers to [`withId()`](./withId.md)
  and [`withClass()`](./withClass.md).
- **Immutable clone.** Each call returns a new `HtmlTag` instance; the original tag is unchanged.
- **Closing tags unsupported.** Calling the method on a closing tag raises an `InvalidArgumentException`.
- **Case preservation.** Pass `$case_sensitive = true` to retain the original attribute casing.

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$attr_name` | — | `string` | Attribute name. Must be non-empty after trimming. |
| `$attr_value` | `null` | `null\|string` | Optional attribute value. When `null`, a boolean attribute is emitted (e.g. `required`). |
| `$case_sensitive` | `false` | `bool` | Preserve the provided attribute name casing when `true`. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | New `HtmlTag` instance with the attribute applied. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | The tag is closing-only, the attribute name is empty, `class`/`id` are supplied without a value, or the delegated helper rejects the provided value. |

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

### Delegated class attributes merge with `withClass()`

<!-- test:html-tag-with-attribute-class -->
```php
use Orryv\XString\HtmlTag;

$tag = HtmlTag::new('div')->withClass('card')->withAttribute('class', 'primary shadow');
#Test: self::assertSame('<div class="card primary shadow">', (string) $tag);
```

### Delegated ID attributes reuse `withId()`

<!-- test:html-tag-with-attribute-id -->
```php
use Orryv\XString\HtmlTag;

$tag = HtmlTag::new('section')->withAttribute('id', ' hero ');
#Test: self::assertSame('<section id="hero">', (string) $tag);
```

### Original instance stays unchanged

<!-- test:html-tag-with-attribute-immutable -->
```php
use Orryv\XString\HtmlTag;

$original = HtmlTag::new('a')->withAttribute('href', '#top');
$clone = $original->withAttribute('target', '_blank');
#Test: self::assertSame('<a href="#top">', (string) $original);
#Test: self::assertSame('<a href="#top" target="_blank">', (string) $clone);
```

### Reject calling on a closing tag

<!-- test:html-tag-with-attribute-closing -->
```php
use Orryv\XString\HtmlTag;
use InvalidArgumentException;

$closing = HtmlTag::closeTag('div');
#Test: $this->expectException(InvalidArgumentException::class);
$closing->withAttribute('data-test', 'nope');
```

### Reject empty attribute names

<!-- test:html-tag-with-attribute-empty-name -->
```php
use Orryv\XString\HtmlTag;
use InvalidArgumentException;

$tag = HtmlTag::new('span');
#Test: $this->expectException(InvalidArgumentException::class);
$tag->withAttribute('   ', 'value');
```

### Reject null class attributes

<!-- test:html-tag-with-attribute-class-null -->
```php
use Orryv\XString\HtmlTag;
use InvalidArgumentException;

$tag = HtmlTag::new('div');
#Test: $this->expectException(InvalidArgumentException::class);
$tag->withAttribute('class');
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `HtmlTag::withAttribute` | `public function withAttribute(string $attr_name, ?string $attr_value = null, bool $case_sensitive = false): self` — Append an attribute to the tag without mutating the original instance. |
