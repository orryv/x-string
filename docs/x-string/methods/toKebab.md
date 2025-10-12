# XString::toKebab()

## Table of Contents
- [XString::toKebab()](#xstringtokebab)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Convert a space separated title](#convert-a-space-separated-title)
    - [Handle camelCase source strings](#handle-camelcase-source-strings)
    - [Collapse mixed separators](#collapse-mixed-separators)
    - [Byte mode keeps raw length accounting](#byte-mode-keeps-raw-length-accounting)
    - [Grapheme mode keeps emoji intact](#grapheme-mode-keeps-emoji-intact)
    - [Empty strings stay empty](#empty-strings-stay-empty)
    - [Immutability check](#immutability-check)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toKebab(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Transforms the current string into **kebab-case** by lowercasing words, inserting hyphens between them, and respecting the
current instance's encoding. Word boundaries are detected from whitespace, punctuation such as underscores or dashes, and
camelCase/PascalCase transitions.

## Important notes and considerations

- **Hyphen output only.** Regardless of the original separators, the result always uses `-` between words.
- **Encoding aware.** Uses multibyte aware lowercasing so accented characters remain correct.
- **Mode preserved.** The returned `XString` keeps the same mode and encoding as the source instance.

## Parameters

This method does not accept parameters.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` converted to kebab-case. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Convert a space separated title

<!-- test:tokebab-basic -->
```php
use Orryv\XString;

$title = XString::new('Hello World Example');
$result = $title->toKebab();

#Test: self::assertSame('hello-world-example', (string) $result);
#Test: self::assertSame('Hello World Example', (string) $title);
```

### Handle camelCase source strings

<!-- test:tokebab-camel -->
```php
use Orryv\XString;

$value = XString::new('XMLHttpRequestParser');
$result = $value->toKebab();

#Test: self::assertSame('xml-http-request-parser', (string) $result);
```

### Collapse mixed separators

<!-- test:tokebab-mixed-separators -->
```php
use Orryv\XString;

$value = XString::new('double--dash__value');
$result = $value->toKebab();

#Test: self::assertSame('double-dash-value', (string) $result);
```

### Byte mode keeps raw length accounting

<!-- test:tokebab-byte-mode -->
```php
use Orryv\XString;

$value = XString::new('Ångström Growth')->withMode('bytes');
$result = $value->toKebab();

#Test: self::assertSame('ångström-growth', (string) $result);
#Test: self::assertSame(17, $result->length());
```

### Grapheme mode keeps emoji intact

<!-- test:tokebab-grapheme-mode -->
```php
use Orryv\XString;

$value = XString::new('🙂 Smile')->withMode('graphemes');
$result = $value->toKebab();

#Test: self::assertSame('🙂-smile', (string) $result);
#Test: self::assertSame(7, $result->length());
```

### Empty strings stay empty

<!-- test:tokebab-empty -->
```php
use Orryv\XString;

$empty = XString::new('');
$result = $empty->toKebab();

#Test: self::assertSame('', (string) $result);
```

### Immutability check

<!-- test:tokebab-immutable -->
```php
use Orryv\XString;

$original = XString::new('Mutable Value');
$converted = $original->toKebab();

#Test: self::assertSame('Mutable Value', (string) $original);
#Test: self::assertSame('mutable-value', (string) $converted);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toKebab` | `public function toKebab(): self` — Convert the string to kebab-case while preserving the original instance. |
