# XString::graphemeLength()

## Table of Contents
- [XString::graphemeLength()](#xstringgraphemelength)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Count user-visible characters](#count-user-visible-characters)
    - [Works consistently across modes](#works-consistently-across-modes)
    - [Handle complex emoji sequences](#handle-complex-emoji-sequences)
    - [Empty strings report zero](#empty-strings-report-zero)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function graphemeLength(): int
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✗ | Public |

## Description

Returns the number of grapheme clusters (human-perceived characters) in the current string. Unlike `length()`, this method
ignores the active iteration mode and always counts graphemes, so combining marks, surrogate pairs and emoji sequences are
reported as a single unit.

## Important notes and considerations

- **Mode agnostic.** The result is the same regardless of whether the instance has been switched to byte or codepoint mode.
- **Unicode-aware fallbacks.** Uses PHP's `grapheme_strlen()` when available and falls back to robust regex- and mbstring-based counting.
- **Non-mutating.** The original string and its mode remain unchanged.

## Parameters

This method does not accept parameters.

## Returns

| Return Type | Description |
| --- | --- |
| `int` | Number of grapheme clusters in the string. |

## Thrown exceptions

This method does not throw exceptions.

## Examples

### Count user-visible characters

<!-- test:grapheme-length-basic -->
```php
use Orryv\XString;

$xstring = XString::new("Cafe\u{0301}");

#Test: self::assertSame(4, $xstring->graphemeLength());
#Test: self::assertSame("Cafe\u{0301}", (string) $xstring);
```

### Works consistently across modes

<!-- test:grapheme-length-modes -->
```php
use Orryv\XString;

$value = "mañana";
$xstring = XString::new($value);

#Test: self::assertSame(6, $xstring->graphemeLength());
#Test: self::assertSame(6, $xstring->asBytes()->graphemeLength());
#Test: self::assertSame(6, $xstring->asCodepoints()->graphemeLength());
```

### Handle complex emoji sequences

<!-- test:grapheme-length-emoji -->
```php
use Orryv\XString;

$astronaut = "👩‍🚀";

#Test: self::assertSame(1, XString::new($astronaut)->graphemeLength());
#Test: self::assertSame(3, XString::new($astronaut)->asCodepoints()->length());
```

### Empty strings report zero

<!-- test:grapheme-length-empty -->
```php
use Orryv\XString;

$xstring = XString::new('');

#Test: self::assertSame(0, $xstring->graphemeLength());
#Test: self::assertSame('', (string) $xstring);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::graphemeLength` | `public function graphemeLength(): int` — Count Unicode grapheme clusters regardless of the current iteration mode. |
