# XString::toSafeFileName()

## Table of Contents
- [XString::toSafeFileName()](#xstringtosafefilename)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Handle reserved names and invalid characters](#handle-reserved-names-and-invalid-characters)
    - [Preserve Unicode while sanitising separators](#preserve-unicode-while-sanitising-separators)
    - [Collapse empty or special names](#collapse-empty-or-special-names-1)
    - [Whitespace-only values fallback to underscore](#whitespace-only-values-fallback-to-underscore)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toSafeFileName(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Generate a conservative, cross-platform safe filename by combining the sanitisation rules of Windows, Linux, and macOS.
Illegal characters are replaced with underscores, reserved device names are escaped, colons/slashes/backslashes are neutralised,
and empty values become `_`.

## Important notes and considerations

- **Cross-platform safety.** Covers Windows device names and illegal characters in addition to Unix/macOS restrictions.
- **Control removal.** ASCII control bytes are stripped.
- **Length guard.** Output is truncated to 255 bytes to fit common filesystem limits.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Portable filename safe for major operating systems. |

## Examples

### Handle reserved names and invalid characters

<!-- test:safe-filename-reserved -->
```php
use Orryv\XString;

$value = XString::new('CON?.txt');
$result = $value->toSafeFileName();

#Test: self::assertSame('CON_.txt', (string) $result);
```

### Preserve Unicode while sanitising separators

<!-- test:safe-filename-unicode -->
```php
use Orryv\XString;

$value = XString::new('Récap/2024.txt');
$result = $value->toSafeFileName();

#Test: self::assertSame('Récap_2024.txt', (string) $result);
```

### Collapse empty or special names

<!-- test:safe-filename-special -->
```php
use Orryv\XString;

$value = XString::new('..');
$result = $value->toSafeFileName();

#Test: self::assertSame('_', (string) $result);
```

### Whitespace-only values fallback to underscore

<!-- test:safe-filename-whitespace -->
```php
use Orryv\XString;

$value = XString::new("   ");
$result = $value->toSafeFileName();

#Test: self::assertSame('_', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toSafeFileName` | `public function toSafeFileName(): self` — Produce a conservative filename safe across Windows, Linux, and macOS by neutralising reserved characters/names and collapsing empty results to underscores. |
