# XString::after()

## Table of Contents
- [XString::after()](#xstringafter)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Extract the domain from an email](#extract-the-domain-from-an-email)
    - [Skip earlier delimiters](#skip-earlier-delimiters)
    - [Search from the end of the string](#search-from-the-end-of-the-string)
    - [Return the original string when missing](#return-the-original-string-when-missing)
    - [Immutability check](#immutability-check)
    - [Reject negative skip values](#reject-negative-skip-values)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function after(Newline|HtmlTag|Regex|string|array $search, $last_occurence = false, int $skip = 0): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Returns a new `XString` containing the substring that appears after the specified delimiter. Supports forward and reverse
searching, skipping occurrences, and resolving compound delimiters through arrays. Provide arrays to offer alternative
delimiters, and nest them when fragments must appear sequentially.

## Important notes and considerations

- **Directional searches.** When `$last_occurence` is `true`, the search begins from the end of the string and `$skip` counts from the
  end as well.
- **Array delimiters.** Provide arrays of scalars to treat them as OR delimiters. Wrap fragments inside their own arrays when
  multiple pieces must be matched consecutively.
- **Graceful fallback.** If the delimiter is absent, the original string is returned.
- **Immutable.** The source instance remains unchanged.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$search` | `Newline\|HtmlTag\|Regex\|string\|array` | — | Delimiter(s) to search for. Arrays act as OR delimiters; nest arrays to enforce sequences. |
| `$last_occurence` | `bool` | `false` | Search from the end of the string instead of the beginning. |
| `$skip` | `int` | `0` | Number of delimiter occurrences to skip before selecting one. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Substring following the located delimiter (or the original value if no match is found). |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | Thrown when `$skip` is negative or when a delimiter fragment is empty. |

## Examples

### Extract the domain from an email

<!-- test:after-email -->
```php
use Orryv\XString;

$email = XString::new('user@example.com');
$result = $email->after('@');
#Test: self::assertSame('example.com', (string) $result);
#Test: self::assertSame('user@example.com', (string) $email);
```

### Skip earlier delimiters

<!-- test:after-skip -->
```php
use Orryv\XString;

$path = XString::new('one/two/three/four');
$result = $path->after('/', skip: 1);
#Test: self::assertSame('three/four', (string) $result);
```

### Search from the end of the string

<!-- test:after-reversed -->
```php
use Orryv\XString;

$path = XString::new('path/to/file.txt');
$result = $path->after('/', last_occurence: true);
#Test: self::assertSame('file.txt', (string) $result);
```

### Return the original string when missing

<!-- test:after-missing -->
```php
use Orryv\XString;

$text = XString::new('no delimiter');
$result = $text->after('|');
#Test: self::assertSame('no delimiter', (string) $result);
```

### Immutability check

<!-- test:after-immutability -->
```php
use Orryv\XString;

$value = XString::new('abc-def');
$after = $value->after('-');
#Test: self::assertSame('abc-def', (string) $value);
#Test: self::assertSame('def', (string) $after);
```

### Reject negative skip values

<!-- test:after-invalid-skip -->
```php
use Orryv\XString;
use InvalidArgumentException;

$value = XString::new('example');
#Test: $this->expectException(InvalidArgumentException::class);
$value->after('e', skip: -1);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::after` | `public function after(Newline\|HtmlTag\|Regex\|string\|array $search, $last_occurence = false, int $skip = 0): self` — Return the substring after a chosen delimiter with optional reverse traversal and skip support. |
