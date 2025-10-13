# XString::nl2br()

## Table of Contents
- [XString::nl2br()](#xstringnl2br)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Convert Unix newlines to XHTML breaks](#convert-unix-newlines-to-xhtml-breaks)
    - [Emit HTML4 style breaks when requested](#emit-html4-style-breaks-when-requested)
    - [Windows newlines become consistent breaks](#windows-newlines-become-consistent-breaks)
    - [Consecutive blank lines are preserved](#consecutive-blank-lines-are-preserved)
    - [Mode switches still encode correctly](#mode-switches-still-encode-correctly)
    - [Empty strings stay empty](#empty-strings-stay-empty-3)
    - [Original instance remains unchanged](#original-instance-remains-unchanged-3)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function nl2br(bool $is_xhtml = true): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Transforms newline characters into HTML `<br>` elements. By default the output follows XHTML conventions (`<br />`), but you can opt into the HTML4 style (`<br>`) via the `$is_xhtml` flag. Existing characters are left untouched and a fresh immutable `XString` is returned.

## Important notes and considerations

- **Supports multiple newline styles.** Handles `\n`, `\r\n`, and `\r` transparently.
- **Optional XHTML compliance.** Pass `false` when generating legacy HTML without self-closing tags.
- **Immutable operation.** The original string is left exactly as-is.

## Parameters

| Parameter | Type | Default | Description |
| --- | --- | --- | --- |
| `$is_xhtml` | `bool` | `true` | Whether to use `<br />` (true) or `<br>` (false) in the result. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | New `XString` where newline characters are replaced by `<br>` tags. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Convert Unix newlines to XHTML breaks

<!-- test:nl2br-unix -->
```php
use Orryv\XString;

$result = XString::new("Line 1\nLine 2")->nl2br();

#Test: self::assertSame("Line 1<br />\nLine 2", (string) $result);
```

### Emit HTML4 style breaks when requested

<!-- test:nl2br-html4 -->
```php
use Orryv\XString;

$result = XString::new("One\nTwo")->nl2br(false);

#Test: self::assertSame("One<br>\nTwo", (string) $result);
```

### Windows newlines become consistent breaks

<!-- test:nl2br-windows -->
```php
use Orryv\XString;

$result = XString::new("First\r\nSecond")->nl2br();

#Test: self::assertSame("First<br />\r\nSecond", (string) $result);
```

### Consecutive blank lines are preserved

<!-- test:nl2br-consecutive -->
```php
use Orryv\XString;

$result = XString::new("Top\n\nBottom")->nl2br();

#Test: self::assertSame("Top<br />\n<br />\nBottom", (string) $result);
```

### Mode switches still encode correctly

<!-- test:nl2br-bytes-mode -->
```php
use Orryv\XString;

$value = XString::new("Σ\nσ")->withMode('bytes');
$result = $value->nl2br();

#Test: self::assertSame("Σ<br />\nσ", (string) $result);
```

### Empty strings stay empty

<!-- test:nl2br-empty -->
```php
use Orryv\XString;

$result = XString::new('')->nl2br();

#Test: self::assertSame('', (string) $result);
```

### Original instance remains unchanged

<!-- test:nl2br-immutable -->
```php
use Orryv\XString;

$value = XString::new("Keep\nOriginal");
$value->nl2br();

#Test: self::assertSame("Keep\nOriginal", (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::nl2br` | `public function nl2br(bool $is_xhtml = true): self` — Convert newline characters to `<br>` tags using XHTML or HTML4 formatting without mutating the original string. |
