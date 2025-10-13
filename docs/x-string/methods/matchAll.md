# XString::matchAll()

## Table of Contents
- [XString::matchAll()](#xstringmatchall)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Collect every numeric token](#collect-every-numeric-token)
    - [Limit the number of collected matches](#limit-the-number-of-collected-matches)
    - [Combine multiple patterns with named captures](#combine-multiple-patterns-with-named-captures)
    - [Request structured matches with PREG_SET_ORDER](#request-structured-matches-with-preg_set_order)
    - [Provide flags as an array](#provide-flags-as-an-array)
    - [Limit of zero always returns an empty result](#limit-of-zero-always-returns-an-empty-result)
    - [Empty pattern arrays are rejected](#empty-pattern-arrays-are-rejected)
    - [All patterns must be Regex instances](#all-patterns-must-be-regex-instances)
    - [Invalid patterns bubble up as ValueError](#invalid-patterns-bubble-up-as-valueerror)
    - [Original instance remains unchanged](#original-instance-remains-unchanged)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function matchAll(
    Regex|array<Regex> $pattern,
    false|int $limit = false,
    array|int|null $flags = PREG_PATTERN_ORDER
): array
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✗ | Public |

## Description

Executes one or more regular expressions against the current string and returns the aggregated result of
`preg_match_all()`. Use `$flags` to control the output format (e.g. `PREG_PATTERN_ORDER`, `PREG_SET_ORDER`,
`PREG_OFFSET_CAPTURE`) and `$limit` to cap the total number of captured matches across every supplied pattern.
When multiple patterns are provided, captures with the same index or name are appended to the same output entry.

## Important notes and considerations

- **Aggregated captures.** Full matches share the index `0`. Give your capture groups names when you need to
  distinguish data gathered by different patterns.
- **Limit applies globally.** `$limit` counts total matches collected across every pattern. `false` means no limit;
  `0` always returns an empty array.
- **Result structure mirrors `preg_match_all()`.** Use `PREG_SET_ORDER` when you prefer one array per match.
- **Validation.** Empty pattern arrays or non-`Regex` entries trigger `InvalidArgumentException`. Invalid regular
  expressions surface as `ValueError`.
- **Immutability.** The method never mutates the original `XString` instance.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$pattern` | `Regex\|array<Regex>` | — | Regular expression(s) executed against the string. |
| `$limit` | `false\|int` | `false` | Maximum number of matches to collect. `false` removes the limit, `0` returns an empty result. |
| `$flags` | `array\|int\|null` | `PREG_PATTERN_ORDER` | Bitmask passed to `preg_match_all()`. You may supply a single integer or an array of flags to combine. |

## Returns

| Return Type | Description |
| --- | --- |
| `array` | Aggregated results from `preg_match_all()`. Uses capture indexes/names as keys when in `PREG_PATTERN_ORDER` mode, or a list of match arrays when `PREG_SET_ORDER` is requested. Returns an empty array when nothing matches. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | `$pattern` is empty, contains non-`Regex` entries, or `$limit` is negative. |
| `ValueError` | One of the supplied regex patterns is invalid. |

## Examples

### Collect every numeric token

<!-- test:match-all-digits -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$log = XString::new('IDs: 135, 246, 579.');
$matches = $log->matchAll(Regex::new('/\d{3}/'));

#Test: self::assertSame(['135', '246', '579'], $matches[0]);
```

### Limit the number of collected matches

<!-- test:match-all-limit -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$sequence = XString::new('1, 2, 3, 4, 5, 6');
$matches = $sequence->matchAll(Regex::new('/\d/'), limit: 3);

#Test: self::assertSame(['1', '2', '3'], $matches[0]);
```

### Combine multiple patterns with named captures

<!-- test:match-all-multiple-patterns -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$report = XString::new('Sprint #42 finished with 17 tasks done.');
$matches = $report->matchAll([
    Regex::new('/(?P<number>\d+)/'),
    Regex::new('/(?P<tag>#\d+)/'),
]);

#Test: self::assertSame(['42', '17'], $matches['number']);
#Test: self::assertSame(['#42'], $matches['tag']);
```

### Request structured matches with PREG_SET_ORDER

<!-- test:match-all-set-order -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$line = XString::new('age=39, score=98');
$matches = $line->matchAll(
    Regex::new('/(?P<key>\w+)=(?P<value>\d+)/'),
    flags: PREG_SET_ORDER
);

#Test: self::assertCount(2, $matches);
#Test: self::assertSame('age', $matches[0]['key']);
#Test: self::assertSame('39', $matches[0]['value']);
#Test: self::assertSame('score', $matches[1]['key']);
#Test: self::assertSame('98', $matches[1]['value']);
```

### Provide flags as an array

<!-- test:match-all-flags-array -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$text = XString::new('Line 1\nLine 2');
$matches = $text->matchAll(
    Regex::new('/Line (?P<index>\d)/'),
    flags: [PREG_SET_ORDER, PREG_OFFSET_CAPTURE]
);

#Test: self::assertSame('1', $matches[0]['index'][0]);
#Test: self::assertSame(['Line 2', 8], $matches[1][0]);
```

### Limit of zero always returns an empty result

<!-- test:match-all-limit-zero -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$value = XString::new('numbers: 10 20 30');
$matches = $value->matchAll(Regex::new('/\d+/'), limit: 0);

#Test: self::assertSame([], $matches);
```

### Empty pattern arrays are rejected

<!-- test:match-all-empty-patterns -->
```php
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\Regex;

$value = XString::new('content');

#Test: $this->expectException(InvalidArgumentException::class);
$value->matchAll([]);
```

### All patterns must be Regex instances

<!-- test:match-all-non-regex -->
```php
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\Regex;

$value = XString::new('content');
$patterns = [Regex::new('/foo/'), 'bar'];

#Test: $this->expectException(InvalidArgumentException::class);
$value->matchAll($patterns);
```

### Invalid patterns bubble up as ValueError

<!-- test:match-all-invalid-pattern -->
```php
use Orryv\XString;
use Orryv\XString\Regex;
use ValueError;

$value = XString::new('content');

#Test: $this->expectException(ValueError::class);
$value->matchAll(Regex::new('/(unclosed/'));
```

### Original instance remains unchanged

<!-- test:match-all-immutability -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$value = XString::new('Order #55 processed');
$value->matchAll(Regex::new('/\d+/'));

#Test: self::assertSame('Order #55 processed', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::matchAll` | `public function matchAll(Regex|array<Regex> $pattern, false|int $limit = false, array|int|null $flags = PREG_PATTERN_ORDER): array` — Run one or more regex patterns and aggregate every match, honouring `preg_match_all()` flags and optional global limits. |
