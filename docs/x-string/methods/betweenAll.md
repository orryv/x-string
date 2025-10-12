# XString::betweenAll()

## Table of Contents
- [XString::betweenAll()](#xstringbetweenall)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Collect substrings between brackets](#collect-substrings-between-brackets)
    - [Search from the end of the string](#search-from-the-end-of-the-string)
    - [Allow multiple delimiter options](#allow-multiple-delimiter-options)
    - [Match mixed delimiter types with OR behavior](#match-mixed-delimiter-types-with-or-behavior)
    - [Require ordered delimiter sequences](#require-ordered-delimiter-sequences)
    - [Use mixed delimiter types sequentially](#use-mixed-delimiter-types-sequentially)
    - [Extract values from HTML fragments](#extract-values-from-html-fragments)
    - [No matches returns an empty array](#no-matches-returns-an-empty-array)
    - [Reject empty delimiter fragments](#reject-empty-delimiter-fragments)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function betweenAll(Newline|HtmlTag|Regex|Stringable|string|array $start, Newline|HtmlTag|Regex|Stringable|string|array $end, bool $reversed = false, string $start_behavior = 'sequential', string $end_behavior = 'sequential'): array
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✗ | Public |

## Description

Finds every non-overlapping substring located between the supplied start and end delimiters and returns them as an ordered list.
You can supply strings, [`Newline`](../../newline/methods/new.md), [`HtmlTag`](../../html-tag/methods/new.md), [`Regex`](../../regex/methods/new.md),
or arrays and decide whether they must appear sequentially or act as alternatives through the behavior flags. Set
`$reversed` to `true` to return the matches in reverse order.

## Important notes and considerations

- **Alternative delimiters.** Supply arrays of scalars to control delimiter matching. `'sequential'` behavior requires fragments to appear in order (e.g. `['<article>', '<section>']`), while `'or'` treats each entry as a candidate.
- **Non-overlapping.** Once a match is produced, the search continues after the closing delimiter to avoid overlapping segments.
- **Direction control.** `$reversed` flips the order of the resulting array, making it easy to inspect trailing sections first.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$start` | `Newline\|HtmlTag\|Regex\|Stringable\|string\|array` | — | Opening delimiter(s). |
| `$end` | `Newline\|HtmlTag\|Regex\|Stringable\|string\|array` | — | Closing delimiter(s). |
| `$reversed` | `bool` | `false` | Reverse the order of the collected substrings. |
| `$start_behavior` | `'sequential'\|'or'` | `'sequential'` | How arrays passed to `$start` should be matched. Sequential mode enforces ordering; OR mode accepts any fragment. |
| `$end_behavior` | `'sequential'\|'or'` | `'sequential'` | How arrays passed to `$end` should be matched. Sequential mode enforces ordering; OR mode accepts any fragment. |

## Returns

| Return Type | Description |
| --- | --- |
| `list<string>` | All substrings located between the chosen delimiters. Empty results are returned when no matches are found. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | Any delimiter fragment normalises to an empty string, or an empty array of fragments is provided. |

## Examples

### Collect substrings between brackets

<!-- test:between-all-basic -->
```php
use Orryv\XString;

$text = XString::new('[one][two][three]');
$segments = $text->betweenAll('[', ']');

#Test: self::assertSame(['one', 'two', 'three'], $segments);
#Test: self::assertSame('[one][two][three]', (string) $text);
```

### Search from the end of the string

<!-- test:between-all-reversed -->
```php
use Orryv\XString;

$text = XString::new('<a>first</a><a>second</a><a>third</a>');
$segments = $text->betweenAll('<a>', '</a>', reversed: true);

#Test: self::assertSame(['third', 'second', 'first'], $segments);
```

### Allow multiple delimiter options

<!-- test:between-all-alternatives -->
```php
use Orryv\XString;

$text = XString::new('[one]{two}(three)');
$segments = $text->betweenAll(['[', '{', '('], [']', '}', ')'], start_behavior: 'or', end_behavior: 'or');

#Test: self::assertSame(['one', 'two', 'three'], $segments);
```

### Match mixed delimiter types with OR behavior

<!-- test:between-all-mixed-or -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;

$text = XString::new("<value>100</value>{200}\n<result>300</result>\n");
$segments = $text->betweenAll(
    [
        HtmlTag::new('value'),
        '{',
        [Newline::new("\n"), HtmlTag::new('result')],
    ],
    [
        HtmlTag::closeTag('value'),
        '}',
        [HtmlTag::closeTag('result'), Newline::new("\n")],
    ],
    start_behavior: 'or',
    end_behavior: 'or'
);

#Test: self::assertSame(['100', '200', '300'], $segments);
```

### Require ordered delimiter sequences

<!-- test:between-all-sequence -->
```php
use Orryv\XString;

$html = XString::new('<article><section><p>Alpha</p></section></article><article><section><p>Beta</p></section></article>');
$segments = $html->betweenAll(['<article>', '<section>', '<p>'], ['</p>', '</section>', '</article>']);

#Test: self::assertSame(['Alpha', 'Beta'], $segments);
```

### Use mixed delimiter types sequentially

<!-- test:between-all-mixed-sequential -->
```php
use Orryv\XString;
use Orryv\XString\Newline;
use Orryv\XString\Regex;

$text = XString::new("BEGIN\n::Alpha::\nEND\nBEGIN\n::Beta::\nEND");
$segments = $text->betweenAll(
    ['BEGIN', Newline::new("\n"), '::'],
    ['::', Newline::new("\n"), Regex::new('END')]
);

#Test: self::assertSame(['Alpha', 'Beta'], $segments);
```

### Extract values from HTML fragments

<!-- test:between-all-html -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;

$template = XString::new('<div class="note">First</div><div class="note">Second</div>');
$segments = $template->betweenAll(HtmlTag::new('div')->withClass('note'), HtmlTag::closeTag('div'));

#Test: self::assertSame(['First', 'Second'], $segments);
```

### No matches returns an empty array

<!-- test:between-all-empty -->
```php
use Orryv\XString;

$segments = XString::new('no markers here')->betweenAll('[', ']');

#Test: self::assertSame([], $segments);
```

### Reject empty delimiter fragments

<!-- test:between-all-invalid -->
```php
use InvalidArgumentException;
use Orryv\XString;

$text = XString::new('example');

#Test: $this->expectException(InvalidArgumentException::class);
$text->betweenAll(['', 'start'], 'end');
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::betweenAll` | `public function betweenAll(Newline\|HtmlTag\|Regex\|Stringable\|string\|array $start, Newline\|HtmlTag\|Regex\|Stringable\|string\|array $end, bool $reversed = false, string $start_behavior = 'sequential', string $end_behavior = 'sequential'): array` — Collect every non-overlapping substring between the supplied delimiters, optionally reversing the results and configuring delimiter behaviors. |
