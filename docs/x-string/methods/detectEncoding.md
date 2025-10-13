# XString::detectEncoding()

## Table of Contents
- [XString::detectEncoding()](#xstringdetectencoding)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Detect UTF-8](#detect-utf-8)
    - [Provide a custom priority list](#provide-a-custom-priority-list)
    - [Return false when nothing matches](#return-false-when-nothing-matches)
    - [Empty lists are rejected](#empty-lists-are-rejected)
    - [Original instance remains unchanged](#original-instance-remains-unchanged-3)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function detectEncoding(array $encodings = ['UTF-8', 'ISO-8859-1', 'ASCII']): string|false
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✗ | Public |

## Description

Attempts to determine the string's encoding by checking the provided list in order. Uses `mb_detect_encoding()` when available,
falling back to a defensive `iconv()` round-trip for each candidate. Returns `false` when none of the supplied encodings match.

## Important notes and considerations

- **Order matters.** The first matching encoding is returned, so list more specific encodings before generic ones.
- **Deduplicated candidates.** Duplicate entries are removed before detection, preventing redundant work.
- **Validation.** An empty `$encodings` array raises `InvalidArgumentException`.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$encodings` | `array<string>` | `['UTF-8', 'ISO-8859-1', 'ASCII']` | Candidate encodings to test, in priority order. |

## Returns

| Return Type | Description |
| --- | --- |
| `string` | The first matching encoding from the list. |
| `false` | No candidate matched the string. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | `$encodings` is empty or contains an empty entry. |

## Examples

### Detect UTF-8

<!-- test:detect-encoding-utf8 -->
```php
use Orryv\XString;

$value = XString::new('Café');
$result = $value->detectEncoding();

#Test: self::assertSame('UTF-8', $result);
```

### Provide a custom priority list

<!-- test:detect-encoding-custom -->
```php
use Orryv\XString;

$value = XString::new("Plain ASCII text");
$result = $value->detectEncoding(['ISO-8859-1', 'ASCII']);

#Test: self::assertSame('ISO-8859-1', $result);
```

### Return false when nothing matches

<!-- test:detect-encoding-false -->
```php
use Orryv\XString;

$value = XString::new("\x00\x81\xFF");
$result = $value->detectEncoding(['ASCII']);

#Test: self::assertFalse($result);
```

### Empty lists are rejected

<!-- test:detect-encoding-empty -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('content');

#Test: $this->expectException(InvalidArgumentException::class);
$value->detectEncoding([]);
```

### Original instance remains unchanged

<!-- test:detect-encoding-immutability -->
```php
use Orryv\XString;

$value = XString::new('data');
$value->detectEncoding();

#Test: self::assertSame('data', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::detectEncoding` | `public function detectEncoding(array $encodings = ['UTF-8', 'ISO-8859-1', 'ASCII']): string|false` — Probe the string against a priority-ordered list of encodings, returning the first match or `false` when none match. |
