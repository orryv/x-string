# XString::stripTags()

## Table of Contents
- [XString::stripTags()](#xstringstriptags)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Strip all HTML tags](#strip-all-html-tags)
    - [Allow specific tags using a string whitelist](#allow-specific-tags-using-a-string-whitelist)
    - [Allow tags using HtmlTag helpers](#allow-tags-using-htmltag-helpers)
    - [Allow multiple tags at once](#allow-multiple-tags-at-once)
    - [Self-closing tags can be preserved](#self-closing-tags-can-be-preserved)
    - [Reject nested allowed-tag arrays](#reject-nested-allowed-tag-arrays)
    - [Original instance remains unchanged](#original-instance-remains-unchanged)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function stripTags(Newline|HtmlTag|Regex|string|array<Newline|HtmlTag|Regex|string> $allowed_tags = ''): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Removes all HTML and PHP tags from the current string while optionally whitelisting tags that should remain. You can provide
allowed tags as raw tag names, strings containing `<tag>` snippets, or `HtmlTag` helper instances. Internally the method delegates
to PHP's `strip_tags()` while normalising the allowed tag list to accept the same rich inputs supported across `XString`.

## Important notes and considerations

- **Flexible whitelist input.** Accepts strings, arrays, and `HtmlTag` instances when specifying allowed tags. Plain names are
  automatically wrapped (e.g. `'p'` becomes `<p>`).
- **Attributes are removed.** When a tag is preserved only its tag wrapper remains; attributes are stripped by `strip_tags()`.
- **Immutable clone.** The original `XString` is never modified.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$allowed_tags` | `Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string>` | `''` | Tags that should not be stripped. Accepts plain names (e.g. `'p'`), tag strings (e.g. `'<p>'`), or `HtmlTag` instances. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` instance containing the tag-stripped value. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | The `$allowed_tags` parameter contains nested arrays. |

## Examples

### Strip all HTML tags

<!-- test:strip-tags-basic -->
```php
use Orryv\XString;

$value = XString::new('<div>Hello <strong>World</strong></div>');
$result = $value->stripTags();

#Test: self::assertSame('Hello World', (string) $result);
```

### Allow specific tags using a string whitelist

<!-- test:strip-tags-allow-string -->
```php
use Orryv\XString;

$value = XString::new('<p>Intro</p><span class="note">note</span>');
$result = $value->stripTags('p');

#Test: self::assertSame('<p>Intro</p>note', (string) $result);
```

### Allow tags using HtmlTag helpers

<!-- test:strip-tags-allow-htmltag -->
```php
use Orryv\XString;
use Orryv\XString\HtmlTag;

$value = XString::new('Keep <em>emphasis</em> but remove <strong>strong</strong>');
$result = $value->stripTags(HtmlTag::new('em'));

#Test: self::assertSame('Keep <em>emphasis</em> but remove strong', (string) $result);
```

### Allow multiple tags at once

<!-- test:strip-tags-allow-array -->
```php
use Orryv\XString;

$value = XString::new('<p><em>Rich</em> <strong>text</strong></p>');
$result = $value->stripTags(['p', 'em']);

#Test: self::assertSame('<p><em>Rich</em> text</p>', (string) $result);
```

### Self-closing tags can be preserved

<!-- test:strip-tags-allow-self-closing -->
```php
use Orryv\XString;

$value = XString::new('Line one<br/>Line two<hr/>Done');
$result = $value->stripTags(['br']);

#Test: self::assertSame('Line one<br/>Line twoDone', (string) $result);
```

### Reject nested allowed-tag arrays

<!-- test:strip-tags-nested-array -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('<p>content</p>');

#Test: $this->expectException(InvalidArgumentException::class);
$value->stripTags([['p']]);
```

### Original instance remains unchanged

<!-- test:strip-tags-immutable -->
```php
use Orryv\XString;

$original = XString::new('<span>note</span>');
$original->stripTags();

#Test: self::assertSame('<span>note</span>', (string) $original);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::stripTags` | `public function stripTags(Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string> $allowed_tags = ''): self` — Remove tags while optionally preserving a whitelist of allowed tags. |
