# XStringType::htmlCloseTag()

## Table of Contents
- [XStringType::htmlCloseTag()](#xstringtypehtmlclosetag)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Create a matching closing tag](#create-a-matching-closing-tag)
    - [Respect custom casing](#respect-custom-casing)
    - [Use closing tags in searches](#use-closing-tags-in-searches)
    - [Invalid tag names raise an exception](#invalid-tag-names-raise-an-exception)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public static function htmlCloseTag(string $tag_name, bool $case_sensitive = false): HtmlTag
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv` | Static factory | ✓ | Public |

## Description

Create an immutable [`HtmlTag`](../../html-tag/methods/closeTag.md) adapter that renders a closing HTML tag. The helper mirrors
`HtmlTag::closeTag()` while keeping fluent `XStringType` pipelines readable when you need to express structural HTML fragments.

## Important notes and considerations

- **Same validation rules as `htmlTag()`.** Tag names must be non-empty and contain only supported characters.
- **No attributes.** Closing tags cannot carry classes, IDs, or attributes—attempting to call those modifiers throws an exception.
- **Case sensitivity opt-in.** Pass `$case_sensitive = true` to preserve the provided casing; otherwise the tag is normalised to lowercase.

## Parameters

| Parameter | Type | Default | Description |
| --- | --- | --- | --- |
| `$tag_name` | `string` | — | HTML tag name for the closing element. |
| `$case_sensitive` | `bool` | `false` | Preserve the supplied casing when rendering the tag. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `HtmlTag` | ✓ | Closing-tag adapter ready for composition or comparisons. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$tag_name` is empty or contains unsupported characters. |

## Examples

### Create a matching closing tag

<!-- test:xstring-type-htmlclosetag-basic -->
```php
use Orryv\XStringType;

$closing = XStringType::htmlCloseTag('article');

#Test: self::assertSame('</article>', (string) $closing);
```

### Respect custom casing

<!-- test:xstring-type-htmlclosetag-case -->
```php
use Orryv\XStringType;

$closing = XStringType::htmlCloseTag('MyComponent', case_sensitive: true);

#Test: self::assertSame('</MyComponent>', (string) $closing);
```

### Use closing tags in searches

<!-- test:xstring-type-htmlclosetag-search -->
```php
use Orryv\XString;
use Orryv\XStringType;

$fragment = XString::new('<section><p>Body</p></section>');

#Test: self::assertTrue($fragment->contains(XStringType::htmlCloseTag('section')));
#Test: self::assertFalse($fragment->contains(XStringType::htmlCloseTag('article')));
```

### Invalid tag names raise an exception

<!-- test:xstring-type-htmlclosetag-invalid -->
```php
use InvalidArgumentException;
use Orryv\XStringType;

#Test: $this->expectException(InvalidArgumentException::class);
XStringType::htmlCloseTag('');
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XStringType::htmlCloseTag` | `public static function htmlCloseTag(string $tag_name, bool $case_sensitive = false): HtmlTag` — Create a closing `HtmlTag` adapter for the requested element. |
