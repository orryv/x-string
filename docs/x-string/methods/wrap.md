# XString::wrap()

## Table of Contents
- [XString::wrap()](#xstringwrap)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Wrap with identical markers](#wrap-with-identical-markers)
    - [Use different opening and closing fragments](#use-different-opening-and-closing-fragments)
    - [Wrap using HtmlTag helpers](#wrap-using-htmltag-helpers)
    - [Wrap an empty string](#wrap-an-empty-string)
    - [Preserve original instance (immutability)](#preserve-original-instance-immutability)
    - [Mode is preserved when wrapping](#mode-is-preserved-when-wrapping)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function wrap(Newline|HtmlTag|Regex|string $before, Newline|HtmlTag|Regex|string|null $after = null): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Surrounds the current string with the provided `$before` and `$after` fragments. When `$after` is omitted the
same fragment is used on both sides. Fragments may be raw strings or any supported adapter (`Newline`, `HtmlTag`,
`Regex`, or other `Stringable` objects).

## Important notes and considerations

- **Immutability.** Returns a brand-new `XString`; the original instance is never modified.
- **Adapter support.** Helper objects such as [`HtmlTag`](../../html-tag/methods/new.md), [`Newline`](../../newline/methods/new.md),
  and [`Regex`](../../regex/methods/new.md) are automatically converted to strings.
- **Default closing fragment.** Omitting `$after` reuses `$before`, making it easy to apply symmetrical wrappers.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$before` | `Newline\|HtmlTag\|Regex\|string` | — | Fragment inserted before the current value. |
| `$after` | `Newline\|HtmlTag\|Regex\|string` | `null` | Fragment inserted after the current value. Defaults to `$before`. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` containing the wrapped value with the same mode and encoding. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Wrap with identical markers

<!-- test:wrap-basic -->
```php
use Orryv\XString;

$xstring = XString::new('welcome');
$result = $xstring->wrap('**');
#Test: self::assertSame('**welcome**', (string) $result);
```

### Use different opening and closing fragments

<!-- test:wrap-different-before-after -->
```php
use Orryv\XString;

$xstring = XString::new('title');
$result = $xstring->wrap('<', '>');
#Test: self::assertSame('<title>', (string) $result);
```

### Wrap using HtmlTag helpers

<!-- test:wrap-htmltag -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;

$xstring = XString::new('Important');
$result = $xstring->wrap(HtmlTag::new('strong'), HtmlTag::closeTag('strong'));
#Test: self::assertSame('<strong>Important</strong>', (string) $result);
```

### Wrap an empty string

<!-- test:wrap-empty -->
```php
use Orryv\XString;

$xstring = XString::new('');
$result = $xstring->wrap('(', ')');
#Test: self::assertSame('()', (string) $result);
```

### Preserve original instance (immutability)

<!-- test:wrap-immutability -->
```php
use Orryv\XString;

$xstring = XString::new('immutable');
$wrapped = $xstring->wrap('[', ']');
#Test: self::assertSame('immutable', (string) $xstring);
#Test: self::assertSame('[immutable]', (string) $wrapped);
```

### Mode is preserved when wrapping

<!-- test:wrap-byte-mode -->
```php
use Orryv\XString;

$xstring = XString::new('Å')->withMode('bytes');
$result = $xstring->wrap('[', ']');
#Test: self::assertSame('[Å]', (string) $result);
#Test: self::assertSame(4, $result->length());
```

## One-line API table entry

| Method | Version | Signature & Description |
| --- | --- | --- |
| `wrap` | 1.0 | `public function wrap(Newline\|HtmlTag\|Regex\|string $before, Newline\|HtmlTag\|Regex\|string|null $after = null): self` — Wrap the current string with the provided fragments. |
