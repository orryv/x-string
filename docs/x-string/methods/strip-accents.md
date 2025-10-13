# XString::stripAccents()

## Table of Contents
- [XString::stripAccents()](#xstringstripaccents)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Remove accents from common Latin words](#remove-accents-from-common-latin-words)
    - [Handle ligatures and locale-specific letters](#handle-ligatures-and-locale-specific-letters)
    - [Remove combining accent marks](#remove-combining-accent-marks)
    - [Non-Latin scripts remain unchanged](#non-latin-scripts-remain-unchanged)
    - [Original instance stays untouched](#original-instance-stays-untouched)
    - [Empty strings stay empty](#empty-strings-stay-empty)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function stripAccents(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Normalises the string and removes diacritical marks, yielding an accent-free representation. Accented characters are decomposed
and recomposed without their combining marks, with additional fallbacks that map ligatures and stroke characters (e.g. `ß`, `Ł`)
to their closest ASCII counterparts.

## Important notes and considerations

- **Unicode normalisation.** Decomposes characters before stripping combining marks, ensuring results for both precomposed and
  combining-accent sequences.
- **Ligature fallbacks.** Includes mappings for characters such as `ß → ss`, `Æ → AE`, and `Ł → L` when they do not decompose
  cleanly.
- **Immutable.** Returns a new `XString`; the original string remains unchanged.

## Parameters

This method does not accept parameters.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` where accented characters have been replaced with their accent-free equivalents. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Remove accents from common Latin words

<!-- test:strip-accents-basic -->
```php
use Orryv\XString;

$value = XString::new('Café crème brûlée');
$result = $value->stripAccents();

#Test: self::assertSame('Cafe creme brulee', (string) $result);
```

### Handle ligatures and locale-specific letters

<!-- test:strip-accents-ligatures -->
```php
use Orryv\XString;

$value = XString::new('Ångström & Straße — façade');
$result = $value->stripAccents();

#Test: self::assertSame('Angstrom & Strasse — facade', (string) $result);
```

### Remove combining accent marks

<!-- test:strip-accents-combining -->
```php
use Orryv\XString;

$value = XString::new("Cafe\u{0301} mañana");
$result = $value->stripAccents();

#Test: self::assertSame('Cafe manana', (string) $result);
```

### Non-Latin scripts remain unchanged

<!-- test:strip-accents-non-latin -->
```php
use Orryv\XString;

$value = XString::new('中文 日本語 한국어');
$result = $value->stripAccents();

#Test: self::assertSame('中文 日本語 한국어', (string) $result);
```

### Original instance stays untouched

<!-- test:strip-accents-immutable -->
```php
use Orryv\XString;

$original = XString::new('Señorita');
$processed = $original->stripAccents();

#Test: self::assertSame('Senorita', (string) $processed);
#Test: self::assertSame('Señorita', (string) $original);
```

### Empty strings stay empty

<!-- test:strip-accents-empty -->
```php
use Orryv\XString;

$result = XString::new('')->stripAccents();

#Test: self::assertSame('', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::stripAccents` | `public function stripAccents(): self` — Remove diacritical marks and ligatures while leaving base characters intact. |
