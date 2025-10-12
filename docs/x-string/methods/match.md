# XString::match()

## Table of Contents
- [XString::match()](#xstringmatch)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Match a ticket identifier](#match-a-ticket-identifier)
    - [No match returns null](#no-match-returns-null)
    - [Try multiple patterns sequentially](#try-multiple-patterns-sequentially)
    - [Empty pattern arrays are rejected](#empty-pattern-arrays-are-rejected)
    - [All entries must be Regex instances](#all-entries-must-be-regex-instances)
    - [Invalid regex surfaces as ValueError](#invalid-regex-surfaces-as-valueerror)
    - [Original instance remains unchanged](#original-instance-remains-unchanged)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function match(Regex|array<Regex> $pattern): array<int, array<int|string, string>> | null
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✗ | Public |

## Description

Executes one or more regular expressions against the current string and returns **every** match that is found. Provide either a single `Regex` instance or an array of them; each pattern is evaluated independently and all matches are returned in discovery order. Each match contains the full substring plus any captured groups (numeric and named).

## Important notes and considerations

- **All matches for every pattern.** All provided patterns are evaluated. Matches from later patterns are appended to the result instead of stopping after the first success.
- **Immutable source.** Even though an array is returned, the underlying string is not modified.
- **Regex validation.** Invalid patterns throw `ValueError`, mirroring PHP's native behaviour.

## Parameters

| Name | Type | Description |
| --- | --- | --- |
| `$pattern` | `Regex\|array<Regex>` | A single regex or ordered list of regex patterns to evaluate. |

## Returns

| Return Type | Description |
| --- | --- |
| `array<int, array<int|string, string>>` | Every match found, with the full match plus captured groups. Each inner array mirrors the structure returned by `preg_match_all()` with numeric and named keys preserved. |
| `null` | Returned when no pattern matches. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | The pattern array is empty or contains non-`Regex` entries. |
| `ValueError` | A provided regular expression is invalid. |

## Examples

### Match numbered references

<!-- test:match-basic -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$message = XString::new('Tickets #4321 resolved, #99 reopened');
$matches = $message->match(Regex::new('/#(?P<id>\d+)/'));

#Test: self::assertCount(2, $matches);
#Test: self::assertSame('#4321', $matches[0][0]);
#Test: self::assertSame('4321', $matches[0]['id']);
#Test: self::assertSame('#99', $matches[1][0]);
#Test: self::assertSame('99', $matches[1]['id']);
```

### No match returns null

<!-- test:match-no-result -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$result = XString::new('No numbers here')->match(Regex::new('/\d+/'));

#Test: self::assertNull($result);
```

### Combine multiple patterns

<!-- test:match-multiple-patterns -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$value = XString::new('v2.5.0-beta.3');
$patterns = [
    Regex::new('/^v(?P<major>\d+)/'),
    Regex::new('/\.(?P<section>\d+)/'),
];

$matches = $value->match($patterns);

#Test: self::assertCount(4, $matches);
#Test: self::assertSame('2', $matches[0]['major']);
#Test: self::assertSame('.5', $matches[1][0]);
#Test: self::assertSame('5', $matches[1]['section']);
#Test: self::assertSame('.0', $matches[2][0]);
#Test: self::assertSame('.3', $matches[3][0]);
#Test: self::assertSame('3', $matches[3]['section']);
```

### Empty pattern arrays are rejected

<!-- test:match-empty-array -->
```php
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\Regex;

$value = XString::new('content');

#Test: $this->expectException(InvalidArgumentException::class);
$value->match([]);
```

### All entries must be Regex instances

<!-- test:match-invalid-entry -->
```php
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\Regex;

$value = XString::new('content');
$patterns = [Regex::new('/foo/'), 'bar'];

#Test: $this->expectException(InvalidArgumentException::class);
$value->match($patterns);
```

### Invalid regex surfaces as ValueError

<!-- test:match-invalid-regex -->
```php
use Orryv\XString;
use Orryv\XString\Regex;
use ValueError;

$value = XString::new('content');

#Test: $this->expectException(ValueError::class);
$value->match(Regex::new('/(unclosed/'));
```

### Original instance remains unchanged

<!-- test:match-immutable -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$value = XString::new('Order #77 processed');
$value->match(Regex::new('/#(\d+)/'));

#Test: self::assertSame('Order #77 processed', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::match` | `public function match(Regex|array<Regex> $pattern): array<int, array<int|string, string>> \| null` — Return every regex match found (including named captures) or `null` when nothing matches. |
