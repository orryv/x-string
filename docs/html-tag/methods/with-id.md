# HtmlTag::withId()

## Table of Contents
- [HtmlTag::withId()](#htmltagwithid)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Assign an ID attribute](#assign-an-id-attribute)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function withId(string $id): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Return an immutable clone of the current `HtmlTag` with the specified element ID attached.

## Important notes and considerations

- **Immutability.** Always returns a new `HtmlTag` instance.
- **Whitespace trimmed.** Leading/trailing whitespace is removed from the provided ID.
- **Not allowed on closing tags.** Calling `withId()` on a closing tag raises an `InvalidArgumentException`.

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$id` | — | `string` | ID attribute value. Must not be empty after trimming. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | New `HtmlTag` instance with the ID applied. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | The tag is a closing tag or `$id` is empty after trimming. |

## Examples

### Assign an ID attribute

<!-- test:html-tag-with-id-basic -->
```php
use Orryv\XString\HtmlTag;

$tag = HtmlTag::new('nav')->withId('primary-nav');
#Test: self::assertSame('<nav id="primary-nav">', (string) $tag);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `HtmlTag::withId` | `public function withId(string $id): self` — Return a clone with the provided ID attribute set. |
