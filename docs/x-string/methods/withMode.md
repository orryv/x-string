# XString::withMode()

## Table of Contents
- [XString::withMode()](#xstringwithmode)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Measure byte length using byte mode](#measure-byte-length-using-byte-mode)
    - [Inspect Unicode code points](#inspect-unicode-code-points)
    - [Switch encoding for downstream operations](#switch-encoding-for-downstream-operations)
    - [Invalid mode throws an exception](#invalid-mode-throws-an-exception)
    - [Empty encoding throws an exception](#empty-encoding-throws-an-exception)
    - [Mode names are case-insensitive](#mode-names-are-case-insensitive)
    - [Round-trip between byte and grapheme views](#round-trip-between-byte-and-grapheme-views)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function withMode(string $mode = 'graphemes', string $encoding = 'UTF-8'): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Creates a new immutable `XString` that shares the same string value but operates in the requested **mode** and **encoding**.
Modes control how traversal and length-related helpers interpret the string:

- `bytes` — raw byte offsets/lengths.
- `codepoints` — Unicode code point units (requires `mbstring`).
- `graphemes` — user-perceived characters (default; requires `intl` or falls back to `mbstring`).

The selected encoding is used by multibyte-aware operations such as `length()`, `toUpper()`, and other helpers that rely on
`mb_*` functions. The original instance remains unchanged, making it easy to hop between different views of the same data.

## Important notes and considerations

- **Immutable clone.** The method always returns a brand-new `XString`; the receiver is never modified.
- **Validation.** Only `bytes`, `codepoints`, and `graphemes` are accepted. Encoding strings must be non-empty.
- **Graceful fallbacks.** When grapheme utilities are unavailable the class falls back to multibyte functions.
- **Aliases.** Convenience helpers [`asBytes()`](./asBytes.md), [`asCodepoints()`](./asCodepoints.md), and
  [`asGraphemes()`](./asGraphemes.md) simply call `withMode()` with predefined arguments.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$mode` | `string` | `'graphemes'` | Interpretation mode: `bytes`, `codepoints`, or `graphemes`. Case-insensitive. |
| `$encoding` | `string` | `'UTF-8'` | Encoding used by multibyte operations. Must be a non-empty string accepted by `mbstring`. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` containing the same text but configured with the requested mode and encoding. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | Thrown when `$mode` is not one of `bytes`, `codepoints`, `graphemes`, or when `$encoding` is empty. |

## Examples

### Measure byte length using byte mode

<!-- test:with-mode-bytes -->
```php
use Orryv\XString;

// "a\u{0301}" is the letter a followed by a combining accent.
$xstring = XString::new("a\u{0301}");
$bytes = $xstring->withMode('bytes');

#Test: self::assertSame(1, $xstring->length());
#Test: self::assertSame(3, $bytes->length());
```

### Inspect Unicode code points

<!-- test:with-mode-codepoints -->
```php
use Orryv\XString;

$xstring = XString::new("a\u{0301}");
$codepoints = $xstring->withMode('codepoints');

#Test: self::assertSame(2, $codepoints->length());
#Test: self::assertSame(1, $xstring->length());
```

### Switch encoding for downstream operations

<!-- test:with-mode-encoding -->
```php
use Orryv\XString;

$xstring = XString::new('hello world');
$iso = $xstring->withMode('graphemes', 'ISO-8859-1');
$upper = $iso->toUpper();

#Test: self::assertSame('HELLO WORLD', (string) $upper);
#Test: self::assertSame('hello world', (string) $xstring);
```

### Invalid mode throws an exception

<!-- test:with-mode-invalid-mode -->
```php
use InvalidArgumentException;
use Orryv\XString;

$xstring = XString::new('example');

#Test: $this->expectException(InvalidArgumentException::class);
$xstring->withMode('invalid');
```

### Empty encoding throws an exception

<!-- test:with-mode-empty-encoding -->
```php
use InvalidArgumentException;
use Orryv\XString;

$xstring = XString::new('example');

#Test: $this->expectException(InvalidArgumentException::class);
$xstring->withMode('codepoints', '');
```

### Mode names are case-insensitive

<!-- test:with-mode-case-insensitive -->
```php
use Orryv\XString;

$xstring = XString::new('é');
$bytes = $xstring->withMode('BYTES');

#Test: self::assertSame(2, $bytes->length());
#Test: self::assertNotSame($xstring, $bytes);
```

### Encoding influences multibyte calculations

<!-- test:with-mode-encoding-length -->
```php
use Orryv\XString;

$word = XString::new('Ångström');
$utf8 = $word->withMode('codepoints', 'UTF-8');
$iso = $word->withMode('codepoints', 'ISO-8859-1');

#Test: self::assertSame(8, $utf8->length());
#Test: self::assertSame(10, $iso->length());
#Test: self::assertSame('Ångström', (string) $word);
```

### Round-trip between byte and grapheme views

<!-- test:with-mode-round-trip -->
```php
use Orryv\XString;

$emoji = XString::new('👩‍💻');
$bytes = $emoji->withMode('bytes');
$graphemes = $bytes->withMode('graphemes');

#Test: self::assertSame(11, $bytes->length());
#Test: self::assertSame(1, $graphemes->length());
#Test: self::assertSame('👩‍💻', (string) $emoji);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::withMode` | `public function withMode(string $mode = 'graphemes', string $encoding = 'UTF-8'): self` — Return a new immutable instance operating in the specified mode and encoding. |
