# XString::before()

## Table of Contents
- [XString::before()](#xstringbefore)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Extract username from an email address](#extract-username-from-an-email-address)
    - [Skip earlier occurrences](#skip-earlier-occurrences)
    - [Search from the end of the string](#search-from-the-end-of-the-string)
    - [Graceful fallback when the delimiter is missing](#graceful-fallback-when-the-delimiter-is-missing)
    - [Immutability check](#immutability-check)
    - [Reject negative skip values](#reject-negative-skip-values)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function before(Newline|HtmlTag|Regex|string|array $search, $last_occurence = false, int $skip = 0): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Returns a new `XString` containing the substring that appears before the specified delimiter. You can skip a number of matches
or run the lookup from the end of the string. Arrays may be supplied to resolve multi-step delimiters sequentially.

## Important notes and considerations

- **Directional searches.** Passing `$last_occurence = true` searches from the end of the string. In that case `$skip` counts from the
  end as well.
- **Array delimiters.** When arrays are supplied, each fragment is resolved in sequence, allowing you to match compound markers.
- **Graceful fallback.** If the delimiter cannot be found, the original value is returned unchanged.
- **Immutable.** The original instance is never modified.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$search` | `Newline\|HtmlTag\|Regex\|string\|array` | — | Delimiter(s) to search for. Arrays are matched sequentially. |
| `$last_occurence` | `bool` | `false` | Search from the end of the string. |
| `$skip` | `int` | `0` | Number of delimiter occurrences to skip before returning a result. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Substring before the selected delimiter (or the original value when none is found). |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | Thrown when `$skip` is negative or when a delimiter fragment is empty. |

## Examples

### Extract username from an email address

<!-- test:before-email -->
```php
use Orryv\XString;

$email = XString::new('user@example.com');
$result = $email->before('@');
#Test: self::assertSame('user', (string) $result);
#Test: self::assertSame('user@example.com', (string) $email);
```

### Skip earlier occurrences

<!-- test:before-skip -->
```php
use Orryv\XString;

$path = XString::new('one/two/three/four');
$result = $path->before('/', skip: 2);
#Test: self::assertSame('one/two/three', (string) $result);
```

### Search from the end of the string

<!-- test:before-reversed -->
```php
use Orryv\XString;

$path = XString::new('path/to/file.txt');
$result = $path->before('/', last_occurence: true);
#Test: self::assertSame('path/to', (string) $result);
```

### Graceful fallback when the delimiter is missing

<!-- test:before-missing -->
```php
use Orryv\XString;

$text = XString::new('no delimiter');
$result = $text->before('|');
#Test: self::assertSame('no delimiter', (string) $result);
```

### Immutability check

<!-- test:before-immutability -->
```php
use Orryv\XString;

$value = XString::new('abc-def');
$before = $value->before('-');
#Test: self::assertSame('abc-def', (string) $value);
#Test: self::assertSame('abc', (string) $before);
```

### Reject negative skip values

<!-- test:before-invalid-skip -->
```php
use Orryv\XString;
use InvalidArgumentException;

$value = XString::new('example');
#Test: $this->expectException(InvalidArgumentException::class);
$value->before('e', skip: -1);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::before` | `public function before(Newline\|HtmlTag\|Regex\|string\|array $search, $last_occurence = false, int $skip = 0): self` — Return the substring before a chosen delimiter with optional reverse traversal and skip support. |
