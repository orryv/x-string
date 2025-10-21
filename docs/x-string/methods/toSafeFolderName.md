# XString::toSafeFolderName()

## Table of Contents
- [XString::toSafeFolderName()](#xstringtosafefoldername)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Handle reserved names and invalid characters](#handle-reserved-names-and-invalid-characters)
    - [Preserve Unicode while sanitising separators](#preserve-unicode-while-sanitising-separators)
    - [Collapse empty or special names](#collapse-empty-or-special-names)
    - [Whitespace-only values fallback to underscore](#whitespace-only-values-fallback-to-underscore)
    - [Original instance remains unchanged](#original-instance-remains-unchanged)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toSafeFolderName(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Generate a conservative, cross-platform safe folder name by combining the sanitisation rules of Windows, Linux, and macOS.
Illegal characters are replaced with underscores, reserved device names are escaped, colons/slashes/backslashes are neutralised,
and empty values become `_`.

## Important notes and considerations

- **Cross-platform safety.** Covers Windows device names and illegal characters in addition to Unix/macOS restrictions.
- **Control removal.** ASCII control bytes are stripped.
- **Length guard.** Output is truncated to 255 bytes to fit common filesystem limits.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Portable folder name safe for major operating systems. |

## Examples

### Handle reserved names and invalid characters

<!-- test:safe-folder-reserved -->
```php
use Orryv\XString;

$value = XString::new('CON?');
$result = $value->toSafeFolderName();

#Test: self::assertSame('CON_', (string) $result);
```

### Preserve Unicode while sanitising separators

<!-- test:safe-folder-unicode -->
```php
use Orryv\XString;

$value = XString::new('Récap/2024');
$result = $value->toSafeFolderName();

#Test: self::assertSame('Récap_2024', (string) $result);
```

### Collapse empty or special names

<!-- test:safe-folder-special -->
```php
use Orryv\XString;

$value = XString::new('..');
$result = $value->toSafeFolderName();

#Test: self::assertSame('_', (string) $result);
```

### Whitespace-only values fallback to underscore

<!-- test:safe-folder-whitespace -->
```php
use Orryv\XString;

$value = XString::new("   ");
$result = $value->toSafeFolderName();

#Test: self::assertSame('_', (string) $result);
```

### Original instance remains unchanged

<!-- test:safe-folder-immutability -->
```php
use Orryv\XString;

$value = XString::new('draft?.bak');
$value->toSafeFolderName();

#Test: self::assertSame('draft?.bak', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toSafeFolderName` | `public function toSafeFolderName(): self` — Produce a conservative folder name safe across Windows, Linux, and macOS by neutralising reserved characters/names and collapsing empty results to underscores. |
