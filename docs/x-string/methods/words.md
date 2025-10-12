# XString::words()

## Table of Contents
- [XString::words()](#xstringwords)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Split whitespace separated words](#split-whitespace-separated-words)
    - [Handle mixed whitespace characters](#handle-mixed-whitespace-characters)
    - [Trim punctuation from each word](#trim-punctuation-from-each-word)
    - [Limit the number of words](#limit-the-number-of-words)
    - [Works after switching modes](#works-after-switching-modes)
    - [Trim removes punctuation-only tokens](#trim-removes-punctuation-only-tokens)
    - [Empty strings return no words](#empty-strings-return-no-words)
    - [Reject invalid limits](#reject-invalid-limits-2)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function words(bool $trim = false, ?int $limit = null): array
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✗ | Public |

## Description

Tokenises the current string into a list of words using Unicode-aware whitespace detection. Optional trimming removes common
leading and trailing punctuation from each token. The method returns a PHP array and never modifies the original `XString`
instance.

## Important notes and considerations

- **Whitespace aware.** Uses a Unicode-aware regular expression so spaces, tabs, newlines, and other separator characters split words.
- **Optional trimming.** When `$trim` is `true`, punctuation characters like quotes, commas, or brackets are stripped from both ends of each word.
- **Limit behaviour.** `$limit` caps the number of returned words; the last element includes the remainder of the string when the limit is reached.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$trim` | `bool` | `false` | Strip common punctuation characters from the start and end of each word. |
| `$limit` | `?int` | `null` | Maximum number of words to return. Must be at least 1 when provided. |

## Returns

| Return Type | Description |
| --- | --- |
| `list<string>` | Ordered list of extracted words. Empty strings are omitted. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | `$limit` is provided but lower than 1. |

## Examples

### Split whitespace separated words

<!-- test:words-basic -->
```php
use Orryv\XString;

$words = XString::new('lorem ipsum dolor')->words();

#Test: self::assertSame(['lorem', 'ipsum', 'dolor'], $words);
```

### Handle mixed whitespace characters

<!-- test:words-mixed-whitespace -->
```php
use Orryv\XString;

$words = XString::new("  alpha\tbeta\n\u{00A0}gamma  ")->words();

#Test: self::assertSame(['alpha', 'beta', 'gamma'], $words);
```

### Trim punctuation from each word

<!-- test:words-trim -->
```php
use Orryv\XString;

$words = XString::new("'Hello,' \"world!\"")->words(trim: true);

#Test: self::assertSame(['Hello', 'world'], $words);
```

### Limit the number of words

<!-- test:words-limit -->
```php
use Orryv\XString;

$words = XString::new('one two three four')->words(limit: 3);

#Test: self::assertSame(['one', 'two', 'three four'], $words);
```

### Works after switching modes

<!-- test:words-mode -->
```php
use Orryv\XString;

$xstring = XString::new('Ångström är här')->withMode('bytes');
$words = $xstring->words();

#Test: self::assertSame(['Ångström', 'är', 'här'], $words);
```

### Trim removes punctuation-only tokens

<!-- test:words-trim-empty -->
```php
use Orryv\XString;

$words = XString::new('... --- ...')->words(trim: true);

#Test: self::assertSame([], $words);
```

### Empty strings return no words

<!-- test:words-empty -->
```php
use Orryv\XString;

$words = XString::new('')->words();

#Test: self::assertSame([], $words);
```

### Reject invalid limits

<!-- test:words-invalid-limit -->
```php
use InvalidArgumentException;
use Orryv\XString;

$xstring = XString::new('alpha beta');

#Test: $this->expectException(InvalidArgumentException::class);
$xstring->words(limit: 0);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::words` | `public function words(bool $trim = false, ?int $limit = null): array` — Tokenise the string into words with optional punctuation trimming and limit control. |
