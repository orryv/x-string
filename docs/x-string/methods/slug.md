# XString::slug()

## Table of Contents
- [XString::slug()](#xstringslug)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Basic ASCII slug conversion](#basic-ascii-slug-conversion)
    - [Stripping punctuation and collapsing whitespace](#stripping-punctuation-and-collapsing-whitespace)
    - [Transliterating accented characters](#transliterating-accented-characters)
    - [Using a custom separator](#using-a-custom-separator)
    - [Multi-character separators are normalised](#multi-character-separators-are-normalised)
    - [Empty input yields an empty slug](#empty-input-yields-an-empty-slug)
    - [Separator must be non-empty](#separator-must-be-non-empty)
    - [Original instance stays intact](#original-instance-stays-intact)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function slug(Newline|HtmlTag|string $separator = '-'): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Converts the string to a URL-friendly *slug*. The method lowercases the value, transliterates accented characters to ASCII when
possible, removes punctuation, and replaces runs of whitespace or non-alphanumeric characters with the chosen separator. Multiple
adjacent separators are collapsed into a single instance and trimmed from both ends.

## Important notes and considerations

- **Transliteration aware.** Uses `Transliterator` and `iconv()` when available to convert extended characters to ASCII.
- **Separator validation.** An empty separator is rejected with an `InvalidArgumentException` to avoid ambiguous results.
- **Mode/encoding preserved.** The returned `XString` retains the original mode and encoding configuration.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$separator` | `Newline\|HtmlTag\|string` | `'-'` | String inserted between slug parts. Must not be empty after normalisation. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` containing the slugified string. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | The `$separator` normalises to an empty string. |

## Examples

### Basic ASCII slug conversion

<!-- test:slug-basic -->
```php
use Orryv\XString;

$value = XString::new('Hello, World!');
$slug = $value->slug();

#Test: self::assertSame('hello-world', (string) $slug);
```

### Stripping punctuation and collapsing whitespace

<!-- test:slug-punctuation -->
```php
use Orryv\XString;

$value = XString::new(' Rock   &   Roll!!! ');
$slug = $value->slug();

#Test: self::assertSame('rock-roll', (string) $slug);
```

### Transliterating accented characters

<!-- test:slug-accented -->
```php
use Orryv\XString;

$value = XString::new('Crème brûlée à la carte');
$slug = $value->slug();

#Test: self::assertSame('creme-brulee-a-la-carte', (string) $slug);
```

### Using a custom separator

<!-- test:slug-custom-separator -->
```php
use Orryv\XString;

$value = XString::new('foo bar baz');
$slug = $value->slug('_');

#Test: self::assertSame('foo_bar_baz', (string) $slug);
```

### Multi-character separators are normalised

<!-- test:slug-multi-separator -->
```php
use Orryv\XString;

$value = XString::new('Ready... Set... Go!');
$slug = $value->slug('--');

#Test: self::assertSame('ready--set--go', (string) $slug);
```

### Empty input yields an empty slug

<!-- test:slug-empty-input -->
```php
use Orryv\XString;

$value = XString::new('');
$slug = $value->slug();

#Test: self::assertSame('', (string) $slug);
```

### Separator must be non-empty

<!-- test:slug-empty-separator -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('will fail');

#Test: $this->expectException(InvalidArgumentException::class);
$value->slug('');
```

### Original instance stays intact

<!-- test:slug-immutable -->
```php
use Orryv\XString;

$value = XString::new('Mutable? No!');
$slug = $value->slug();

#Test: self::assertSame('Mutable? No!', (string) $value);
#Test: self::assertSame('mutable-no', (string) $slug);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::slug` | `public function slug(Newline\|HtmlTag|string $separator = '-'): self` — Build a URL-friendly slug with transliteration and separator normalisation. |
