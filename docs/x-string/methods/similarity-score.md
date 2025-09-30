# XString::similarityScore()

## Table of Contents
- [XString::similarityScore()](#xstringsimilarityscore)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Algorithms](#algorithms)
    - [`levenshtein`](#levenshtein)
    - [`damerau-levenshtein`](#damerau-levenshtein)
    - [`jaro-winkler`](#jaro-winkler)
    - [`lcs-myers`](#lcs-myers)
    - [`ratcliff-obershelp`](#ratcliff-obershelp)
    - [`jaccard`](#jaccard)
    - [`sorensen-dice`](#sorensen-dice)
    - [`cosine-ngrams`](#cosine-ngrams)
    - [`monge-elkan`](#monge-elkan)
    - [`soft-tfidf`](#soft-tfidf)
    - [`github-style`](#github-style)
  - [Examples](#examples)
    - [Exact match (Levenshtein)](#exact-match-levenshtein)
    - [Exact match (Damerau-Levenshtein)](#exact-match-damerau-levenshtein)
    - [Exact match (Jaro-Winkler)](#exact-match-jaro-winkler)
    - [Exact match (LCS/Myers)](#exact-match-lcsmyers)
    - [Exact match (Ratcliff-Obershelp)](#exact-match-ratcliff-obershelp)
    - [Exact match (Jaccard)](#exact-match-jaccard)
    - [Exact match (Sørensen-Dice)](#exact-match-sørensen-dice)
    - [Exact match (Cosine n-grams)](#exact-match-cosine-n-grams)
    - [Exact match (Monge-Elkan)](#exact-match-monge-elkan)
    - [Exact match (Soft TF-IDF)](#exact-match-soft-tf-idf)
    - [Exact match (GitHub style)](#exact-match-github-style)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function similarityScore(
    Newline|HtmlTag|Regex|string $comparison,
    string $algorithm = 'github-style',
    array $options = []
): float
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | — (pure calculation) | Public |

## Description

Calculates a normalized similarity score between the current immutable `XString` value and another string-like input. The return value is a floating-point ratio in the inclusive range **[0.0, 1.0]**, where `1.0` denotes identical strings for the chosen algorithm. A wide selection of string similarity and fuzzy-matching algorithms is available, ranging from classic edit distance to token-based and cosine-based measures.

The method accepts any input supported by [`XString::new()`](./new.md)—plain strings, `Stringable` instances (including `Newline`, `HtmlTag`, and `Regex`), or arrays of such fragments. Inputs are normalized with the same rules as `XString::new()` before comparison. Algorithm-specific behaviour and tunable options are detailed in the [Algorithms](#algorithms) section.

## Important notes and considerations

- **Deterministic ratios.** Given the same inputs, algorithm, and options, the returned score is deterministic and side-effect free.
- **Normalization pipeline.** Before comparison, inputs can be normalized (case folding, whitespace collapsing, punctuation stripping) according to the provided options.
- **Immutability friendly.** The method does not mutate the `XString` instance; it merely reads its value and returns a primitive `float`.
- **Mode awareness.** Tokenization honours the instance's current mode (`bytes`, `codepoints`, or `graphemes`) when splitting or iterating characters.
- **Threshold filtering.** If you set `threshold`, the method still returns the computed ratio but guarantees that values below the threshold are coerced to `0.0`.
- **Secondary metrics.** Algorithms such as `monge-elkan` and `soft-tfidf` rely on a secondary similarity metric (default `jaro-winkler`). You can select any other supported algorithm name via the `secondary_metric` option.

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$comparison` | — | `Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string>` | The value to compare with the current string. Arrays are concatenated before processing. |
| `$algorithm` | `'github-style'` | `string` | Name of the similarity algorithm. See [Algorithms](#algorithms) for the supported values. |
| `$options` | `[]` | `array<string, mixed>` | Algorithm-specific configuration. Unrecognised keys are ignored. |

## Returns

| Return Type | Description |
| --- | --- |
| `float` | Normalized similarity ratio in the range `[0.0, 1.0]`. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$algorithm` is not one of the supported names or an option value is invalid. |

## Algorithms

Unless otherwise stated, algorithms respect the shared options:

- `granularity`: `'token'` (default), `'word'`, or `'character'` — controls how the strings are tokenized before algorithm-specific processing.
- `case_sensitive`: `false` by default — apply case-sensitive comparisons when `true`.
- `normalize_whitespace`: `true` by default — collapse consecutive whitespace and trim around tokens.
- `strip_punctuation`: `true` by default (except when `granularity === 'character'`) — removes punctuation during tokenization.
- `threshold`: `0.0` by default — force results below this ratio to zero.

### `levenshtein`

Computes the classic Levenshtein edit-distance normalized by the maximum length of the two strings. Best suited for small typo detection.

- **Default options:** `{}` (inherits the shared defaults).
- **Additional options:**
  - `transposition_cost` (default `2`): weight assigned when swapping two characters is simulated.

### `damerau-levenshtein`

Enhances Levenshtein by treating adjacent transpositions as a single edit. Normalization matches the Levenshtein ratio approach.

- **Default options:** `{}` (shared defaults).
- **Additional options:**
  - `transposition_cost` (default `1`): cost assigned to a neighbouring swap.

### `jaro-winkler`

Measures the similarity based on matching characters within a sliding window and rewards a common prefix.

- **Default options:** `{ 'prefix_scale' => 0.1, 'prefix_limit' => 4 }` in addition to shared defaults.
- **Additional options:**
  - `prefix_scale`: weight per matching prefix character (default `0.1`).
  - `prefix_limit`: maximum prefix length considered (default `4`).

### `lcs-myers`

Uses Myers' diff algorithm to compute the Longest Common Subsequence, returning `2 * LCS / (|A| + |B|)`.

- **Default options:** `{}` (shared defaults).
- **Additional options:**
  - `weight_common_prefix` (default `0.0`): optional boost applied to shared prefixes.

### `ratcliff-obershelp`

Also known as Gestalt pattern matching; recursively finds the longest common substring and sums the matches.

- **Default options:** `{}` (shared defaults).
- **Additional options:**
  - `symmetric` (default `true`): ensures the score is symmetrical for both inputs.

### `jaccard`

Compares the two token sets and returns the Jaccard index `|A ∩ B| / |A ∪ B|`. Ignores duplicate tokens.

- **Default options:** `{}` (shared defaults).
- **Additional options:**
  - `token_set` (default `true`): if set to `false`, multiplicities are preserved.

### `sorensen-dice`

Calculates `2|A ∩ B| / (|A| + |B|)` over token sets, giving slightly higher scores than Jaccard for partial matches.

- **Default options:** `{}` (shared defaults).
- **Additional options:**
  - `token_set` (default `true`): treat input as sets instead of multisets when `true`.

### `cosine-ngrams`

Builds n-gram frequency vectors and computes the cosine similarity between them.

- **Default options:** `{ 'n' => 3, 'weighting' => 'binary' }` alongside shared defaults.
- **Additional options:**
  - `n`: n-gram length (default `3`).
  - `weighting`: `'binary'`, `'tf'`, `'log'`, `'augmented'`, `'double-normalization-0.5'`, or `'tfidf'` (default `'binary'`).

### `monge-elkan`

Token-based measure that compares each token in one string with the best-matching token in the other using a secondary metric.

- **Default options:** `{ 'secondary_metric' => 'jaro-winkler', 'tau' => 0.9 }` with shared defaults.
- **Additional options:**
  - `secondary_metric`: algorithm name used per token comparison (default `'jaro-winkler'`).
  - `tau`: minimum ratio required to keep a secondary match (default `0.9`).

### `soft-tfidf`

Combines TF-IDF weighting with soft token equality using a secondary metric and threshold.

- **Default options:** `{ 'secondary_metric' => 'jaro-winkler', 'tau' => 0.9, 'weighting' => 'tfidf' }` plus shared defaults.
- **Additional options:**
  - `secondary_metric`: algorithm name for token comparisons (default `'jaro-winkler'`).
  - `tau`: minimum ratio to count a match (default `0.9`).
  - `weighting`: same options as `cosine-ngrams` (default `'tfidf'`).

### `github-style`

Inspired by GitHub's fuzzy matching. Computes a token-level LCS ratio and applies a small prefix bonus for diff-like results.

- **Default options:** `{ 'prefix_scale' => 0.05, 'prefix_limit' => 3 }` plus shared defaults.
- **Additional options:**
  - `prefix_scale`: multiplier for the shared prefix bonus (default `0.05`).
  - `prefix_limit`: maximum prefix length considered (default `3`).

## Examples

### Exact match (Levenshtein)

<!-- test:similarity-levenshtein -->
```php
use Orryv\XString;

$score = XString::new('kitten')->similarityScore('kitten', 'levenshtein');
#Test: self::assertSame(1.0, $score);
```

### Exact match (Damerau-Levenshtein)

<!-- test:similarity-damerau -->
```php
use Orryv\XString;

$score = XString::new('cares')->similarityScore('cares', 'damerau-levenshtein');
#Test: self::assertSame(1.0, $score);
```

### Exact match (Jaro-Winkler)

<!-- test:similarity-jaro-winkler -->
```php
use Orryv\XString;

$score = XString::new('MARTHA')->similarityScore('MARTHA', 'jaro-winkler');
#Test: self::assertSame(1.0, $score);
```

### Exact match (LCS/Myers)

<!-- test:similarity-lcs -->
```php
use Orryv\XString;

$score = XString::new('diff this')->similarityScore('diff this', 'lcs-myers');
#Test: self::assertSame(1.0, $score);
```

### Exact match (Ratcliff-Obershelp)

<!-- test:similarity-ratcliff -->
```php
use Orryv\XString;

$score = XString::new('pattern')->similarityScore('pattern', 'ratcliff-obershelp');
#Test: self::assertSame(1.0, $score);
```

### Exact match (Jaccard)

<!-- test:similarity-jaccard -->
```php
use Orryv\XString;

$score = XString::new('foo bar baz')->similarityScore('foo bar baz', 'jaccard');
#Test: self::assertSame(1.0, $score);
```

### Exact match (Sørensen-Dice)

<!-- test:similarity-sorensen -->
```php
use Orryv\XString;

$score = XString::new('quick brown fox')->similarityScore('quick brown fox', 'sorensen-dice');
#Test: self::assertSame(1.0, $score);
```

### Exact match (Cosine n-grams)

<!-- test:similarity-cosine -->
```php
use Orryv\XString;

$score = XString::new('vector space')->similarityScore('vector space', 'cosine-ngrams', ['n' => 2]);
#Test: self::assertSame(1.0, $score);
```

### Exact match (Monge-Elkan)

<!-- test:similarity-monge-elkan -->
```php
use Orryv\XString;

$score = XString::new('data science')->similarityScore('data science', 'monge-elkan');
#Test: self::assertSame(1.0, $score);
```

### Exact match (Soft TF-IDF)

<!-- test:similarity-soft-tfidf -->
```php
use Orryv\XString;

$score = XString::new('fuzzy logic')->similarityScore('fuzzy logic', 'soft-tfidf');
#Test: self::assertSame(1.0, $score);
```

### Exact match (GitHub style)

<!-- test:similarity-github -->
```php
use Orryv\XString;

$score = XString::new('function similarityScore')->similarityScore('function similarityScore');
#Test: self::assertSame(1.0, $score);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::similarityScore` | `public function similarityScore(Newline|HtmlTag|Regex|string $comparison, string $algorithm = 'github-style', array $options = []): float` — Compute a normalized similarity ratio between this string and another using the selected fuzzy-matching algorithm. |
