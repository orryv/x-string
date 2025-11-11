# XString::toString()

## Table of Contents
- [XString::toString()](#xstringtostring)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Retrieve the underlying PHP string](#retrieve-the-underlying-php-string)
    - [Works the same as string casting](#works-the-same-as-string-casting)
    - [Convenient in method chains](#convenient-in-method-chains)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toString(): string
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✗ | Public |

## Description

Return the underlying PHP `string` stored by the `XString` object. This is a convenience wrapper around the `Stringable`
interface implementation (`__toString()`) so you can explicitly request the scalar value without casting.

## Important notes and considerations

- **No cloning involved.** The method simply exposes the stored string without creating a new `XString` instance.
- **Encoding aware.** The returned string preserves the instance encoding; no additional conversions are performed.
- **Alias for casting.** Functionally equivalent to `(string) $xstring` or `$xstring->__toString()` but clearer in fluent chains.

## Parameters

This method does not accept parameters.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `string` | ✗ | The raw PHP string value held by the `XString` instance. |

## Examples

### Retrieve the underlying PHP string

<!-- test:toString-basic -->
```php
use Orryv\XString;

$xstring = XString::new('Hello, world!');
$value = $xstring->toString();
#Test: self::assertSame('Hello, world!', $value);
```

### Works the same as string casting

<!-- test:toString-casting -->
```php
use Orryv\XString;

$xstring = XString::new('ßeta');
#Test: self::assertSame((string) $xstring, $xstring->toString());
#Test: self::assertSame($xstring->__toString(), $xstring->toString());
```

### Convenient in method chains

<!-- test:toString-chained -->
```php
use Orryv\XString;

$result = XString::new('  padded ')
    ->trim()
    ->toUpper()
    ->toString();
#Test: self::assertSame('PADDED', $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toString` | `public function toString(): string` — Return the underlying PHP string without requiring an explicit cast. |
