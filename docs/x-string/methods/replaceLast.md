# XString::replaceLast()

## Table of Contents
- [XString::replaceLast()](#xstringreplacelast)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Replace the last occurrence of a substring](#replace-the-last-occurrence-of-a-substring)
    - [Replace the last match from a list of candidates](#replace-the-last-match-from-a-list-of-candidates)
    - [Original instance remains unchanged](#original-instance-remains-unchanged)
    - [No replacement when nothing matches](#no-replacement-when-nothing-matches)
    - [Empty search value throws an exception](#empty-search-value-throws-an-exception)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function replaceLast(
    HtmlTag|Newline|Regex|Stringable|string|array $search,
    HtmlTag|Newline|Regex|Stringable|string $replace
): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Returns a new immutable `XString` where only the final occurrence of the provided search value is replaced. The method accepts
any combination of `string`, [`Newline`](../../newline/methods/new.md), [`Regex`](../../regex/methods/new.md),
[`HtmlTag`](../../html-tag/methods/new.md), or `Stringable` values; arrays behave as an OR list processed in order. Internally,
it leverages [`replace()`](./replace.md) with a limit of one and reversed search direction, preserving the instance's mode and
encoding.

## Important notes and considerations

- **Single replacement.** Only the final match is replaced, even when the value appears multiple times.
- **Supports adapters.** Works with `Newline`, `Regex`, and `HtmlTag` helpers just like `replace()`.
- **Immutable clone.** The original string is not modified; a new instance is returned.
- **Array handling.** When `$search` is an array, each entry is tried in sequence until one performs the last-match replacement.

## Parameters

| Name | Type | Description |
| --- | --- | --- |
| `$search` | `HtmlTag\|Newline\|Regex\|Stringable\|string\|array` | Value(s) to match. Arrays are evaluated left-to-right. |
| `$replace` | `HtmlTag\|Newline\|Regex\|Stringable\|string` | Replacement fragment applied to the last match. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` containing the updated value with the final match replaced. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | Thrown when a normalized search value is empty. |

## Examples

### Replace the last occurrence of a substring

<!-- test:replace-last-basic -->
```php
use Orryv\XString;

$xstring = XString::new('one two one two');
$result = $xstring->replaceLast('one', '1');

#Test: self::assertSame('one two 1 two', (string) $result);
#Test: self::assertSame('one two one two', (string) $xstring);
```

### Replace the last match from a list of candidates

<!-- test:replace-last-array -->
```php
use Orryv\XString;

$xstring = XString::new('alpha beta gamma beta alpha');
$result = $xstring->replaceLast(['alpha', 'beta'], 'X');

#Test: self::assertSame('alpha beta gamma beta X', (string) $result);
```

### Original instance remains unchanged

<!-- test:replace-last-immutability -->
```php
use Orryv\XString;

$xstring = XString::new('repeat repeat repeat');
$updated = $xstring->replaceLast('repeat', 'done');

#Test: self::assertSame('repeat repeat repeat', (string) $xstring);
#Test: self::assertSame('repeat repeat done', (string) $updated);
```

### No replacement when nothing matches

<!-- test:replace-last-no-match -->
```php
use Orryv\XString;

$xstring = XString::new('hello world');
$result = $xstring->replaceLast('absent', 'x');

#Test: self::assertSame('hello world', (string) $result);
```

### Empty search value throws an exception

<!-- test:replace-last-empty-search -->
```php
use InvalidArgumentException;
use Orryv\XString;

$xstring = XString::new('example');

#Test: $this->expectException(InvalidArgumentException::class);
$xstring->replaceLast('', 'test');
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::replaceLast` | `public function replaceLast(HtmlTag|Newline|Regex|Stringable|string|array $search, HtmlTag|Newline|Regex|Stringable|string $replace): self` — Replace only the final occurrence of the provided search value. |
