# XString::startsWith()

## Table of Contents
- [XString::startsWith()](#xstringstartswith)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Verify a simple string prefix](#verify-a-simple-string-prefix)
    - [Offer multiple candidates via arrays](#offer-multiple-candidates-via-arrays)
    - [Anchor checks with regular expressions](#anchor-checks-with-regular-expressions)
    - [Match leading HTML structures](#match-leading-html-structures)
    - [Ignore indentation with newline helpers](#ignore-indentation-with-newline-helpers)
    - [Reject empty search values](#reject-empty-search-values)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function startsWith(HtmlTag|Newline|Regex|Stringable|string|array $search): bool
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✗ | Public |

## Description

Checks whether the string begins with one of the supplied candidates. Supports plain strings, `Stringable`
objects, [`HtmlTag`](../../html-tag/methods/new.md) descriptors, [`Newline`](../../newline/methods/new.md) helpers (including
`startsWith()` configurations) and [`Regex`](../../x-string/methods/match.md#technical-details) patterns.

When an array is supplied it is treated as an OR-list; the method returns `true` as soon as any candidate matches.
The original `XString` instance is never modified.

## Important notes and considerations

- **Structured prefix matching.** `HtmlTag` and `Newline` helpers make it easy to check structured prefixes.
- **Regex anchoring.** Regular expressions are evaluated with `preg_match`; ensure they are anchored if you expect
  the pattern to begin at offset zero.
- **Array inputs are flat.** Nested arrays are not supported and raise an exception.

## Parameters

| Name | Type | Description |
| --- | --- | --- |
| `$search` | `HtmlTag\|Newline\|Regex\|Stringable\|string\|array` | One or more candidates to test at the beginning of the string. |

## Returns

| Return Type | Description |
| --- | --- |
| `bool` | `true` when the string starts with any of the candidates, `false` otherwise. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | The candidates list is empty, nested arrays were provided, or a search value normalises to an empty string. |
| `ValueError` | A provided regular expression is invalid. |

## Examples

### Verify a simple string prefix

<!-- test:starts-with-basic -->
```php
use Orryv\XString;

$title = XString::new('Framework: XString');

#Test: self::assertTrue($title->startsWith('Framework'));
#Test: self::assertFalse($title->startsWith('XString'));
#Test: self::assertSame('Framework: XString', (string) $title);
```

### Offer multiple candidates via arrays

<!-- test:starts-with-array -->
```php
use Orryv\XString;

$slug = XString::new('feature/add-index-method');

#Test: self::assertTrue($slug->startsWith(['feature/', 'hotfix/']));
#Test: self::assertFalse($slug->startsWith(['bugfix/', 'release/']));
```

### Anchor checks with regular expressions

<!-- test:starts-with-regex -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$version = XString::new('Version: 1.2.3');

#Test: self::assertTrue($version->startsWith(Regex::new('/^Version:/')));
#Test: self::assertFalse($version->startsWith(Regex::new('/^Release:/')));
```

### Match leading HTML structures

<!-- test:starts-with-html -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;

$document = XString::new('<h1 id="title">Heading</h1>');
$heading = HtmlTag::new('h1')->withId('title');

#Test: self::assertTrue($document->startsWith($heading));
#Test: self::assertFalse($document->startsWith(HtmlTag::new('section')));
```

### Ignore indentation with newline helpers

<!-- test:starts-with-newline -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$block = XString::new("    Item A\nItem B");
$lineMatcher = Newline::new()->startsWith('Item', trim: true);

#Test: self::assertTrue($block->startsWith($lineMatcher));
#Test: self::assertFalse(XString::new('    Other')->startsWith($lineMatcher));
```

### Reject empty search values

<!-- test:starts-with-empty -->
```php
use InvalidArgumentException;
use Orryv\XString;

$string = XString::new('content');

#Test: $this->expectException(InvalidArgumentException::class);
$string->startsWith('');
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::startsWith` | `public function startsWith(HtmlTag\|Newline\|Regex\|Stringable\|string\|array $search): bool` — Determine whether the string begins with any provided candidate, supporting HTML, newline helpers and regular expressions. |
