# XString::length()

## Table of Contents
- [XString::length()](#xstringlength)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Count grapheme clusters by default](#count-grapheme-clusters-by-default)
    - [Count raw bytes](#count-raw-bytes)
    - [Count Unicode code points](#count-unicode-code-points)
    - [Handle complex emoji sequences](#handle-complex-emoji-sequences)
    - [Empty strings report zero](#empty-strings-report-zero)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function length(): int
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✗ | Public |

## Description

Returns the length of the current string in the active iteration mode. By default `XString` operates in **grapheme** mode, so the
result reflects user-perceived characters. Switching to `bytes` or `codepoints` via [`withMode()`](withMode.md) (or one of the
`as…` helpers) changes the measurement strategy.

## Important notes and considerations

- **Mode dependent.** The result varies depending on whether the instance is in `bytes`, `codepoints`, or `graphemes` mode.
- **Encoding aware.** Multibyte-aware functions are used when available to ensure accurate counts across encodings.
- **Non-mutating.** Calling `length()` does not alter the original string.

## Parameters

This method does not accept parameters.

## Returns

| Return Type | Description |
| --- | --- |
| `int` | The string length measured in the currently active mode. |

## Thrown exceptions

This method does not throw exceptions.

## Examples

### Count grapheme clusters by default

<!-- test:length-default-grapheme -->
```php
use Orryv\XString;

$value = "Cafe\u{0301}"; // "Cafe" with a combining accent
$xstring = XString::new($value);

#Test: self::assertSame(4, $xstring->length());
#Test: self::assertSame($value, (string) $xstring);
```

### Count raw bytes

<!-- test:length-bytes -->
```php
use Orryv\XString;

$value = "Cafe\u{0301}"; // 5 bytes in UTF-8
$xstring = XString::new($value)->asBytes();

#Test: self::assertSame(strlen($value), $xstring->length());
```

### Count Unicode code points

<!-- test:length-codepoints -->
```php
use Orryv\XString;

$value = "Cafe\u{0301}";
$xstring = XString::new($value)->asCodepoints();

#Test: self::assertSame(5, $xstring->length());
```

### Handle complex emoji sequences

<!-- test:length-emoji -->
```php
use Orryv\XString;

$value = "👩‍🚀"; // Woman astronaut (single grapheme, multiple code points)

#Test: self::assertSame(1, XString::new($value)->length());
#Test: self::assertGreaterThan(1, XString::new($value)->asCodepoints()->length());
#Test: self::assertSame(strlen($value), XString::new($value)->asBytes()->length());
```

### Empty strings report zero

<!-- test:length-empty -->
```php
use Orryv\XString;

$xstring = XString::new('');

#Test: self::assertSame(0, $xstring->length());
#Test: self::assertSame(0, $xstring->asBytes()->length());
#Test: self::assertSame(0, $xstring->asCodepoints()->length());
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::length` | `public function length(): int` — Return the string length using the instance's current mode (`bytes`, `codepoints`, or `graphemes`). |
