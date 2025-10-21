# XString::toInt()

## Table of Contents
- [XString::toInt()](#xstringtoint)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Parse an integer string](#parse-an-integer-string)
    - [Handle signs and whitespace](#handle-signs-and-whitespace)
    - [Allow digit group separators](#allow-digit-group-separators)
    - [Truncate fractional values](#truncate-fractional-values)
    - [Reject invalid representations](#reject-invalid-representations)
    - [Reject values outside the platform range](#reject-values-outside-the-platform-range)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toInt(): int
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✗ | Public |

## Description

Convert the underlying value to an integer. The string may represent either an integer or floating-point value (optionally
signed) and can contain underscore digit separators. Fractional components are truncated toward zero. Leading and trailing ASCII
whitespace is ignored.

## Important notes and considerations

- **Numeric validation.** Non-numeric strings raise an `InvalidValueConversionException`.
- **Fractional truncation.** Floating-point notations such as `"3.9"` or `"1e3"` are converted by truncating toward zero.
- **Separator support.** Underscores between digits are stripped before validation (e.g. `"1_000"`).
- **Bounds checked.** Values outside PHP's native integer range are rejected.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `int` | ✗ | Parsed integer value. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `Orryv\\XString\\Exceptions\\InvalidValueConversionException` | The value is empty, not numeric, or exceeds the supported integer range. |

## Examples

### Parse an integer string

<!-- test:to-int-basic -->
```php
use Orryv\XString;

$value = XString::new('42');

#Test: self::assertSame(42, $value->toInt());
```

### Handle signs and whitespace

<!-- test:to-int-signed -->
```php
use Orryv\XString;

$value = XString::new("  -17  ");

#Test: self::assertSame(-17, $value->toInt());
```

### Allow digit group separators

<!-- test:to-int-grouped -->
```php
use Orryv\XString;

$value = XString::new('1_234_567');

#Test: self::assertSame(1234567, $value->toInt());
```

### Truncate fractional values

<!-- test:to-int-fractional -->
```php
use Orryv\XString;

$value = XString::new('123.987');
$scientific = XString::new('9.5e1');

#Test: self::assertSame(123, $value->toInt());
#Test: self::assertSame(95, $scientific->toInt());
```

### Reject invalid representations

<!-- test:to-int-invalid -->
```php
use Orryv\XString\Exceptions\InvalidValueConversionException;
use Orryv\XString;

$value = XString::new('12 apples');

#Test: $this->expectException(InvalidValueConversionException::class);
$value->toInt();
```

### Reject values outside the platform range

<!-- test:to-int-overflow -->
```php
use Orryv\XString\Exceptions\InvalidValueConversionException;
use Orryv\XString;

$value = XString::new((string) PHP_INT_MAX . '0');

#Test: $this->expectException(InvalidValueConversionException::class);
$value->toInt();
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toInt` | `public function toInt(): int` — Convert the string to an integer (underscores allowed), truncating fractional input toward zero while enforcing numeric validation and range checks. |
