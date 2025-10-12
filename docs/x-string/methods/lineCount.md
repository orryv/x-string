# XString::lineCount()

## Table of Contents
- [XString::lineCount()](#xstringlinecount)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Count lines in mixed newline formats](#count-lines-in-mixed-newline-formats)
    - [Trailing newline adds an empty line](#trailing-newline-adds-an-empty-line)
    - [Empty strings have zero lines](#empty-strings-have-zero-lines)
    - [Does not mutate the original instance](#does-not-mutate-the-original-instance)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function lineCount(): int
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✗ | Public |

## Description

Counts how many logical lines the current string contains. Splitting is newline-aware and supports `\n`, `\r\n`, and `\r`
sequences, mirroring the behaviour of [`lines()`](lines.md) without materialising the intermediate array when you only need the count.

## Important notes and considerations

- **Whitespace aware.** All newline conventions are recognised and collapsed consistently.
- **Trailing separators count.** A trailing newline produces an extra (empty) line, matching how `lines()` behaves.
- **Non-mutating.** The underlying instance is left untouched.

## Parameters

This method does not accept parameters.

## Returns

| Return Type | Description |
| --- | --- |
| `int` | Number of lines detected in the string. |

## Thrown exceptions

This method does not throw exceptions.

## Examples

### Count lines in mixed newline formats

<!-- test:line-count-mixed -->
```php
use Orryv\XString;

$text = "alpha\nbravo\rcharlie\r\ndelta";

#Test: self::assertSame(4, XString::new($text)->lineCount());
```

### Trailing newline adds an empty line

<!-- test:line-count-trailing -->
```php
use Orryv\XString;

$value = XString::new("First\nSecond\n");

#Test: self::assertSame(3, $value->lineCount());
#Test: self::assertSame(['First', 'Second', ''], $value->lines());
```

### Empty strings have zero lines

<!-- test:line-count-empty -->
```php
use Orryv\XString;

#Test: self::assertSame(0, XString::new('')->lineCount());
```

### Does not mutate the original instance

<!-- test:line-count-immutability -->
```php
use Orryv\XString;

$original = XString::new("One\nTwo");
$lines = $original->lineCount();

#Test: self::assertSame(2, $lines);
#Test: self::assertSame("One\nTwo", (string) $original);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::lineCount` | `public function lineCount(): int` — Return the number of newline-delimited lines without mutating the instance. |
