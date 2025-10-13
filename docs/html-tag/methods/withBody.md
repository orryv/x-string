# HtmlTag::withBody()

## Table of Contents
- [HtmlTag::withBody()](#htmltagwithbody)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Attach a simple body fragment](#attach-a-simple-body-fragment)
    - [Combine multiple fragment types](#combine-multiple-fragment-types)
    - [Append incrementally](#append-incrementally)
    - [Original tag remains unchanged](#original-tag-remains-unchanged)
    - [Reject calling on a self-closing tag](#reject-calling-on-a-self-closing-tag)
    - [Reject calling on a closing tag](#reject-calling-on-a-closing-tag)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function withBody(HtmlTag|Newline|Regex|Stringable|string|array $body): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Return an immutable clone of the current `HtmlTag` with additional body content appended after the opening tag. The body may be
provided as plain strings, other adapters (`HtmlTag`, `Newline`, `Regex`), any `Stringable` implementation, or arrays mixing these
fragment types.

## Important notes and considerations

- **Immutable clone.** `withBody()` never mutates the original instance.
- **Self-closing & closing tags unsupported.** Attempting to append a body to a self-closing or closing tag raises an
  `InvalidArgumentException`.
- **Cumulative appends.** Each call appends to the existing body, allowing incremental construction of complex markup.
- **Pair with `withEndTag()`.** Use [`withEndTag()`](./withEndTag.md) to emit the closing tag automatically when needed.

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$body` | — | `HtmlTag\|Newline\|Regex\|Stringable\|string\|array<HtmlTag\|Newline\|Regex\|Stringable\|string>` | Fragment or fragments to append inside the tag. Arrays are flattened and concatenated. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | New `HtmlTag` instance containing the appended body content. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | The tag is self-closing or closing. |

## Examples

### Attach a simple body fragment

<!-- test:html-tag-with-body-basic -->
```php
use Orryv\XString\HtmlTag;

$tag = HtmlTag::new('span')->withBody('Hello world');
#Test: self::assertSame('<span>Hello world', (string) $tag);
```

### Combine multiple fragment types

<!-- test:html-tag-with-body-fragments -->
```php
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;
use Stringable;

$tag = HtmlTag::new('div')
    ->withBody([
        HtmlTag::new('strong')->withBody('Title')->withEndTag(false),
        Newline::new(),
        new class implements Stringable {
            public function __toString(): string
            {
                return 'Summary';
            }
        },
    ]);
#Test: self::assertSame('<div><strong>Title</strong>' . PHP_EOL . 'Summary', (string) $tag);
```

### Append incrementally

<!-- test:html-tag-with-body-append -->
```php
use Orryv\XString\HtmlTag;

$tag = HtmlTag::new('p')
    ->withBody('First ')
    ->withBody('Second');
#Test: self::assertSame('<p>First Second', (string) $tag);
```

### Original tag remains unchanged

<!-- test:html-tag-with-body-immutable -->
```php
use Orryv\XString\HtmlTag;

$original = HtmlTag::new('article');
$clone = $original->withBody('Summary');
#Test: self::assertSame('<article>', (string) $original);
#Test: self::assertSame('<article>Summary', (string) $clone);
```

### Reject calling on a self-closing tag

<!-- test:html-tag-with-body-self-closing -->
```php
use Orryv\XString\HtmlTag;
use InvalidArgumentException;

$tag = HtmlTag::new('img', true);
#Test: $this->expectException(InvalidArgumentException::class);
$tag->withBody('image');
```

### Reject calling on a closing tag

<!-- test:html-tag-with-body-closing -->
```php
use Orryv\XString\HtmlTag;
use InvalidArgumentException;

$closing = HtmlTag::closeTag('div');
#Test: $this->expectException(InvalidArgumentException::class);
$closing->withBody('nope');
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `HtmlTag::withBody` | `public function withBody(HtmlTag|Newline|Regex|Stringable|string|array $body): self` — Append body fragments to an opening tag without mutating the original instance. |
