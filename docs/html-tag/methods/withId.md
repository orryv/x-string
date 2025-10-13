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
    - [Whitespace is trimmed automatically](#whitespace-is-trimmed-automatically)
    - [Original tag remains unchanged](#original-tag-remains-unchanged)
    - [Replace an existing ID with a new one](#replace-an-existing-id-with-a-new-one)
    - [Reject calling on a closing tag](#reject-calling-on-a-closing-tag)
    - [Reject empty IDs](#reject-empty-ids)
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

Return an immutable clone of the current `HtmlTag` with the provided element ID applied. Any surrounding whitespace is trimmed so
the ID stored on the tag is the cleaned value.

## Important notes and considerations

- **Immutable clone.** Each call returns a brand-new `HtmlTag` instance.
- **Whitespace trimmed.** Leading and trailing whitespace on the provided ID is removed.
- **Closing tags unsupported.** Calling the method on a closing tag raises an `InvalidArgumentException`.

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$id` | — | `string` | ID attribute value. Must not be empty after trimming whitespace. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | New `HtmlTag` instance containing the specified ID. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | The tag is a closing element or `$id` is empty after trimming. |

## Examples

### Assign an ID attribute

<!-- test:html-tag-with-id-basic -->
```php
use Orryv\XString\HtmlTag;

$tag = HtmlTag::new('nav')->withId('primary-nav');
#Test: self::assertSame('<nav id="primary-nav">', (string) $tag);
```

### Whitespace is trimmed automatically

<!-- test:html-tag-with-id-trim -->
```php
use Orryv\XString\HtmlTag;

$tag = HtmlTag::new('section')->withId("  hero  ");
#Test: self::assertSame('<section id="hero">', (string) $tag);
```

### Original tag remains unchanged

<!-- test:html-tag-with-id-immutable -->
```php
use Orryv\XString\HtmlTag;

$original = HtmlTag::new('aside');
$clone = $original->withId('sidebar');
#Test: self::assertSame('<aside>', (string) $original);
#Test: self::assertSame('<aside id="sidebar">', (string) $clone);
```

### Replace an existing ID with a new one

<!-- test:html-tag-with-id-replace -->
```php
use Orryv\XString\HtmlTag;

$tag = HtmlTag::new('form')
    ->withId('login-form')
    ->withId('signup-form');
#Test: self::assertSame('<form id="signup-form">', (string) $tag);
```

### Reject calling on a closing tag

<!-- test:html-tag-with-id-closing -->
```php
use Orryv\XString\HtmlTag;
use InvalidArgumentException;

$closing = HtmlTag::closeTag('section');
#Test: $this->expectException(InvalidArgumentException::class);
$closing->withId('nope');
```

### Reject empty IDs

<!-- test:html-tag-with-id-empty -->
```php
use Orryv\XString\HtmlTag;
use InvalidArgumentException;

$tag = HtmlTag::new('div');
#Test: $this->expectException(InvalidArgumentException::class);
$tag->withId('   ');
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `HtmlTag::withId` | `public function withId(string $id): self` — Apply an ID attribute without mutating the original tag. |
