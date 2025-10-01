# XString::mask()

## Table of Contents
- [XString::mask()](#xstringmask)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Format a phone number](#format-a-phone-number)
    - [Use a custom placeholder](#use-a-custom-placeholder)
    - [Mask shorter input strings](#mask-shorter-input-strings)
    - [Support for byte mode](#support-for-byte-mode)
    - [Align a mask from the end](#align-a-mask-from-the-end)
    - [Immutability check](#immutability-check)
    - [Reject empty placeholder characters](#reject-empty-placeholder-characters)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function mask(Newline|HtmlTag|Regex|string $mask, Newline|HtmlTag|Regex|string $mask_char = '#', $reversed = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Applies a mask pattern to the current string and returns a new `XString` containing the masked representation. Each occurrence
of the placeholder character (default `#`) is replaced by the next unit from the source string according to the current mode
(bytes, code points, or graphemes). Literal characters inside the mask are copied as-is. Any remaining characters in the
source beyond the available placeholders are ignored, while insufficient input simply omits those placeholders from the
result.

## Important notes and considerations

- **Placeholder granularity.** The placeholder character must be exactly one grapheme cluster. This ensures multi-byte
  characters (such as emojis) remain intact when masking.
- **Mode aware.** Masking iterates through the source string using the instance mode. In byte mode each placeholder consumes
  one byte, while in grapheme mode it consumes one full grapheme.
- **Truncation on exhaustion.** When the source runs out of characters, the mask stops processing so trailing literal segments
  tied to empty placeholders are omitted.
- **Reverse alignment.** Pass `$reversed = true` to align placeholders with the end of the string instead of the beginning,
  which is useful for right-aligned formats such as account numbers or serials.
- **Immutable.** The method never mutates the original instance; it returns a new `XString` preserving the current mode and
  encoding settings.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$mask` | `Newline\|HtmlTag\|Regex\|string` | — | The mask template that defines literal characters and placeholder positions. |
| `$mask_char` | `Newline\|HtmlTag\|Regex\|string` | `'#'` | Placeholder grapheme used to pull characters from the original value. |
| `$reversed` | `bool` | `false` | Align placeholders with the end of the string instead of the beginning. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new instance containing the masked value while preserving mode and encoding. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | Thrown when the placeholder is empty or consists of more than one grapheme. |

## Examples

### Format a phone number

<!-- test:mask-phone-number -->
```php
use Orryv\XString;

$number = XString::new('5558675309');
$result = $number->mask('###-###-####');
#Test: self::assertSame('555-867-5309', (string) $result);
```

### Use a custom placeholder

<!-- test:mask-custom-placeholder -->
```php
use Orryv\XString;

$number = XString::new('123456789');
$result = $number->mask('(***) ***-****', '*');
#Test: self::assertSame('(123) 456-789', (string) $result);
```

### Mask shorter input strings

<!-- test:mask-short-input -->
```php
use Orryv\XString;

$id = XString::new('42');
$result = $id->mask('ID-###-##');
#Test: self::assertSame('ID-42', (string) $result);
```

### Support for byte mode

<!-- test:mask-byte-mode -->
```php
use Orryv\XString;

$binary = XString::new("A\x00B\x01")->withMode('bytes');
$result = $binary->mask('0x## ##');
#Test: self::assertSame('30784100204201', bin2hex((string) $result));
#Test: self::assertSame("A\x00B\x01", (string) $binary);
```

### Align a mask from the end

<!-- test:mask-reversed -->
```php
use Orryv\XString;

$original = XString::new('123456789');
$masked = $original->mask('*****####', reversed: true);
#Test: self::assertSame('*****6789', (string) $masked);
#Test: self::assertSame('123456789', (string) $original);
```

### Immutability check

<!-- test:mask-immutability -->
```php
use Orryv\XString;

$source = XString::new('🙂🙃');
$masked = $source->mask('##-##');
#Test: self::assertSame('🙂🙃', (string) $source);
#Test: self::assertSame('🙂🙃-', (string) $masked);
```

### Reject empty placeholder characters

<!-- test:mask-invalid-placeholder -->
```php
use Orryv\XString;
use InvalidArgumentException;

$source = XString::new('1234');
#Test: $this->expectException(InvalidArgumentException::class);
$source->mask('##-##', '');
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::mask` | `public function mask(Newline\|HtmlTag\|Regex\|string $mask, Newline\|HtmlTag\|Regex\|string $mask_char = '#', $reversed = false): self` — Apply a masking template that replaces placeholder graphemes with sequential characters from the string. |
