# XString::lines()

## Table of Contents
- [XString::lines()](#xstringlines)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Split multi-line text](#split-multi-line-text)
    - [Trim each line](#trim-each-line)
    - [Limit the number of lines](#limit-the-number-of-lines)
    - [Handle mixed newline styles](#handle-mixed-newline-styles)
    - [Preserve trailing empty line](#preserve-trailing-empty-line)
    - [Empty strings return no lines](#empty-strings-return-no-lines)
    - [Works after changing mode](#works-after-changing-mode)
    - [Reject invalid limits](#reject-invalid-limits-1)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function lines(bool $trim = false, ?int $limit = null): array
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✗ | Public |

## Description

Returns the contents of the current string as an array of lines. You can optionally trim surrounding whitespace from each line
and/or cap the total number of lines returned. Internally the method recognises `\n`, `\r`, and `\r\n`, so it works across
platform-specific newline styles.

## Important notes and considerations

- **No mutation.** The original `XString` instance remains untouched; the method simply returns an array.
- **Trim option.** When `$trim` is `true`, leading and trailing whitespace (spaces, tabs, newlines) is removed from each line.
- **Limit semantics.** `$limit` represents the maximum number of lines. The final entry contains the remainder of the string once the limit is reached.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$trim` | `bool` | `false` | Remove leading and trailing whitespace from every extracted line. |
| `$limit` | `?int` | `null` | Optional maximum number of lines to return. Must be greater than or equal to 1 when provided. |

## Returns

| Return Type | Description |
| --- | --- |
| `list<string>` | Lines of text in original order. Trailing delimiters yield empty strings, mirroring PHP's native behaviour. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | `$limit` is provided but less than 1. |

## Examples

### Split multi-line text

<!-- test:lines-basic -->
```php
use Orryv\XString;

$text = XString::new("first\nsecond\nthird");
$lines = $text->lines();

#Test: self::assertSame(['first', 'second', 'third'], $lines);
#Test: self::assertSame("first\nsecond\nthird", (string) $text);
```

### Trim each line

<!-- test:lines-trim -->
```php
use Orryv\XString;

$text = XString::new("  alpha  \n\tbeta \n gamma\t");
$lines = $text->lines(trim: true);

#Test: self::assertSame(['alpha', 'beta', 'gamma'], $lines);
```

### Limit the number of lines

<!-- test:lines-limit -->
```php
use Orryv\XString;

$text = XString::new("a\nb\nc\nd");
$lines = $text->lines(limit: 3);

#Test: self::assertSame(['a', 'b', "c\nd"], $lines);
```

### Handle mixed newline styles

<!-- test:lines-mixed-newlines -->
```php
use Orryv\XString;

$text = XString::new("line1\r\nline2\nline3\rline4");
$lines = $text->lines();

#Test: self::assertSame(['line1', 'line2', 'line3', 'line4'], $lines);
```

### Preserve trailing empty line

<!-- test:lines-trailing -->
```php
use Orryv\XString;

$text = XString::new("hello\n");
$lines = $text->lines();

#Test: self::assertSame(['hello', ''], $lines);
```

### Empty strings return no lines

<!-- test:lines-empty -->
```php
use Orryv\XString;

$lines = XString::new('')->lines();

#Test: self::assertSame([], $lines);
```

### Works after changing mode

<!-- test:lines-mode -->
```php
use Orryv\XString;

$xstring = XString::new("α\nβ")->withMode('codepoints');
$lines = $xstring->lines();

#Test: self::assertSame(['α', 'β'], $lines);
```

### Reject invalid limits

<!-- test:lines-invalid-limit -->
```php
use InvalidArgumentException;
use Orryv\XString;

$text = XString::new("line one\nline two");

#Test: $this->expectException(InvalidArgumentException::class);
$text->lines(limit: 0);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::lines` | `public function lines(bool $trim = false, ?int $limit = null): array` — Split the string into an array of lines with optional trimming and limit control. |
