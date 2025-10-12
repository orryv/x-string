# XString::toTitle()

## Table of Contents
- [XString::toTitle()](#xstringtotitle)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Convert a basic sentence](#convert-a-basic-sentence)
    - [Retain punctuation separators](#retain-punctuation-separators)
    - [Normalize irregular spacing](#normalize-irregular-spacing)
    - [Handle accented characters](#handle-accented-characters)
    - [Grapheme mode awareness](#grapheme-mode-awareness)
    - [Empty strings stay empty](#empty-strings-stay-empty)
    - [Immutability check](#immutability-check)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toTitle(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Produces a Title Case version of the current string. Each word's first letter is capitalised while the remaining characters are
lowercased using encoding-aware functions. Separators such as whitespace, hyphens, and apostrophes are preserved.

## Important notes and considerations

- **Encoding sensitive.** Uses `mb_convert_case()` when available, falling back to multibyte-friendly `ucwords()` otherwise.
- **No mutation.** Returns a new `XString` instance; the original remains untouched.
- **Mode preserved.** The mode/encoding configuration stays the same on the new instance.

## Parameters

This method does not accept parameters.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` containing the Title Cased text. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Convert a basic sentence

<!-- test:totitle-basic -->
```php
use Orryv\XString;

$value = XString::new('once upon a time');
$result = $value->toTitle();

#Test: self::assertSame('Once Upon A Time', (string) $result);
#Test: self::assertSame('once upon a time', (string) $value);
```

### Retain punctuation separators

<!-- test:totitle-punctuation -->
```php
use Orryv\XString;

$result = XString::new('well-known co-founder')->toTitle();

#Test: self::assertSame('Well-Known Co-Founder', (string) $result);
```

### Normalize irregular spacing

<!-- test:totitle-spacing -->
```php
use Orryv\XString;

$result = XString::new("multiple\tspaces\nallowed")->toTitle();

#Test: self::assertSame("Multiple\tSpaces\nAllowed", (string) $result);
```

### Handle accented characters

<!-- test:totitle-accents -->
```php
use Orryv\XString;

$result = XString::new("l'été à l'ombre")->toTitle();

#Test: self::assertSame("L'Été À L'Ombre", (string) $result);
```

### Grapheme mode awareness

<!-- test:totitle-grapheme-mode -->
```php
use Orryv\XString;

$value = XString::new('ångor ångström')->withMode('graphemes');
$result = $value->toTitle();

#Test: self::assertSame('Ångor Ångström', (string) $result);
#Test: self::assertSame(14, $result->length());
```

### Empty strings stay empty

<!-- test:totitle-empty -->
```php
use Orryv\XString;

$result = XString::new('')->toTitle();

#Test: self::assertSame('', (string) $result);
```

### Immutability check

<!-- test:totitle-immutable -->
```php
use Orryv\XString;

$original = XString::new('mutable string');
$title = $original->toTitle();

#Test: self::assertSame('mutable string', (string) $original);
#Test: self::assertSame('Mutable String', (string) $title);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toTitle` | `public function toTitle(): self` — Convert the string to Title Case while preserving separators and mode. |
