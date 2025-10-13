# XString::crc32()

## Table of Contents
- [XString::crc32()](#xstringcrc32)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Default hexadecimal digest](#default-hexadecimal-digest)
    - [Raw binary output](#raw-binary-output)
    - [Treats empty strings consistently](#treats-empty-strings-consistently)
    - [Original instance remains unchanged](#original-instance-remains-unchanged)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function crc32(bool $raw_output = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Calculates the CRC32B checksum of the string. By default an 8-character hexadecimal digest is produced. When `$raw_output` is `true`
the raw 4-byte binary checksum is returned instead.

## Important notes and considerations

- **Not cryptographically secure.** CRC32 is designed for error detection, not for protecting secrets.
- **Immutable operation.** The original instance is not modified.
- **Binary output may contain null bytes.** Treat it as opaque data.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$raw_output` | `bool` | `false` | When `true`, return the 4-byte binary checksum instead of hex characters. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | `XString` containing the CRC32B checksum (hex or raw bytes). |

## Examples

### Default hexadecimal digest

<!-- test:crc32-hex -->
```php
use Orryv\XString;

$result = XString::new('password')->crc32();

#Test: self::assertSame(hash('crc32b', 'password'), (string) $result);
```

### Raw binary output

<!-- test:crc32-raw -->
```php
use Orryv\XString;

$result = XString::new('password')->crc32(true);

#Test: self::assertSame(4, strlen((string) $result));
#Test: self::assertSame(hash('crc32b', 'password', true), (string) $result);
```

### Treats empty strings consistently

<!-- test:crc32-empty -->
```php
use Orryv\XString;

$result = XString::new('')->crc32();

#Test: self::assertSame(hash('crc32b', ''), (string) $result);
```

### Original instance remains unchanged

<!-- test:crc32-immutable -->
```php
use Orryv\XString;

$value = XString::new('immutable');
$value->crc32();

#Test: self::assertSame('immutable', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::crc32` | `public function crc32(bool $raw_output = false): self` — Compute the CRC32B checksum, optionally returning raw bytes. |
