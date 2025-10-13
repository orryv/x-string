# XString::md5()

## Table of Contents
- [XString::md5()](#xstringmd5)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Default hexadecimal digest](#default-hexadecimal-digest)
    - [Raw binary output](#raw-binary-output)
    - [Different strings yield different hashes](#different-strings-yield-different-hashes)
    - [Original instance remains unchanged](#original-instance-remains-unchanged-2)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function md5(bool $raw_output = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Calculates the MD5 hash of the string. By default a 32-character hexadecimal digest is produced. When `$raw_output` is `true`
the raw 16-byte binary hash is returned instead.

## Important notes and considerations

- **Immutable operation.** The original instance is not modified.
- **MD5 is not cryptographically secure.** Use stronger algorithms (e.g. SHA-256) for security-sensitive code.
- **Binary output may contain null bytes.** Treat it as opaque data.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$raw_output` | `bool` | `false` | When `true`, return the 16-byte binary hash instead of hex characters. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | `XString` containing the MD5 digest (hex or raw bytes). |

## Examples

### Default hexadecimal digest

<!-- test:md5-hex -->
```php
use Orryv\XString;

$value = XString::new('password');
$result = $value->md5();

#Test: self::assertSame('5f4dcc3b5aa765d61d8327deb882cf99', (string) $result);
```

### Raw binary output

<!-- test:md5-raw -->
```php
use Orryv\XString;

$value = XString::new('password');
$result = $value->md5(true);

#Test: self::assertSame(16, strlen((string) $result));
#Test: self::assertSame(md5('password', true), (string) $result);
```

### Different strings yield different hashes

<!-- test:md5-different -->
```php
use Orryv\XString;

$upper = XString::new('Case');
$lower = XString::new('case');

#Test: self::assertSame('0819eb30cc2cd18cf6b02042458c5da1', (string) $upper->md5());
#Test: self::assertSame('cd14c323902024e72c850aa828d634a7', (string) $lower->md5());
```

### Original instance remains unchanged

<!-- test:md5-immutability -->
```php
use Orryv\XString;

$value = XString::new('immutable');
$value->md5();

#Test: self::assertSame('immutable', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::md5` | `public function md5(bool $raw_output = false): self` — Compute the MD5 hash, optionally returning raw bytes. |
