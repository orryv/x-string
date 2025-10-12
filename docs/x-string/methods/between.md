# XString::between()

## Table of Contents
- [XString::between()](#xstringbetween)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Extract text between brackets](#extract-text-between-brackets)
    - [Skip leading occurrences](#skip-leading-occurrences)
    - [Skip closing delimiters after the start](#skip-closing-delimiters-after-the-start)
    - [Traverse from the end of the string](#traverse-from-the-end-of-the-string)
    - [Use multi-step start and end sequences](#use-multi-step-start-and-end-sequences)
    - [Use mixed delimiter types sequentially](#use-mixed-delimiter-types-sequentially)
    - [Allow alternative delimiters](#allow-alternative-delimiters)
    - [Match mixed delimiter types with OR behavior](#match-mixed-delimiter-types-with-or-behavior)
    - [Missing delimiters return an empty string](#missing-delimiters-return-an-empty-string)
    - [Reject negative skip counts](#reject-negative-skip-counts)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function between(Newline|HtmlTag|Regex|string|array $start, Newline|HtmlTag|Regex|string|array $end, $last_occurence = false, int $skip_start = 0, int $skip_end = 0, string $start_behavior = 'sequential', string $end_behavior = 'sequential'): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Returns the substring located between two delimiters. You can search forwards or backwards, skip a number of opening and
closing delimiters, and choose how arrays are interpreted: treat them as ordered sequences (default) or as independent
alternatives using the `$start_behavior` and `$end_behavior` flags. A fresh `XString` instance is returned, preserving the
original mode and encoding.

## Important notes and considerations

- **Flexible delimiters.** Supply an array of fragments and control how it is interpreted via the behavior flags. The default
  `'sequential'` mode requires fragments to appear in order (`['<article>', '<section>']`). Switch to `'or'` to treat each entry
  as a standalone option.
- **Directional searches.** Set `$last_occurence` to `true` to work from the end of the string. `$skip_start` counts start delimiters
  from the end, while `$skip_end` still counts occurrences forward after the selected start.
- **Graceful fallbacks.** If the requested start or end delimiters are not found, an empty string is returned.
- **Immutable.** The source instance is never modified.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$start` | `Newline\|HtmlTag\|Regex\|string\|array` | — | Opening delimiter(s) to locate. |
| `$end` | `Newline\|HtmlTag\|Regex\|string\|array` | — | Closing delimiter(s) to locate. |
| `$last_occurence` | `bool` | `false` | Search from the end of the string instead of the beginning. |
| `$skip_start` | `int` | `0` | Number of start occurrences to skip before selecting one. |
| `$skip_end` | `int` | `0` | Number of end occurrences to skip (after the start has been chosen). |
| `$start_behavior` | `'sequential'\|'or'` | `'sequential'` | How to interpret `$start` arrays. `'sequential'` matches fragments in order, `'or'` treats every entry as a candidate. |
| `$end_behavior` | `'sequential'\|'or'` | `'sequential'` | How to interpret `$end` arrays. `'sequential'` matches fragments in order, `'or'` treats every entry as a candidate. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Substring between the chosen start and end delimiters. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | Thrown when `$skip_start` or `$skip_end` is negative, or when a delimiter fragment is empty. |

## Examples

### Extract text between brackets

<!-- test:between-basic -->
```php
use Orryv\XString;

$text = XString::new('Hello [World] Example');
$result = $text->between('[', ']');
#Test: self::assertSame('World', (string) $result);
#Test: self::assertSame('Hello [World] Example', (string) $text);
```

### Skip leading occurrences

<!-- test:between-skip-start -->
```php
use Orryv\XString;

$template = XString::new('{{first}} {{second}} {{third}}');
$result = $template->between('{{', '}}', skip_start: 1);
#Test: self::assertSame('second', (string) $result);
```

### Skip closing delimiters after the start

<!-- test:between-skip-end -->
```php
use Orryv\XString;

$text = XString::new('[first|inner|final] tail');
$result = $text->between('[', '|', skip_end: 1);
#Test: self::assertSame('first|inner', (string) $result);
```

### Traverse from the end of the string

<!-- test:between-reversed -->
```php
use Orryv\XString;

$text = XString::new('Start [first] Middle [second] End');
$result = $text->between('[', ']', last_occurence: true);
#Test: self::assertSame('second', (string) $result);
```

### Use multi-step start and end sequences

<!-- test:between-sequences -->
```php
use Orryv\XString;

$html = XString::new('<article><section><p>Body</p></section></article>');
$result = $html->between(['<article>', '<section>', '<p>'], ['</p>', '</section>']);
#Test: self::assertSame('Body', (string) $result);
```

### Use mixed delimiter types sequentially

<!-- test:between-mixed-sequential -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;

$text = XString::new("<section>\nID: 42\n</section>");
$result = $text->between(
    [HtmlTag::new('section'), Newline::new("\n"), 'ID: '],
    [Newline::new("\n"), HtmlTag::closeTag('section')]
);

#Test: self::assertSame('42', (string) $result);
```

### Allow alternative delimiters

<!-- test:between-or-behavior -->
```php
use Orryv\XString;

$text = XString::new('<title>Hello</title> {World}');
$result = $text->between(['<title>', '{'], ['</title>', '}'], start_behavior: 'or', end_behavior: 'or');
#Test: self::assertSame('Hello', (string) $result);
```

### Match mixed delimiter types with OR behavior

<!-- test:between-mixed-or -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;
use Orryv\XString\Regex;

$text = XString::new("<value>100</value>\n{200}\n<result>300</result>\n");
$result = $text->between(
    [
        HtmlTag::new('value'),
        '{',
        [Newline::new("\n"), '<result>'],
    ],
    [
        HtmlTag::closeTag('value'),
        '}',
        [Regex::new('</result>'), Newline::new("\n")],
    ],
    start_behavior: 'or',
    end_behavior: 'or'
);

#Test: self::assertSame('100', (string) $result);
```

### Missing delimiters return an empty string

<!-- test:between-missing -->
```php
use Orryv\XString;

$text = XString::new('No brackets here');
$result = $text->between('[', ']');
#Test: self::assertSame('', (string) $result);
```

### Reject negative skip counts

<!-- test:between-invalid-skip -->
```php
use Orryv\XString;
use InvalidArgumentException;

$text = XString::new('Example content');
#Test: $this->expectException(InvalidArgumentException::class);
$text->between('[', ']', skip_start: -1);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::between` | `public function between(Newline\|HtmlTag\|Regex\|string\|array $start, Newline\|HtmlTag\|Regex\|string\|array $end, $last_occurence = false, int $skip_start = 0, int $skip_end = 0, string $start_behavior = 'sequential', string $end_behavior = 'sequential'): self` — Return the substring between opening and closing delimiters with support for directional searches, skip counters, and configurable delimiter behaviors. |
