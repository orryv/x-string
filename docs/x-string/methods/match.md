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
    - [Capture the earliest ticket identifier](#capture-the-earliest-ticket-identifier)
    - [Respect the offset parameter](#respect-the-offset-parameter)
    - [All patterns are evaluated](#all-patterns-are-evaluated)
    - [No match returns null](#no-match-returns-null)
    - [Negative offsets are rejected](#negative-offsets-are-rejected)
    - [Empty pattern arrays are rejected](#empty-pattern-arrays-are-rejected)
    - [All entries must be Regex instances](#all-entries-must-be-regex-instances)
    - [Invalid regex surfaces as ValueError](#invalid-regex-surfaces-as-valueerror)
    - [Original instance remains unchanged](#original-instance-remains-unchanged)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function match(Regex|array<Regex> $pattern, int $offset = 0): ?self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Executes one or more regular expressions against the current string and returns the match whose starting position appears first
in the original value. Provide a single `Regex` instance or an array; every pattern is evaluated and the earliest match is
converted into a fresh `XString`. The optional `$offset` lets you skip ahead in the string before matching.

## Important notes and considerations

- **Earliest position wins.** All supplied patterns are evaluated. The match with the lowest byte offset is returned, even if it
  comes from a later pattern in the list.
- **Offset-aware.** `$offset` is forwarded to `preg_match()`, letting you start the search after an initial segment.
- **Immutable result.** A new `XString` instance is created for the matched substring while the original value remains
  untouched.
- **Regex validation.** Invalid patterns throw `ValueError`, mirroring PHP's native behaviour.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$pattern` | `Regex\|array<Regex>` | — | A single regex or ordered list of regex patterns to evaluate. |
| `$offset` | `int` | `0` | Start position (in bytes) inside the string when searching. Must be `>= 0`. |

## Returns

| Return Type | Description |
| --- | --- |
| `self` | Fresh `XString` instance containing the matched substring that starts earliest in the string. |
| `null` | Returned when no pattern matches. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | The pattern array is empty, contains non-`Regex` entries, or `$offset` is negative. |
| `ValueError` | A provided regular expression is invalid. |

## Examples

### Capture the earliest ticket identifier

<!-- test:match-first-ticket -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$message = XString::new('Tickets #4321 resolved, #99 reopened');
$match = $message->match(Regex::new('/#\d+/'));

#Test: self::assertInstanceOf(XString::class, $match);
#Test: self::assertSame('#4321', (string) $match);
```

### Respect the offset parameter

<!-- test:match-offset -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$value = XString::new('ID: #12, ID: #45');
$match = $value->match(Regex::new('/#\d+/'), offset: 7);

#Test: self::assertSame('#45', (string) $match);
```

### All patterns are evaluated

<!-- test:match-lowest-position -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$value = XString::new('Release v2.5.0-beta');
$patterns = [
    Regex::new('/beta/'),
    Regex::new('/v\d+/'),
    Regex::new('/\.\d/'),
];

$match = $value->match($patterns);

#Test: self::assertSame('v2', (string) $match);
```

### No match returns null

<!-- test:match-no-result -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$result = XString::new('No numbers here')->match(Regex::new('/\d+/'));

#Test: self::assertNull($result);
```

### Negative offsets are rejected

<!-- test:match-negative-offset -->
```php
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\Regex;

$value = XString::new('content');

#Test: $this->expectException(InvalidArgumentException::class);
$value->match(Regex::new('/./'), offset: -1);
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
$match = $value->match(Regex::new('/#(\d+)/'));

#Test: self::assertSame('Order #77 processed', (string) $value);
#Test: self::assertNotSame($value, $match);
#Test: self::assertSame('#77', (string) $match);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::match` | `public function match(Regex|array<Regex> $pattern, int $offset = 0): ?self` — Return the earliest substring matched by the provided regex patterns (starting from the optional offset), or `null` when nothing matches. |
