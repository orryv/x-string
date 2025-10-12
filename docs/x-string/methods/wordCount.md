# XString::wordCount()

## Table of Contents
- [XString::wordCount()](#xstringwordcount)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Count words with punctuation](#count-words-with-punctuation)
    - [Collapse repeated whitespace](#collapse-repeated-whitespace)
    - [Newlines split words too](#newlines-split-words-too)
    - [Empty strings return zero](#empty-strings-return-zero)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function wordCount(): int
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✗ | Public |

## Description

Returns how many word tokens are present in the string. The counting logic mirrors [`words()`](words.md), splitting on any
Unicode whitespace separators while keeping punctuation attached to the surrounding tokens.

## Important notes and considerations

- **Whitespace-insensitive.** Consecutive spaces, tabs and other Unicode separators do not inflate the count.
- **Punctuation preserved.** Symbols such as commas or exclamation marks remain part of the surrounding word but still contribute
  to the total.
- **Non-mutating.** The source `XString` remains unchanged.

## Parameters

This method does not accept parameters.

## Returns

| Return Type | Description |
| --- | --- |
| `int` | Number of word tokens detected. |

## Thrown exceptions

This method does not throw exceptions.

## Examples

### Count words with punctuation

<!-- test:word-count-punctuation -->
```php
use Orryv\XString;

$sentence = XString::new('Hello, world! This is 2024.');

#Test: self::assertSame(5, $sentence->wordCount());
#Test: self::assertSame('Hello, world! This is 2024.', (string) $sentence);
```

### Collapse repeated whitespace

<!-- test:word-count-spacing -->
```php
use Orryv\XString;

$messy = XString::new("alpha\t\t beta    gamma\u{00A0}delta");

#Test: self::assertSame(4, $messy->wordCount());
#Test: self::assertSame(['alpha', 'beta', 'gamma', 'delta'], $messy->words(trim: true));
```

### Newlines split words too

<!-- test:word-count-newlines -->
```php
use Orryv\XString;

$text = XString::new("line one\nline two\rline three");

#Test: self::assertSame(6, $text->wordCount());
```

### Empty strings return zero

<!-- test:word-count-empty -->
```php
use Orryv\XString;

#Test: self::assertSame(0, XString::new('')->wordCount());
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::wordCount` | `public function wordCount(): int` — Count word tokens using the same Unicode-aware rules as `words()`. |
