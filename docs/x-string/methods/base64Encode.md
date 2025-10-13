# XString::base64Encode()

## Table of Contents
- [XString::base64Encode()](#xstringbase64encode)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Encode plain text](#encode-plain-text)
    - [Binary data is safely encoded](#binary-data-is-safely-encoded)
    - [Modes do not affect the output](#modes-do-not-affect-the-output)
    - [Original instance remains unchanged](#original-instance-remains-unchanged)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function base64Encode(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Encodes the string using the Base64 alphabet. The resulting value is always safe for text transport and contains only ASCII
characters.

## Important notes and considerations

- **Immutable operation.** A new `XString` instance containing the encoded payload is returned.
- **No padding changes.** Standard `=` padding is preserved.
- **Mode preservation.** The current iteration mode (bytes/codepoints/graphemes) is preserved.

## Parameters

`—` This method does not take any parameters.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | New `XString` instance holding the Base64 encoded string. |

## Examples

### Encode plain text

<!-- test:base64-encode-text -->
```php
use Orryv\XString;

$value = XString::new('Hello, World!');
$result = $value->base64Encode();

#Test: self::assertSame('SGVsbG8sIFdvcmxkIQ==', (string) $result);
```

### Binary data is safely encoded

<!-- test:base64-encode-binary -->
```php
use Orryv\XString;

$value = XString::new("\x00\xFF\x10\x80");
$result = $value->base64Encode();

#Test: self::assertSame('AP8QgA==', (string) $result);
```

### Modes do not affect the output

<!-- test:base64-encode-mode -->
```php
use Orryv\XString;

$value = XString::new('data')->withMode('bytes');
$result = $value->base64Encode();

#Test: self::assertSame('ZGF0YQ==', (string) $result);
```

### Original instance remains unchanged

<!-- test:base64-encode-immutability -->
```php
use Orryv\XString;

$value = XString::new('secret');
$value->base64Encode();

#Test: self::assertSame('secret', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::base64Encode` | `public function base64Encode(): self` — Encode the string using the Base64 alphabet. |
