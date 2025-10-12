# XString::sentenceCount()

## Table of Contents
- [XString::sentenceCount()](#xstringsentencecount)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Count sentences with mixed punctuation](#count-sentences-with-mixed-punctuation)
    - [Abbreviations do not terminate sentences](#abbreviations-do-not-terminate-sentences)
    - [Fallback to newline separation when punctuation is absent](#fallback-to-newline-separation-when-punctuation-is-absent)
    - [Handle ellipses and mixed punctuation](#handle-ellipses-and-mixed-punctuation)
    - [Empty strings yield zero](#empty-strings-yield-zero)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function sentenceCount(): int
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✗ | Public |

## Description

Estimates how many sentences are present in the string. Sentence boundaries are detected via terminal punctuation (`.`, `!`, `?`, `…`)
with awareness for common abbreviations and initials so that constructs like “Dr.” or “J. K. Rowling” do not end the count prematurely.
When no punctuation exists, the method falls back to counting non-empty newline-separated blocks.

## Important notes and considerations

- **Abbreviation aware.** Common abbreviations and single-letter initials are ignored as sentence terminators.
- **Supports ellipses and closing quotes.** Punctuation clusters such as `?!` or `..."` are treated as a single boundary.
- **Graceful fallback.** If no punctuation is found, non-empty lines are counted instead, ensuring multi-line prose is still recognised.
- **Non-mutating.** The original string remains unchanged.

## Parameters

This method does not accept parameters.

## Returns

| Return Type | Description |
| --- | --- |
| `int` | Number of detected sentences. |

## Thrown exceptions

This method does not throw exceptions.

## Examples

### Count sentences with mixed punctuation

<!-- test:sentence-count-mixed -->
```php
use Orryv\XString;

$paragraph = XString::new('One sentence. Two more! Is it three? Yes.');

#Test: self::assertSame(4, $paragraph->sentenceCount());
```

### Abbreviations do not terminate sentences

<!-- test:sentence-count-abbreviation -->
```php
use Orryv\XString;

$text = XString::new('He met Dr. Strange. They spoke with Mr. Smith Jr. about the plan.');

#Test: self::assertSame(2, $text->sentenceCount());
```

### Fallback to newline separation when punctuation is absent

<!-- test:sentence-count-newlines -->
```php
use Orryv\XString;

$note = XString::new("Line one without punctuation\nSecond line continues");

#Test: self::assertSame(2, $note->sentenceCount());
```

### Handle ellipses and mixed punctuation

<!-- test:sentence-count-ellipses -->
```php
use Orryv\XString;

$value = XString::new('Wait... still here?! Absolutely.');

#Test: self::assertSame(3, $value->sentenceCount());
```

### Empty strings yield zero

<!-- test:sentence-count-empty -->
```php
use Orryv\XString;

#Test: self::assertSame(0, XString::new('')->sentenceCount());
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::sentenceCount` | `public function sentenceCount(): int` — Estimate the number of sentences, handling abbreviations, ellipses, and newline-only prose gracefully. |
