# XString::toPascal()

## Table of Contents
- [XString::toPascal()](#xstringtopascal)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Convert a phrase to PascalCase](#convert-a-phrase-to-pascalcase)
    - [Normalize mixed separators](#normalize-mixed-separators)
    - [Leave existing PascalCase intact](#leave-existing-pascalcase-intact)
    - [Unicode friendly conversion](#unicode-friendly-conversion)
    - [Byte mode length accounting](#byte-mode-length-accounting)
    - [Empty strings stay empty](#empty-strings-stay-empty)
    - [Immutability check](#immutability-check)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toPascal(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Returns a PascalCase representation of the current string. Words are detected using the same rules as `toCamel()` and the first
letter of every word is capitalised, making this a convenience wrapper around `toCamel(true)`.

## Important notes and considerations

- **Delegates to toCamel().** Shares the same word-detection behaviour and encoding awareness as `toCamel()`.
- **Immutable.** Returns a new `XString`, leaving the original untouched.
- **Mode preserved.** The mode/encoding set on the original instance is kept on the returned value.

## Parameters

This method does not accept parameters.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` converted to PascalCase. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Convert a phrase to PascalCase

<!-- test:topascal-basic -->
```php
use Orryv\XString;

$result = XString::new('make interface great again')->toPascal();

#Test: self::assertSame('MakeInterfaceGreatAgain', (string) $result);
```

### Normalize mixed separators

<!-- test:topascal-mixed -->
```php
use Orryv\XString;

$result = XString::new('api-response_builder')->toPascal();

#Test: self::assertSame('ApiResponseBuilder', (string) $result);
```

### Leave existing PascalCase intact

<!-- test:topascal-existing -->
```php
use Orryv\XString;

$value = XString::new('AlreadyPascalCase');
$result = $value->toPascal();

#Test: self::assertSame('AlreadyPascalCase', (string) $result);
```

### Unicode friendly conversion

<!-- test:topascal-unicode -->
```php
use Orryv\XString;

$result = XString::new('élève du soir')->toPascal();

#Test: self::assertSame('ÉlèveDuSoir', (string) $result);
```

### Byte mode length accounting

<!-- test:topascal-byte-mode -->
```php
use Orryv\XString;

$value = XString::new('ångström growth')->withMode('bytes');
$result = $value->toPascal();

#Test: self::assertSame('ÅngströmGrowth', (string) $result);
#Test: self::assertSame(16, $result->length());
```

### Empty strings stay empty

<!-- test:topascal-empty -->
```php
use Orryv\XString;

$result = XString::new('')->toPascal();

#Test: self::assertSame('', (string) $result);
```

### Immutability check

<!-- test:topascal-immutable -->
```php
use Orryv\XString;

$original = XString::new('mutable string');
$pascal = $original->toPascal();

#Test: self::assertSame('mutable string', (string) $original);
#Test: self::assertSame('MutableString', (string) $pascal);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toPascal` | `public function toPascal(): self` — Convert the string to PascalCase while preserving the original instance. |
