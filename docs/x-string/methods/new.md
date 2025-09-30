# XString::new()

## Table of Contents
- [XString::new()](#xstringnew)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Create from a plain string](#create-from-a-plain-string)
    - [Combine array values without mutating the source](#combine-array-values-without-mutating-the-source)
    - [Combine adapters, including HtmlTag](#combine-adapters-including-htmltag)
    - [Combine adapters, including HtmlTag with end Tag](#combine-adapters-including-htmltag-with-end-tag)
    - [Empty input defaults to an empty string](#empty-input-defaults-to-an-empty-string)
    - [Unsupported data in the array raises an exception](#unsupported-data-in-the-array-raises-an-exception)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public static function new(Newline|HtmlTag|Regex|Stringable|string|array<Newline|HtmlTag|Regex|Stringable|string> $data = ''): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Static factory | ✓ | Public |

## Description

Creates a new immutable `XString` instance from the supplied data. The method accepts individual strings, newline adapters, HTML tag adapters,
regular-expression adapters, other `Stringable` implementations, or arrays containing any mixture of those values. Array inputs are concatenated in order without
introducing extra separators. When no argument is provided the method yields an empty `XString`.

**Algorithm overview:**

- Normalises `$data` into an ordered list of string fragments:
  - Single values are wrapped in an array.
  - `Newline`/`HtmlTag`/`Regex` adapters are converted to their string representation.
- Validates that every fragment is scalar string data.
- Concatenates the fragments without modifying the original input array.
- Creates a new `XString` with the concatenated string, preserving the default grapheme mode and UTF-8 encoding.
- Time complexity: **O(n)** with `n = number of fragments + total string length`; Space complexity: **O(total string length)**.

## Important notes and considerations

- **Immutability.** `XString::new()` always returns a fresh instance; it never mutates the input data.
- **Adapters.** `Newline`, `HtmlTag`, and `Regex` adapters are string-cast before concatenation, allowing fluent pipelines.
- **Arrays are optional.** Passing a plain string is the common case, but arrays enable batching inputs that originate from
  iterative processes.
- **Mode & encoding.** The produced instance starts in grapheme mode with UTF-8 encoding. Use `withMode()` when a different
  interpretation is needed.

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$data` | `''` | `Newline\|HtmlTag\|Regex\|Stringable\|string\|array<Newline\|HtmlTag\|Regex\|Stringable\|string>` | Source material for the new instance. Arrays are concatenated in order. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Immutable `XString` containing the concatenated data. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `\InvalidArgumentException` | `$data` (or any array element) is not a `Newline`, `HtmlTag`, `Regex`, `Stringable`, or `string` value. |

## Examples

### Create from a plain string

<!-- test:xstring-new-plain -->
```php
use Orryv\XString;

$xstring = XString::new('Hello world');
#Test: self::assertInstanceOf(XString::class, $xstring);
#Test: self::assertSame('Hello world', (string) $xstring);
```

### Combine array values without mutating the source

<!-- test:xstring-new-array -->
```php
use Orryv\XString;

$parts = ['Hello', ' ', 'world', '!'];
$result = XString::new($parts);
#Test: self::assertSame('Hello world!', (string) $result);
#Test: self::assertSame(['Hello', ' ', 'world', '!'], $parts);
```


### Combine adapters, including HtmlTag

<!-- test:xstring-new-html-tag -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;

$fragments = [
    HtmlTag::new('p')->withClass(['intro', 'lead']),
    'Hello',
    Newline::new(),
    HtmlTag::closeTag('p'),
];
$result = XString::new($fragments);
#Test: self::assertSame("<p class=\"intro lead\">Hello" . PHP_EOL . "</p>", (string) $result);
```

### Combine adapters, including HtmlTag with end Tag

<!-- test:xstring-new-html-tag -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;

$fragment =HtmlTag::new('p')
      ->withClass(['intro', 'lead'])
      ->withBody('Hello')
      ->withEndTag();
$result = XString::new($fragment);
#Test: self::assertSame("<p class=\"intro lead\">Hello" . PHP_EOL . "</p>", (string) $result);
```

### Empty input defaults to an empty string

<!-- test:xstring-new-empty -->
```php
use Orryv\XString;

$xstring = XString::new();
#Test: self::assertSame('', (string) $xstring);
```

### Unsupported data in the array raises an exception

<!-- test:xstring-new-invalid -->
```php
use Orryv\XString;

#Test: $this->expectException(\InvalidArgumentException::class);
XString::new(['valid', 123]);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::new` | `public static function new(Newline\|HtmlTag\|Regex\|Stringable\|string\|array<Newline\|HtmlTag\|Regex\|Stringable\|string> $data = ''): self` — Create a new immutable `XString` instance by concatenating the provided data. |
