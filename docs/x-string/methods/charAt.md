# XString::charAt()

## Table of Contents
- [XString::charAt()](#xstringcharat)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Access grapheme clusters by index](#access-grapheme-clusters-by-index)
    - [Support negative indices](#support-negative-indices)
    - [Inspect individual bytes](#inspect-individual-bytes)
    - [Work with complex emoji sequences](#work-with-complex-emoji-sequences)
    - [Reject out-of-range indices](#reject-out-of-range-indices)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function charAt(int $index): string
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✗ | Public |

## Description

Returns the unit located at the specified index, honouring the instance's active iteration mode. By default `XString`
operates in grapheme mode so user-visible characters are returned, but switching via [`withMode()`](withMode.md) or the
`as…` helpers lets you inspect raw bytes or Unicode code points instead.

## Important notes and considerations

- **Mode sensitive.** The meaning of “character” depends on the current mode (`graphemes`, `codepoints`, or `bytes`).
- **Negative indexes supported.** Passing `-1` returns the last unit, `-2` the second-last, etc.
- **Bounds checked.** An `InvalidArgumentException` is thrown when the index is outside the valid range.
- **Non-mutating.** Calling `charAt()` never alters the original string.

## Parameters

| Name | Type | Description |
| --- | --- | --- |
| `$index` | `int` | Position to retrieve. Negative values address from the end of the string. |

## Returns

| Return Type | Description |
| --- | --- |
| `string` | The unit at the requested index in the active mode. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | When the index is outside the valid range or the string is empty. |

## Examples

### Access grapheme clusters by index

<!-- test:char-at-grapheme -->
```php
use Orryv\XString;

$value = XString::new('résumé');

#Test: self::assertSame('é', $value->charAt(1));
#Test: self::assertSame('m', $value->charAt(4));
#Test: self::assertSame('résumé', (string) $value);
```

### Support negative indices

<!-- test:char-at-negative -->
```php
use Orryv\XString;

$text = XString::new('Unicode');

#Test: self::assertSame('e', $text->charAt(-1));
#Test: self::assertSame('U', $text->charAt(-7));
```

### Inspect individual bytes

<!-- test:char-at-bytes -->
```php
use Orryv\XString;

$bytes = XString::new('Café')->asBytes();

#Test: self::assertSame('f', $bytes->charAt(2));
#Test: self::assertSame('c3', bin2hex($bytes->charAt(3)));
#Test: self::assertSame('a9', bin2hex($bytes->charAt(4)));
```

### Work with complex emoji sequences

<!-- test:char-at-emoji -->
```php
use Orryv\XString;

$emoji = XString::new('👨‍👩‍👧‍👦');

#Test: self::assertSame('👨‍👩‍👧‍👦', $emoji->charAt(0));
#Test: self::assertSame("\u{200D}", $emoji->asCodepoints()->charAt(1));
```

### Reject out-of-range indices

<!-- test:char-at-exception -->
```php
use InvalidArgumentException;
use Orryv\XString;

$empty = XString::new('');

#Test: $this->expectException(InvalidArgumentException::class);
$empty->charAt(0);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::charAt` | `public function charAt(int $index): string` — Retrieve the unit at a given index using the instance's current iteration mode, throwing when the index is invalid. |
