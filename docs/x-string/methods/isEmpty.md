# XString::isEmpty()

## Table of Contents
- [XString::isEmpty()](#xstringisempty)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Treat common whitespace as empty](#treat-common-whitespace-as-empty)
    - [Exclude specific whitespace types from the check](#exclude-specific-whitespace-types-from-the-check)
    - [Non-whitespace content is never empty](#non-whitespace-content-is-never-empty)
    - [Tabs can be considered significant](#tabs-can-be-considered-significant)
    - [Numeric zero remains meaningful content](#numeric-zero-remains-meaningful-content)
    - [An already empty string remains empty](#an-already-empty-string-remains-empty)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function isEmpty(bool $newline = true, bool $space = true, bool $tab = true): bool
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✗ | Public |

## Description

Determines whether the string should be considered empty after optionally ignoring common whitespace characters.
By default spaces, tabs and newlines all count as empty characters. Toggle the corresponding boolean flags to treat
any of them as significant instead.

The check is performed without mutating the underlying `XString` instance.

## Important notes and considerations

- **Flexible whitespace rules.** Choose which whitespace characters contribute to emptiness.
- **Immutable behaviour.** Calling `isEmpty()` never alters the original string.
- **Mode agnostic.** The method operates on the raw string value; the iteration mode has no impact.
- **Whitespace only.** Only the configured whitespace characters are ignored — numeric strings such as `'0'` remain non-empty.

## Parameters

| Name | Type | Description |
| --- | --- | --- |
| `$newline` | `bool` | When `true`, `\r`, `\n` and `\r\n` are treated as empty characters. |
| `$space` | `bool` | When `true`, regular spaces are treated as empty characters. |
| `$tab` | `bool` | When `true`, tab characters are treated as empty characters. |

## Returns

| Return Type | Description |
| --- | --- |
| `bool` | `true` when the string is empty after the configured whitespace is ignored. |

## Examples

### Treat common whitespace as empty

<!-- test:is-empty-basic -->
```php
use Orryv\XString;

$value = XString::new("   \n   ");

#Test: self::assertTrue($value->isEmpty());
#Test: self::assertSame("   \n   ", (string) $value);
```

### Exclude specific whitespace types from the check

<!-- test:is-empty-newline-excluded -->
```php
use Orryv\XString;

$value = XString::new(" \n ");

#Test: self::assertTrue($value->isEmpty());
#Test: self::assertFalse($value->isEmpty(newline: false));
```

### Non-whitespace content is never empty

<!-- test:is-empty-non-empty -->
```php
use Orryv\XString;

$value = XString::new('  content  ');

#Test: self::assertFalse($value->isEmpty());
```

### Tabs can be considered significant

<!-- test:is-empty-tabs -->
```php
use Orryv\XString;

$value = XString::new("\t");

#Test: self::assertTrue($value->isEmpty());
#Test: self::assertFalse($value->isEmpty(tab: false));
```

### Numeric zero remains meaningful content

<!-- test:is-empty-zero -->
```php
use Orryv\XString;

$value = XString::new('0');

#Test: self::assertFalse($value->isEmpty());
#Test: self::assertFalse($value->isEmpty(newline: false, space: false, tab: false));
```

### An already empty string remains empty

<!-- test:is-empty-truly-empty -->
```php
use Orryv\XString;

$value = XString::new('');

#Test: self::assertTrue($value->isEmpty());
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::isEmpty` | `public function isEmpty(bool $newline = true, bool $space = true, bool $tab = true): bool` — Check whether a string should be considered empty while optionally ignoring spaces, tabs and/or newline characters. |
