# XString::indexOf()

## Table of Contents
- [XString::indexOf()](#xstringindexof)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Locate substrings in the default grapheme mode](#locate-substrings-in-the-default-grapheme-mode)
    - [Pick the earliest match from an array of candidates](#pick-the-earliest-match-from-an-array-of-candidates)
    - [Search from the end of the string](#search-from-the-end-of-the-string)
    - [Respect the active iteration mode](#respect-the-active-iteration-mode)
    - [Match structured data such as HTML tags](#match-structured-data-such-as-html-tags)
    - [Use regular expressions for flexible matching](#use-regular-expressions-for-flexible-matching)
    - [Interpret arrays sequentially](#interpret-arrays-sequentially)
    - [Combine grouped sequences with OR behaviour](#combine-grouped-sequences-with-or-behaviour)
    - [Normalise newline styles automatically](#normalise-newline-styles-automatically)
    - [Collect all matches when the limit is zero](#collect-all-matches-when-the-limit-is-zero)
    - [Limit the number of reported matches](#limit-the-number-of-reported-matches)
    - [Reject empty search values](#reject-empty-search-values)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function indexOf(
    HtmlTag|Newline|Regex|Stringable|string|array $search,
    bool $reversed = false,
    int|bool $limit = 1,
    string $behavior = 'or'
): false|int|array<int, int>
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✗ | Public |

## Description

Returns the index of the first occurrence of one or more search candidates. All search inputs
(strings, `Stringable` objects, [`HtmlTag`](../../html-tag/methods/new.md), [`Newline`](../../newline/methods/new.md)
helpers and [`Regex`](../../x-string/methods/match.md#technical-details) patterns) are supported.

By default the lookup is performed from the start of the string. When `$reversed` is `true` the lookup is
performed from the end instead, returning the highest index found. When an array of candidates is supplied it
is treated as an OR-list — the first match (according to the search direction) wins. Switch `$behavior` to
`'sequential'` when you want an array to be interpreted as an ordered chain of fragments instead of individual
alternatives. Sequential matches report the index of the **last** fragment in the chain so you can align the
result with the final element that satisfied the sequence.

Control how many results are returned with `$limit`: keep the default `1` to fetch a single index, pass `0` or
`false` to collect every match (as an ordered array), or provide an integer greater than `1` to retrieve up to
that many positions.

Indexes are reported in the current iteration mode (`graphemes`, `codepoints` or `bytes`), so the same substring can yield
different values depending on how the instance was created (see [`withMode`](withMode.md)).

## Important notes and considerations

- **Immutable behaviour.** Calling `indexOf()` never mutates the original `XString` instance.
- **Mode-aware indexes.** Results honour the instance's iteration mode; use `asBytes()`, `asCodepoints()` or
  `asGraphemes()` when you need a specific representation.
- **Flexible limits.** Set `$limit` to `0`/`false` to gather every match or to an integer `> 1` to fetch that many
  positions. Returned arrays are ordered according to the search direction.
- **Structured inputs.** `HtmlTag`, `Regex` and `Newline` helpers are matched using the same semantics as other search APIs.
- **Configurable behaviour.** Keep `$behavior` set to `'or'` to treat each candidate (or nested array of fragments) as an alternative, or switch to `'sequential'` to require every fragment to appear in order and receive the index of the chain's final fragment.
- **Array handling.** Nested arrays represent grouped fragments when using `'or'`; deeper levels of nesting remain unsupported and will raise an exception.

## Parameters

| Name | Type | Description |
| --- | --- | --- |
| `$search` | `HtmlTag\|Newline\|Regex\|Stringable\|string\|array` | One or more candidates to locate. |
| `$reversed` | `bool` | Search from the end of the string when `true`. |
| `$limit` | `int\|bool` | `1` returns a single index. `0`/`false` returns all matches. Integers `> 1` return that many matches. |
| `$behavior` | `string` | `'or'` (default) treats each candidate as an alternative. `'sequential'` requires fragments to appear in order. |

## Returns

| Return Type | Description |
| --- | --- |
| `false\|int\|array<int, int>` | Depending on `$limit`: an index, an ordered list of indexes, or `false` when nothing matched. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | The candidates list is empty, nested arrays are provided, a search value normalises to an empty string, `$limit` is invalid, or `$behavior` is not recognised. |
| `ValueError` | A provided regular expression is invalid. |

## Examples

### Locate substrings in the default grapheme mode

<!-- test:index-of-basic -->
```php
use Orryv\XString;

$headline = XString::new('Welcome to XString, an immutable helper');

#Test: self::assertSame(11, $headline->indexOf('XString'));
#Test: self::assertFalse($headline->indexOf('missing'));
#Test: self::assertSame('Welcome to XString, an immutable helper', (string) $headline);
```

### Pick the earliest match from an array of candidates

<!-- test:index-of-array -->
```php
use Orryv\XString;

$palette = XString::new('Palette: red, green, blue');

#Test: self::assertSame(9, $palette->indexOf(['green', 'red']));
#Test: self::assertSame(9, $palette->indexOf(['red', 'blue']));
```

### Search from the end of the string

<!-- test:index-of-reversed -->
```php
use Orryv\XString;

$phrase = XString::new('repeat repeat once');

#Test: self::assertSame(7, $phrase->indexOf('repeat', reversed: true));
#Test: self::assertSame(0, $phrase->indexOf('repeat'));
```

### Respect the active iteration mode

<!-- test:index-of-modes -->
```php
use Orryv\XString;

$sequence = XString::new("A👩‍🚀B");

#Test: self::assertSame(2, $sequence->indexOf('B'));
#Test: self::assertSame(4, $sequence->asCodepoints()->indexOf('B'));
#Test: self::assertSame(12, $sequence->asBytes()->indexOf('B'));
```

### Match structured data such as HTML tags

<!-- test:index-of-html -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;

$markup = XString::new('<article><section class="intro">Welcome</section></article>');

$section = HtmlTag::new('section')->withClass('intro');

#Test: self::assertSame(9, $markup->indexOf($section));
#Test: self::assertFalse($markup->indexOf(HtmlTag::new('aside')));
```

### Use regular expressions for flexible matching

<!-- test:index-of-regex -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$ticket = XString::new('Ticket #123 is open');

#Test: self::assertSame(7, $ticket->indexOf(Regex::new('/#[0-9]+/')));
#Test: self::assertFalse($ticket->indexOf(Regex::new('/#999/')));
```

### Interpret arrays sequentially

<!-- test:index-of-sequential -->
```php
use Orryv\XString;

$workflow = XString::new('alpha beta gamma delta');

#Test: self::assertSame(6, $workflow->indexOf(['alpha', 'beta'], behavior: 'sequential'));
#Test: self::assertSame(17, $workflow->indexOf(['gamma', 'delta'], behavior: 'sequential'));
#Test: self::assertFalse($workflow->indexOf(['beta', 'alpha'], behavior: 'sequential'));
```

### Combine grouped sequences with OR behaviour

<!-- test:index-of-or-groups -->
```php
use Orryv\XString;

$workflow = XString::new('alpha beta gamma delta');

#Test: self::assertSame(0, $workflow->indexOf([['alpha', 'beta'], ['gamma', 'delta']], behavior: 'or'));
#Test: self::assertSame([0, 11], $workflow->indexOf([['alpha', 'beta'], ['gamma', 'delta']], behavior: 'or', limit: 0));
```

### Normalise newline styles automatically

<!-- test:index-of-newline -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$log = XString::new("First line\nSecond line");

#Test: self::assertSame(10, $log->indexOf(Newline::new("\r\n")));
#Test: self::assertSame(10, $log->indexOf(Newline::new("\n")));
```

### Collect all matches when the limit is zero

<!-- test:index-of-limit-zero -->
```php
use Orryv\XString;

$record = XString::new('aba bab');

#Test: self::assertSame([0, 5], $record->indexOf('ab', limit: 0));
#Test: self::assertSame([5, 0], $record->indexOf('ab', reversed: true, limit: 0));
```

### Limit the number of reported matches

<!-- test:index-of-limit-count -->
```php
use Orryv\XString;

$report = XString::new('one two one two one');

#Test: self::assertSame([0, 8], $report->indexOf('one', limit: 2));
#Test: self::assertSame([16, 8], $report->indexOf('one', reversed: true, limit: 2));
```

### Reject empty search values

<!-- test:index-of-empty -->
```php
use InvalidArgumentException;
use Orryv\XString;

$text = XString::new('content');

#Test: $this->expectException(InvalidArgumentException::class);
$text->indexOf('');
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::indexOf` | `public function indexOf(HtmlTag\|Newline\|Regex\|Stringable\|string\|array $search, bool $reversed = false, int\|bool $limit = 1): false\|int\|array<int, int>` — Locate one or more occurrences of a candidate, honouring the current iteration mode and optional result limits. |
