# XString::asGraphemes()

## Table of Contents
- [XString::asGraphemes()](#xstringasgraphemes)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Normalize to grapheme mode](#normalize-to-grapheme-mode)
    - [Alias equivalence with `withMode()`](#alias-equivalence-with-withmode)
    - [Empty encoding throws an exception](#empty-encoding-throws-an-exception)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function asGraphemes(string $encoding = 'UTF-8'): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

A shorthand for [`withMode('graphemes', $encoding)`](./withMode.md) that guarantees the instance interprets offsets and lengths
as grapheme clusters—what end users typically perceive as a single character. Grapheme mode is also the library's default view,
so this method is handy when you need to explicitly reset after switching to bytes or code points.

## Important notes and considerations

- **Alias semantics.** Internally delegates to `withMode()` using the `graphemes` mode.
- **Encoding aware.** `$encoding` must be a non-empty string accepted by `mbstring` / `intl` utilities.
- **Immutable clone.** Always returns a new `XString` without mutating the original.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$encoding` | `string` | `'UTF-8'` | Encoding used for multibyte and grapheme operations. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` set to grapheme mode using the supplied encoding. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | Thrown when `$encoding` is an empty string. |

## Examples

### Normalize to grapheme mode

<!-- test:as-graphemes-length -->
```php
use Orryv\XString;

$xstring = XString::new("a\u{0301}");
$bytes = $xstring->asBytes();
$graphemes = $bytes->asGraphemes();

#Test: self::assertSame(1, $graphemes->length());
#Test: self::assertSame(3, $bytes->length());
```

### Alias equivalence with `withMode()`

<!-- test:as-graphemes-alias -->
```php
use Orryv\XString;

$xstring = XString::new('👩‍💻 developer');
$manual = $xstring->withMode('graphemes');
$alias = $xstring->asGraphemes();

#Test: self::assertSame($manual->length(), $alias->length());
```

### Empty encoding throws an exception

<!-- test:as-graphemes-empty-encoding -->
```php
use InvalidArgumentException;
use Orryv\XString;

$xstring = XString::new('example');

#Test: $this->expectException(InvalidArgumentException::class);
$xstring->asGraphemes('');
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::asGraphemes` | `public function asGraphemes(string $encoding = 'UTF-8'): self` — Alias for `withMode('graphemes', $encoding)` returning a grapheme-aware clone. |
