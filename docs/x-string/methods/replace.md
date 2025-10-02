# XString::replace()

<!-- skip-url-warning -->[bla](codex/bla.md)

## Table of Contents
- [XString::replace()](#xstringreplace)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Replace all occurrences](#replace-all-occurrences)
    - [Limit the number of replacements](#limit-the-number-of-replacements)
    - [Replace from the end of the string](#replace-from-the-end-of-the-string)
    - [Replace multiple search strings at once](#replace-multiple-search-strings-at-once)
    - [Immutability check](#immutability-check)
    - [No replacements when limit is zero](#no-replacements-when-limit-is-zero)
    - [Invalid limit throws an exception](#invalid-limit-throws-an-exception)
    - [Empty search value throws an exception](#empty-search-value-throws-an-exception)
    - [Replace with html tag](#replace-with-html-tag)
    - [Replace with Newline](#replace-with-newline)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function replace(
    HtmlTag|Newline|Regex|Stringable|string|array $search,
    HtmlTag|Newline|Regex|Stringable|string $replace,
    ?int $limit = null,
    bool $reversed = false
): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Returns a new immutable `XString` with occurrences of the supplied search string(s) replaced by a new fragment. The method
accepts individual `string`, [`Newline`](../../newline/methods/new.md), [`Regex`](../../regex/methods/new.md),
[`HtmlTag`](../../html-tag/methods/new.md), or any `Stringable` value, as well as arrays containing a combination of these.
Use the optional `$limit` argument to control how many replacements are performed and the `$reversed` flag to begin
replacing from the end of the string.

## Important notes and considerations

- **Immutability.** A new `XString` is returned; the original remains unchanged.
- **Multiple search values.** Provide an array of search fragments to replace each one sequentially using the same
  replacement string.
- **Limit & direction.** `$limit` restricts the total number of replacements across all search values. Set `$reversed`
  to `true` to replace occurrences starting from the end of the string.
- **Stringable support.** All search and replacement values are normalized via `__toString()` when available.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$search` | `HtmlTag\|Newline\|Regex\|Stringable\|string\|array` | — | The value or values to search for. Arrays are processed in the provided order. |
| `$replace` | `HtmlTag\|Newline\|Regex\|Stringable\|string` | — | Replacement fragment applied to every match. |
| `$limit` | `?int` | `null` | Maximum number of replacements. `null` means no limit. Must be `>= 0`. |
| `$reversed` | `bool` | `false` | When `true`, replacements occur starting from the end of the string. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` containing the replaced value with identical mode/encoding. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | Thrown when `$limit` is negative or a search value normalizes to an empty string. |

## Examples

### Replace all occurrences

<!-- test:replace-all -->
```php
use Orryv\XString;

$xstring = XString::new('lorem ipsum lorem ipsum');
$result = $xstring->replace('lorem', 'dolor');
#Test: self::assertSame('dolor ipsum dolor ipsum', (string) $result);
```

### Limit the number of replacements

<!-- test:replace-limit -->
```php
use Orryv\XString;

$xstring = XString::new('aaa aaa aaa');
$result = $xstring->replace('aaa', 'bbb', limit: 2);
#Test: self::assertSame('bbb bbb aaa', (string) $result);
```

### Replace from the end of the string

<!-- test:replace-reversed -->
```php
use Orryv\XString;

$xstring = XString::new('2024-05-01 2024-06-01 2024-07-01');
$result = $xstring->replace('2024', '2025', limit: 2, reversed: true);
#Test: self::assertSame('2024-05-01 2025-06-01 2025-07-01', (string) $result);
```

### Replace multiple search strings at once

<!-- test:replace-multiple-search -->
```php
use Orryv\XString;

$xstring = XString::new('red green blue red');
$result = $xstring->replace(['red', 'blue'], 'X');
#Test: self::assertSame('X green X X', (string) $result);
```

### Immutability check

<!-- test:replace-immutability -->
```php
use Orryv\XString;

$xstring = XString::new('alpha beta gamma');
$replaced = $xstring->replace('beta', 'delta');
#Test: self::assertSame('alpha beta gamma', (string) $xstring);
#Test: self::assertSame('alpha delta gamma', (string) $replaced);
```

### No replacements when limit is zero

<!-- test:replace-zero-limit -->
```php
use Orryv\XString;

$xstring = XString::new('unchanged text');
$result = $xstring->replace('text', 'content', limit: 0);
#Test: self::assertSame('unchanged text', (string) $result);
```

### Invalid limit throws an exception

<!-- test:replace-invalid-limit -->
```php
use InvalidArgumentException;
use Orryv\XString;

$xstring = XString::new('example');
#Test: $this->expectException(InvalidArgumentException::class);
$xstring->replace('example', 'test', limit: -1);
```

### Empty search value throws an exception

<!-- test:replace-empty-search -->
```php
use InvalidArgumentException;
use Orryv\XString;

$xstring = XString::new('example');
#Test: $this->expectException(InvalidArgumentException::class);
$xstring->replace('', 'test');
```

### Replace with html tag

<!-- test:replace-html-tag -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;

$xstring = XString::new('<span id="span1" class="this is a span">World!</span>')
    ->replace(
        HtmlTag::new('span')->withClass('a', 'is'), // matches tags containing the listed classes
        'Hello '
    );

#Test: self::assertSame('Hello World!</span>', (string) $xstring);

$xstring = $xstring->replace(
    HtmlTag::endTag('span'),
    ''
);
#Test: self::assertSame('Hello World!', (string) $xstring);
```

### Replace with Newline

<!-- test:replace-newline -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$xstring = XString::new(" Line1 - blabla\nHello, World!")
    ->replace(
        Newline::new()->startsWith('Line1', trim: true), // matches an entire line starting with "Line1" (ignoring indentation)
        'Welcome!'
    );

#Test: self::assertSame("Welcome!\nHello, World!", (string) $xstring);


$xstring = XString::new("Line0\n Line1 - blabla\nHello, World!")
    ->replace(
        Newline::new()->startsWith('Line1', trim: true), // replaces each line that begins with the given prefix
        'Welcome!'
    );

#Test: self::assertSame("Line0\nWelcome!\nHello, World!", (string) $xstring);
```


## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::replace` | `public function replace(HtmlTag\|Newline\|Regex\|Stringable\|string\|array $search, HtmlTag\|Newline\|Regex\|Stringable\|string $replace, ?int $limit = null, bool $reversed = false): self` — Replace occurrences with optional limit and reversed search order. |
