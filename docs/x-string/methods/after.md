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
    - [Use mixed delimiter types sequentially](#use-mixed-delimiter-types-sequentially)
    - [Choose between sequential and OR behavior](#choose-between-sequential-and-or-behavior)
    - [Reject negative skip values](#reject-negative-skip-values)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function after(Newline|HtmlTag|Regex|string|array $search, $last_occurence = false, int $skip = 0, string $start_behavior = 'sequential'): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Returns a new `XString` containing the substring that appears after the specified delimiter. Supports forward and reverse
searching, skipping occurrences, and resolving compound delimiters through arrays. Provide arrays and choose whether they
should be interpreted sequentially (default) or as independent alternatives via the `$start_behavior` flag.

## Important notes and considerations

- **Directional searches.** When `$last_occurence` is `true`, the search begins from the end of the string and `$skip` counts from the
  end as well.
- **Array delimiters.** Provide arrays of scalars and pick `'sequential'` (match fragments in order) or `'or'` (treat each entry as an alternative) to suit your use case.
- **Graceful fallback.** If the delimiter is absent, the original string is returned.
- **Immutable.** The source instance remains unchanged.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$search` | `Newline\|HtmlTag\|Regex\|string\|array` | — | Delimiter(s) to search for. |
| `$last_occurence` | `bool` | `false` | Search from the end of the string instead of the beginning. |
| `$skip` | `int` | `0` | Number of delimiter occurrences to skip before selecting one. |
| `$start_behavior` | `'sequential'\|'or'` | `'sequential'` | How arrays passed to `$search` should be matched. Sequential mode enforces ordering; OR mode accepts any fragment. |

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

### Use mixed delimiter types sequentially

<!-- test:after-mixed-sequential -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;
use Orryv\XString\Regex;

$text = XString::new("<header>\nTitle: Report</header>\nSummary");
$result = $text->after([
    HtmlTag::new('header'),
    Newline::new("\n"),
    'Title: ',
    Regex::new('Report'),
    HtmlTag::closeTag('header'),
    Newline::new("\n"),
]);

#Test: self::assertSame('Summary', (string) $result);
```

### Choose between sequential and OR behavior

<!-- test:after-or-behavior -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;
use Orryv\XString\Regex;

$value = XString::new("<note>Alpha</note>\n{Beta}\nResult: Gamma");
$sequential = $value->after([HtmlTag::new('note'), Regex::new('</note>')]);
$mixed = $value->after([
    HtmlTag::new('note'),
    Regex::new('{'),
    [Newline::new("\n"), Regex::new('Result: ')],
], skip: 1, start_behavior: 'or');
#Test: self::assertSame("\n{Beta}\nResult: Gamma", (string) $sequential);
#Test: self::assertSame('Gamma', (string) $mixed);
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
| `XString::after` | `public function after(Newline\|HtmlTag\|Regex\|string\|array $search, $last_occurence = false, int $skip = 0, string $start_behavior = 'sequential'): self` — Return the substring after a chosen delimiter with optional reverse traversal, skip support, and configurable delimiter behavior. |
