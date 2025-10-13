# XStringType::regex()

## Table of Contents
- [XStringType::regex()](#xstringtyperegex)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Wrap a reusable pattern for matching](#wrap-a-reusable-pattern-for-matching)
    - [Reuse the adapter across string operations](#reuse-the-adapter-across-string-operations)
    - [Original adapter stays untouched](#original-adapter-stays-untouched)
    - [Surface invalid patterns when used](#surface-invalid-patterns-when-used)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public static function regex(string $pattern): Regex
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv` | Static factory | ✓ | Public |

## Description

Create a new immutable [`Regex`](../../regex/methods/new.md) adapter from a PCRE pattern. The helper mirrors `Regex::new()` while
keeping fluent pipelines readable when you prefer constructing type-aware adapters via `XStringType`.

## Important notes and considerations

- **Delegate factory.** Behaviour matches [`Regex::new()`](../../regex/methods/new.md); validation occurs when the pattern is used.
- **Provide delimiters.** Supply the complete PCRE pattern including delimiters and inline modifiers (e.g. `'/foo/i'`).
- **Immutable adapter.** Each call returns a fresh `Regex` object—perfect for reusing the same pattern in different operations.

## Parameters

| Parameter | Type | Description |
| --- | --- | --- |
| `$pattern` | `string` | Complete PCRE pattern including delimiters and optional inline modifiers. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `Regex` | ✓ | Regex adapter wrapping the provided pattern. |

## Thrown exceptions

This helper does not perform validation and therefore does not throw additional exceptions. Errors in the pattern surface when
an `XString` method executes it.

## Examples

### Wrap a reusable pattern for matching

<!-- test:xstring-type-regex-match -->
```php
use Orryv\XString;
use Orryv\XStringType;
use Orryv\XString\Regex;

$pattern = XStringType::regex('/\d+/');
$result = XString::new('Invoice-2048')->match($pattern);

#Test: self::assertInstanceOf(Regex::class, $pattern);
#Test: self::assertSame('2048', (string) $result);
```

### Reuse the adapter across string operations

<!-- test:xstring-type-regex-reuse -->
```php
use Orryv\XString;
use Orryv\XStringType;

$pattern = XStringType::regex('/ETA:\s*(\d{2}:\d{2})/');

$schedule = XString::new('ETA: 19:45');
$announcement = $schedule->replace($pattern, 'Arrives at $1');
$message = XString::new('Boarding ETA: 19:45');

#Test: self::assertSame('Arrives at 19:45', (string) $announcement);
#Test: self::assertTrue($message->contains($pattern));
#Test: self::assertSame('ETA: 19:45', (string) $schedule);
```

### Original adapter stays untouched

<!-- test:xstring-type-regex-immutable -->
```php
use Orryv\XStringType;

$first = XStringType::regex('/foo/');
$second = XStringType::regex('/foo/');

#Test: self::assertSame((string) $first, (string) $second);
#Test: self::assertNotSame($first, $second);
```

### Surface invalid patterns when used

<!-- test:xstring-type-regex-invalid -->
```php
use Orryv\XString;
use Orryv\XStringType;
use ValueError;

$string = XString::new('abc');

#Test: $this->expectException(ValueError::class);
$string->match(XStringType::regex('/(?P<unbalanced/'));
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XStringType::regex` | `public static function regex(string $pattern): Regex` — Create a `Regex` adapter using a complete PCRE pattern. |
