# XString::base64Decode()

## Table of Contents
- [XString::base64Decode()](#xstringbase64decode)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Decode a Base64 payload](#decode-a-base64-payload)
    - [Whitespace is ignored before decoding](#whitespace-is-ignored-before-decoding)
    - [Invalid Base64 input raises an exception](#invalid-base64-input-raises-an-exception)
    - [Empty strings decode to empty strings](#empty-strings-decode-to-empty-strings)
    - [Original instance remains unchanged](#original-instance-remains-unchanged-1)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function base64Decode(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Decodes a Base64-encoded string. Whitespace characters (spaces, newlines, tabs) are stripped before decoding to support
multiline payloads produced by chunked encoders.

## Important notes and considerations

- **Immutable operation.** A new `XString` instance is returned.
- **Strict validation.** Characters outside the Base64 alphabet trigger an `InvalidArgumentException`.
- **Binary output.** The decoded result may contain arbitrary bytes.

## Parameters

`—` This method does not take any parameters.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | New `XString` instance containing the decoded bytes. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | The value is not valid Base64 data. |

## Examples

### Decode a Base64 payload

<!-- test:base64-decode-text -->
```php
use Orryv\XString;

$value = XString::new('U29tZSBkYXRhIQ==');
$result = $value->base64Decode();

#Test: self::assertSame('Some data!', (string) $result);
```

### Whitespace is ignored before decoding

<!-- test:base64-decode-whitespace -->
```php
use Orryv\XString;

$value = XString::new("SGV\nsbG8=");
$result = $value->base64Decode();

#Test: self::assertSame('Hello', (string) $result);
```

### Invalid Base64 input raises an exception

<!-- test:base64-decode-invalid -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('@@not-base64@@');

#Test: $this->expectException(InvalidArgumentException::class);
$value->base64Decode();
```

### Empty strings decode to empty strings

<!-- test:base64-decode-empty -->
```php
use Orryv\XString;

$value = XString::new('');
$result = $value->base64Decode();

#Test: self::assertSame('', (string) $result);
```

### Original instance remains unchanged

<!-- test:base64-decode-immutability -->
```php
use Orryv\XString;

$value = XString::new('U2VjcmV0');
$value->base64Decode();

#Test: self::assertSame('U2VjcmV0', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::base64Decode` | `public function base64Decode(): self` — Decode a Base64 payload into its original bytes. |
