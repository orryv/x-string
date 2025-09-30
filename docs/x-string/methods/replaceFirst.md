# XString::replaceFirst()

## Table of Contents
- [XString::replaceFirst()](#xstringreplacefirst)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Replace the first occurrence of a substring](#replace-the-first-occurrence-of-a-substring)
    - [Replace the first match from multiple candidates](#replace-the-first-match-from-multiple-candidates)
    - [Immutability check](#immutability-check)
    - [No change when the substring is absent](#no-change-when-the-substring-is-absent)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function replaceFirst(
    HtmlTag|Newline|Regex|Stringable|string|array $search,
    HtmlTag|Newline|Regex|Stringable|string $replace
): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Creates a new immutable `XString` by replacing only the first occurrence of the provided search string(s) with the given
replacement. Internally it delegates to [`replace()`](./replace.md) using a limit of one, so all validation and supported
input types are identical.

## Important notes and considerations

- **Immutability.** A new instance is returned; the original value remains unchanged.
- **Delegates to `replace()`.** All validation rules (including empty search checks) are inherited from
  [`XString::replace()`](./replace.md).
- **Multiple candidates.** When an array of search strings is provided, candidates are evaluated in the supplied order;
  only the first match is replaced.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$search` | `HtmlTag\|Newline\|Regex\|Stringable\|string\|array` | — | One or more values to search for. |
| `$replace` | `HtmlTag\|Newline\|Regex\|Stringable\|string` | — | Replacement fragment applied to the first match. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` with at most one replacement applied. |

## Thrown exceptions

See [`XString::replace()`](./replace.md#thrown-exceptions) for the list of possible exceptions (they propagate unchanged).

## Examples

### Replace the first occurrence of a substring

<!-- test:replace-first-basic -->
```php
use Orryv\XString;

$xstring = XString::new('foo bar foo bar');
$result = $xstring->replaceFirst('foo', 'baz');
#Test: self::assertSame('baz bar foo bar', (string) $result);
```

### Replace the first match from multiple candidates

<!-- test:replace-first-multiple -->
```php
use Orryv\XString;

$xstring = XString::new('alpha beta gamma');
$result = $xstring->replaceFirst(['delta', 'beta', 'gamma'], 'theta');
#Test: self::assertSame('alpha theta gamma', (string) $result);
```

### Immutability check

<!-- test:replace-first-immutability -->
```php
use Orryv\XString;

$xstring = XString::new('repeat me');
$replaced = $xstring->replaceFirst('repeat', 'echo');
#Test: self::assertSame('repeat me', (string) $xstring);
#Test: self::assertSame('echo me', (string) $replaced);
```

### No change when the substring is absent

<!-- test:replace-first-no-match -->
```php
use Orryv\XString;

$xstring = XString::new('unchanged');
$result = $xstring->replaceFirst('missing', 'found');
#Test: self::assertSame('unchanged', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::replaceFirst` | `public function replaceFirst(HtmlTag\|Newline\|Regex\|Stringable\|string\|array $search, HtmlTag\|Newline\|Regex\|Stringable\|string $replace): self` — Replace only the first occurrence of the provided search value(s). |
