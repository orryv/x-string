# XString::toSnake()

## Table of Contents
- [XString::toSnake()](#xstringtosnake)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Convert a space separated string](#convert-a-space-separated-string)
    - [Convert from hyphen separated input](#convert-from-hyphen-separated-input)
    - [Convert using multiple delimiters](#convert-using-multiple-delimiters)
    - [Handle PascalCase input](#handle-pascalcase-input)
    - [Insert separators between letters and digits](#insert-separators-between-letters-and-digits)
    - [Normalize existing snake case](#normalize-existing-snake-case)
    - [Empty strings remain unchanged](#empty-strings-remain-unchanged)
    - [Immutability check](#immutability-check)
    - [Reject empty delimiters](#reject-empty-delimiters)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toSnake(Newline|HtmlTag|Regex|string|array<Newline|HtmlTag|Regex|string> $input_delimiter = ' '): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Creates a snake_case variant of the current string. The provided delimiter represents how the words in the source are separated
(e.g. spaces, hyphens, underscores). The method also inserts delimiters at case and alphanumeric transitions to make converting
PascalCase or camelCase straightforward. The result is lower-cased and underscores are used as the output separator.

## Important notes and considerations

- **Input delimiter awareness.** Pass the delimiter that separates words in the input. The default (`' '`) handles
  space-separated phrases. Provide `'-'`, `'_'`, or an array of delimiters for other formats.
- **Automatic boundary detection.** Case transitions (such as `FooBar`) and letter/digit boundaries automatically receive an
  inserted delimiter before conversion.
- **Immutable.** A new instance is returned while the original value is left untouched.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$input_delimiter` | `Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string>` | `' '` | Expected delimiter(s) in the source string used to split words. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` whose value is transformed to snake_case. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | Thrown when `$input_delimiter` is empty or contains empty strings. |

## Examples

### Convert a space separated string

<!-- test:tosnake-basic -->
```php
use Orryv\XString;

$title = XString::new('Hello World Example');
$result = $title->toSnake();
#Test: self::assertSame('hello_world_example', (string) $result);
#Test: self::assertSame('Hello World Example', (string) $title);
```

### Convert from hyphen separated input

<!-- test:tosnake-hyphen -->
```php
use Orryv\XString;

$slug = XString::new('already-separated-value');
$result = $slug->toSnake('-');
#Test: self::assertSame('already_separated_value', (string) $result);
```

### Convert using multiple delimiters

<!-- test:tosnake-multiple-delimiters -->
```php
use Orryv\XString;

$value = XString::new('this.is-aString');
$result = $value->toSnake(['.', '-']);
#Test: self::assertSame('this_is_a_string', (string) $result);
```

### Handle PascalCase input

<!-- test:tosnake-pascal -->
```php
use Orryv\XString;

$class = XString::new('HTTPRequestHandler');
$result = $class->toSnake();
#Test: self::assertSame('http_request_handler', (string) $result);
```

### Insert separators between letters and digits

<!-- test:tosnake-digits -->
```php
use Orryv\XString;

$version = XString::new('Version2Update');
$result = $version->toSnake();
#Test: self::assertSame('version_2_update', (string) $result);
```

### Normalize existing snake case

<!-- test:tosnake-existing -->
```php
use Orryv\XString;

$value = XString::new('Already_Snake');
$result = $value->toSnake('_');
#Test: self::assertSame('already_snake', (string) $result);
```

### Empty strings remain unchanged

<!-- test:tosnake-empty -->
```php
use Orryv\XString;

$empty = XString::new('');
$result = $empty->toSnake();
#Test: self::assertSame('', (string) $result);
```

### Immutability check

<!-- test:tosnake-immutability -->
```php
use Orryv\XString;

$value = XString::new('MutableValue');
$snake = $value->toSnake();
#Test: self::assertSame('MutableValue', (string) $value);
#Test: self::assertSame('mutable_value', (string) $snake);
```

### Reject empty delimiters

<!-- test:tosnake-invalid-delimiter -->
```php
use Orryv\XString;
use InvalidArgumentException;

$value = XString::new('Example');
#Test: $this->expectException(InvalidArgumentException::class);
$value->toSnake('');
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toSnake` | `public function toSnake(Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string> $input_delimiter = ' '): self` — Convert the string to snake_case using the provided input delimiter(s) while preserving the original instance. |
