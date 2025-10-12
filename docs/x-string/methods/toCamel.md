# XString::toCamel()

## Table of Contents
- [XString::toCamel()](#xstringtocamel)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Convert a space separated phrase](#convert-a-space-separated-phrase)
    - [Normalize mixed separators and digits](#normalize-mixed-separators-and-digits)
    - [PascalCase variant via capitalize flag](#pascalcase-variant-via-capitalize-flag)
    - [Already camelCase strings stay unchanged](#already-camelcase-strings-stay-unchanged)
    - [Unicode safe lowercasing](#unicode-safe-lowercasing)
    - [Byte mode length accounting](#byte-mode-length-accounting)
    - [Empty strings stay empty](#empty-strings-stay-empty)
    - [Immutability check](#immutability-check)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toCamel(bool $capitalize_first = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Converts the current value to **camelCase** by lowercasing the first word (unless `$capitalize_first` is true) and capitalising
the initial letter of subsequent words. Word boundaries are inferred from whitespace, punctuation (such as `_` or `-`), and
case transitions, so mixed formats are handled consistently.

## Important notes and considerations

- **Optional PascalCase.** Set `$capitalize_first` to `true` to produce a PascalCase result.
- **Encoding aware.** Uses multibyte string functions so accented characters are handled correctly.
- **Mode preserved.** The returned instance keeps the same mode/encoding configuration as the original value.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$capitalize_first` | `bool` | `false` | When `true`, capitalises the first word as well (PascalCase). |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` converted to camelCase / PascalCase. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Convert a space separated phrase

<!-- test:tocamel-basic -->
```php
use Orryv\XString;

$phrase = XString::new('hello world example');
$result = $phrase->toCamel();

#Test: self::assertSame('helloWorldExample', (string) $result);
#Test: self::assertSame('hello world example', (string) $phrase);
```

### Normalize mixed separators and digits

<!-- test:tocamel-mixed -->
```php
use Orryv\XString;

$value = XString::new('make-HTTP_response 42');
$result = $value->toCamel();

#Test: self::assertSame('makeHttpResponse42', (string) $result);
```

### PascalCase variant via capitalize flag

<!-- test:tocamel-pascal-flag -->
```php
use Orryv\XString;

$result = XString::new('customer account')->toCamel(true);

#Test: self::assertSame('CustomerAccount', (string) $result);
```

### Already camelCase strings stay unchanged

<!-- test:tocamel-existing -->
```php
use Orryv\XString;

$value = XString::new('alreadyCamelCase');
$result = $value->toCamel();

#Test: self::assertSame('alreadyCamelCase', (string) $result);
```

### Unicode safe lowercasing

<!-- test:tocamel-unicode -->
```php
use Orryv\XString;

$result = XString::new('Olá Mundo')->toCamel();

#Test: self::assertSame('oláMundo', (string) $result);
```

### Byte mode length accounting

<!-- test:tocamel-byte-mode -->
```php
use Orryv\XString;

$value = XString::new('Ångström Growth')->withMode('bytes');
$result = $value->toCamel();

#Test: self::assertSame('ångströmGrowth', (string) $result);
#Test: self::assertSame(16, $result->length());
```

### Empty strings stay empty

<!-- test:tocamel-empty -->
```php
use Orryv\XString;

$empty = XString::new('');
$result = $empty->toCamel();

#Test: self::assertSame('', (string) $result);
```

### Immutability check

<!-- test:tocamel-immutable -->
```php
use Orryv\XString;

$original = XString::new('Mutable Value');
$camel = $original->toCamel();

#Test: self::assertSame('Mutable Value', (string) $original);
#Test: self::assertSame('mutableValue', (string) $camel);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toCamel` | `public function toCamel(bool $capitalize_first = false): self` — Convert to camelCase while optionally capitalising the first word. |
