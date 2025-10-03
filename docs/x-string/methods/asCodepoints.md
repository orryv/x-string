# XString::asCodepoints()

## Table of Contents
- [XString::asCodepoints()](#xstringascodepoints)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Count Unicode code points](#count-unicode-code-points)
    - [Alias equivalence with `withMode()`](#alias-equivalence-with-withmode)
    - [Empty encoding throws an exception](#empty-encoding-throws-an-exception)
    - [Immutability when switching to code points](#immutability-when-switching-to-code-points)
    - [Encoding labels are trimmed](#encoding-labels-are-trimmed)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function asCodepoints(string $encoding = 'UTF-8'): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Returns a new immutable `XString` that interprets lengths and offsets as Unicode code points. This is a thin wrapper around
[`withMode('codepoints', $encoding)`](./withMode.md) and is useful whenever you need deterministic results based on code
points rather than grapheme clusters or raw bytes.

## Important notes and considerations

- **Alias semantics.** This helper calls `withMode()` internally using the `codepoints` mode.
- **Encoding aware.** The provided `$encoding` must be recognized by `mbstring` and cannot be empty.
- **Immutable clone.** The original instance always remains unchanged.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$encoding` | `string` | `'UTF-8'` | Encoding used for multibyte operations such as `mb_strlen()`. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` configured to operate in code point mode with the chosen encoding. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | Thrown when `$encoding` is an empty string. |

## Examples

### Count Unicode code points

<!-- test:as-codepoints-length -->
```php
use Orryv\XString;

$xstring = XString::new("a\u{0301}");
$codepoints = $xstring->asCodepoints();

#Test: self::assertSame(2, $codepoints->length());
#Test: self::assertSame(1, $xstring->length());
```

### Alias equivalence with `withMode()`

<!-- test:as-codepoints-alias -->
```php
use Orryv\XString;

$xstring = XString::new('Résumé');

$manual = $xstring->withMode('codepoints');
$alias = $xstring->asCodepoints();

#Test: self::assertSame($manual->length(), $alias->length());
```

### Empty encoding throws an exception

<!-- test:as-codepoints-empty-encoding -->
```php
use InvalidArgumentException;
use Orryv\XString;

$xstring = XString::new('example');

#Test: $this->expectException(InvalidArgumentException::class);
$xstring->asCodepoints('');
```

### Immutability when switching to code points

<!-- test:as-codepoints-immutable -->
```php
use Orryv\XString;

$emoji = XString::new('👩‍💻');
$codepoints = $emoji->asCodepoints();

#Test: self::assertSame(3, $codepoints->length());
#Test: self::assertSame(1, $emoji->length());
#Test: self::assertNotSame($emoji, $codepoints);
```

### Encoding labels are trimmed

<!-- test:as-codepoints-trim-encoding -->
```php
use Orryv\XString;

$value = XString::new('Résumé');
$codepoints = $value->asCodepoints("  UTF-8  ");
$lower = $codepoints->toLower();

#Test: self::assertSame('résumé', (string) $lower);
#Test: self::assertSame('Résumé', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::asCodepoints` | `public function asCodepoints(string $encoding = 'UTF-8'): self` — Alias for `withMode('codepoints', $encoding)` returning a code point-aware clone. |
