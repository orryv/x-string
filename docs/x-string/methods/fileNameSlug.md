# XString::fileNameSlug()

## Table of Contents
- [XString::fileNameSlug()](#xstringfilenameslug)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Basic document name sanitising](#basic-document-name-sanitising)
    - [Multi-extension archives stay intact](#multi-extension-archives-stay-intact)
    - [Reserved path characters are stripped](#reserved-path-characters-are-stripped)
    - [Using a custom separator](#using-a-custom-separator)
    - [Empty input yields an empty filename](#empty-input-yields-an-empty-filename)
    - [Separator must be non-empty](#separator-must-be-non-empty)
    - [Original instance remains unchanged](#original-instance-remains-unchanged)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function fileNameSlug(Newline|HtmlTag|string $separator = '-'): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Normalises the string into a filesystem-friendly *filename slug*. The method transliterates to ASCII where possible, lowercases
 the value, replaces disallowed characters with the chosen separator, collapses duplicate separators, and removes leading/trailin
g separators and dots to avoid hidden or relative-path results. Periods inside the name are preserved so existing extensions (inc
luding multi-part ones) stay intact.

## Important notes and considerations

- **Filesystem safe.** Characters forbidden in common filesystems (`/`, `\\`, `:`, `*`, `?`, `"`, `<`, `>`, `|`) are stripped.
- **Separator validation.** An empty separator throws an `InvalidArgumentException`.
- **Mode/encoding preserved.** The new `XString` retains the original mode and encoding configuration.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$separator` | `Newline\|HtmlTag\|string` | `'-'` | String inserted where invalid filename characters were found. Must not normalise to an empty string. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` containing the sanitised filename. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | The `$separator` normalises to an empty string. |

## Examples

### Basic document name sanitising

<!-- test:file-name-slug-basic -->
```php
use Orryv\XString;

$original = XString::new('Quarterly Report.xlsx');
$result = $original->fileNameSlug();

#Test: self::assertSame('quarterly-report.xlsx', (string) $result);
```

### Multi-extension archives stay intact

<!-- test:file-name-slug-multi-extension -->
```php
use Orryv\XString;

$original = XString::new('Archive.backup.TAR.GZ');
$result = $original->fileNameSlug();

#Test: self::assertSame('archive.backup.tar.gz', (string) $result);
```

### Reserved path characters are stripped

<!-- test:file-name-slug-reserved -->
```php
use Orryv\XString;

$original = XString::new('..\\Reports/2024:Q1*Summary?.pdf');
$result = $original->fileNameSlug();

#Test: self::assertSame('reports-2024-q1-summary.pdf', (string) $result);
```

### Using a custom separator

<!-- test:file-name-slug-custom-separator -->
```php
use Orryv\XString;

$original = XString::new('Vacation Photo 01.JPG');
$result = $original->fileNameSlug('_');

#Test: self::assertSame('vacation_photo_01.jpg', (string) $result);
```

### Empty input yields an empty filename

<!-- test:file-name-slug-empty -->
```php
use Orryv\XString;

$original = XString::new('');
$result = $original->fileNameSlug();

#Test: self::assertSame('', (string) $result);
```

### Separator must be non-empty

<!-- test:file-name-slug-empty-separator -->
```php
use InvalidArgumentException;
use Orryv\XString;

$original = XString::new('report.docx');

#Test: $this->expectException(InvalidArgumentException::class);
$original->fileNameSlug('');
```

### Original instance remains unchanged

<!-- test:file-name-slug-immutable -->
```php
use Orryv\XString;

$original = XString::new('Project Plan v1.2.doc');
$result = $original->fileNameSlug();

#Test: self::assertSame('Project Plan v1.2.doc', (string) $original);
#Test: self::assertSame('project-plan-v1.2.doc', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::fileNameSlug` | `public function fileNameSlug(Newline\|HtmlTag|string $separator = '-'): self` — Build a filesystem-safe filename while preserving extensions and collapsing invalid character runs. |
