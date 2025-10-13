# HtmlTag::withClass()

## Table of Contents
- [HtmlTag::withClass()](#htmltagwithclass)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Apply multiple classes from whitespace-delimited input](#apply-multiple-classes-from-whitespace-delimited-input)
    - [Merge array inputs while de-duplicating classes](#merge-array-inputs-while-de-duplicating-classes)
    - [Original instance stays unchanged](#original-instance-stays-unchanged)
    - [Calling without class names throws](#calling-without-class-names-throws)
    - [Reject calling on a closing tag](#reject-calling-on-a-closing-tag)
    - [Reject arrays containing whitespace](#reject-arrays-containing-whitespace)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function withClass(string|array ...$class_name): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Return an immutable clone of the current `HtmlTag` with one or more CSS class names appended. Class names may be supplied as
individual string arguments, whitespace-delimited strings, arrays, or a mixture of these formats.

## Important notes and considerations

- **Immutable clone.** The original `HtmlTag` is never mutated; repeated calls safely chain modifications.
- **Flexible input.** Strings are split on whitespace and trimmed; nested arrays are flattened recursively.
- **No duplicates.** Existing classes are preserved and new classes are appended only when they do not already exist.
- **Closing tags unsupported.** Calling the modifier on a closing tag throws an `InvalidArgumentException`.

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$class_name` | — | `string\|array<int, string> ...` | One or more arguments describing classes to append. Strings are split on whitespace; arrays are flattened. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | New `HtmlTag` instance with the requested classes applied. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | The tag represents a closing element, receives no class names, or a provided class contains internal whitespace. |

## Examples

### Apply multiple classes from whitespace-delimited input

<!-- test:html-tag-with-class-multiple-strings -->
```php
use Orryv\XString\HtmlTag;

$tag = HtmlTag::new('button')
    ->withClass('btn primary is-active');
#Test: self::assertSame('<button class="btn primary is-active">', (string) $tag);
```

### Merge array inputs while de-duplicating classes

<!-- test:html-tag-with-class-deduplicate -->
```php
use Orryv\XString\HtmlTag;

$tag = HtmlTag::new('article', false, true)
    ->withClass(['feature', 'card'], ['card', 'muted']);
#Test: self::assertSame('<article class="feature card muted">', (string) $tag);
```

### Original instance stays unchanged

<!-- test:html-tag-with-class-immutable -->
```php
use Orryv\XString\HtmlTag;

$original = HtmlTag::new('section')->withClass('hero');
$clone = $original->withClass('padded');
#Test: self::assertSame('<section class="hero">', (string) $original);
#Test: self::assertSame('<section class="hero padded">', (string) $clone);
```

### Calling without class names throws

<!-- test:html-tag-with-class-missing-args -->
```php
use Orryv\XString\HtmlTag;
use InvalidArgumentException;

$tag = HtmlTag::new('nav');
#Test: $this->expectException(InvalidArgumentException::class);
$tag->withClass();
```

### Reject calling on a closing tag

<!-- test:html-tag-with-class-closing-exception -->
```php
use Orryv\XString\HtmlTag;
use InvalidArgumentException;

$closing = HtmlTag::closeTag('div');
#Test: $this->expectException(InvalidArgumentException::class);
$closing->withClass('nope');
```

### Reject arrays containing whitespace

<!-- test:html-tag-with-class-invalid-array -->
```php
use Orryv\XString\HtmlTag;
use InvalidArgumentException;

$tag = HtmlTag::new('div');
#Test: $this->expectException(InvalidArgumentException::class);
$tag->withClass(['bad class']);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `HtmlTag::withClass` | `public function withClass(string|array ...$class_name): self` — Append CSS classes to the tag without mutating the original instance. |
