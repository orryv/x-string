# XString::toFloat()

## Table of Contents
- [XString::toFloat()](#xstringtofloat)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Parse a decimal string](#parse-a-decimal-string)
    - [Support scientific notation](#support-scientific-notation)
    - [Allow underscore separators](#allow-underscore-separators)
    - [Reject non-numeric input](#reject-non-numeric-input)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toFloat(): float
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✗ | Public |

## Description

Parse the string into a floating-point number. Whitespace is trimmed, underscores are removed, and the value must satisfy
`is_numeric()` and produce a finite float.

## Important notes and considerations

- **Numeric validation.** Non-numeric strings raise an `InvalidValueConversionException`.
- **Finite results only.** Values that evaluate to `INF`, `-INF`, or `NAN` are rejected.
- **Locale independent.** Only dot (`.`) decimal separators are recognised.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `float` | ✗ | Parsed floating-point value. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `Orryv\\XString\\Exceptions\\InvalidValueConversionException` | The value is empty, not numeric, or evaluates to a non-finite float. |

## Examples

### Parse a decimal string

<!-- test:to-float-basic -->
```php
use Orryv\XString;

$value = XString::new('3.1415');

#Test: self::assertSame(3.1415, $value->toFloat());
```

### Support scientific notation

<!-- test:to-float-scientific -->
```php
use Orryv\XString;

$value = XString::new('  2.5e3  ');

#Test: self::assertSame(2500.0, $value->toFloat());
```

### Allow underscore separators

<!-- test:to-float-grouped -->
```php
use Orryv\XString;

$value = XString::new('1_234.75');

#Test: self::assertSame(1234.75, $value->toFloat());
```

### Reject non-numeric input

<!-- test:to-float-invalid -->
```php
use Orryv\XString;
use Orryv\XString\Exceptions\InvalidValueConversionException;

$value = XString::new('not-a-number');

#Test: $this->expectException(InvalidValueConversionException::class);
$value->toFloat();
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toFloat` | `public function toFloat(): float` — Convert the string to a validated floating-point value (underscores supported) while rejecting non-finite results. |
