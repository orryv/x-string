# Regex::new()

## Table of Contents
- [Regex::new()](#regexnew)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Create a reusable regex adapter](#create-a-reusable-regex-adapter)
    - [Extract data with XString::match()](#extract-data-with-xstringmatch)
    - [Replace matches in a string](#replace-matches-in-a-string)
    - [Immutable value object semantics](#immutable-value-object-semantics)
    - [Surface invalid patterns when used](#surface-invalid-patterns-when-used)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public static function new(string $pattern): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Static factory | ✓ | Public |

## Description

Wrap a PCRE pattern in an immutable `Regex` value object. Pass the adapter to `XString` methods (and other helpers that accept
`Regex`) to clarify intent and avoid sprinkling bare pattern strings throughout your code.

## Important notes and considerations

- **No validation on construction.** The pattern string is stored verbatim; regex syntax errors surface when you *use* the adapter.
- **Encapsulation of modifiers.** Include delimiters and inline modifiers directly in `$pattern` (e.g. `'/foo/i'`).
- **Stringable.** Casting the adapter to a string returns the raw pattern.

## Parameters

| Parameter | Type | Default | Description |
| --- | --- | --- | --- |
| `$pattern` | `string` | — | PCRE pattern, including delimiters and optional inline modifiers. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Regex adapter holding the pattern. |

## Thrown exceptions

This factory does not perform validation and therefore does not throw additional exceptions. Regex errors are raised when an
`XString` method executes the pattern.

## Examples

### Create a reusable regex adapter

<!-- test:regex-new-basic -->
```php
use Orryv\XString\Regex;

$pattern = Regex::new('/^user-\d+$/');

#Test: self::assertSame('/^user-\d+$/', (string) $pattern);
```

### Extract data with XString::match()

<!-- test:regex-new-match -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$response = XString::new('Order #2048 confirmed');
$match = $response->match(Regex::new('/\d+/'));

#Test: self::assertSame('2048', (string) $match);
```

### Replace matches in a string

<!-- test:regex-new-replace -->
```php
use Orryv\XString;
use Orryv\XString\Regex;

$template = XString::new('Invoice-12345.pdf');
$result = $template->replace(Regex::new('/-\d+/'), '-{id}');

#Test: self::assertSame('Invoice-{id}.pdf', (string) $result);
```

### Immutable value object semantics

<!-- test:regex-new-immutability -->
```php
use Orryv\XString\Regex;

$original = Regex::new('/foo/');
$another = Regex::new('/foo/');

#Test: self::assertSame((string) $original, (string) $another);
#Test: self::assertNotSame($original, $another);
```

### Surface invalid patterns when used

<!-- test:regex-new-invalid -->
```php
use Orryv\XString;
use Orryv\XString\Regex;
use ValueError;

$string = XString::new('abc');

$this->expectException(ValueError::class);
$string->match(Regex::new('/(?P<unbalanced/'));
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `Regex::new` | `public static function new(string $pattern): self` — Wrap a PCRE pattern in an immutable adapter for safe reuse with `XString` methods. |
