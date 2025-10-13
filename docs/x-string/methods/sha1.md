# XString::sha1()

## Table of Contents
- [XString::sha1()](#xstringsha1)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Default hexadecimal digest](#default-hexadecimal-digest)
    - [Raw binary output](#raw-binary-output)
    - [Case-sensitive hashes](#case-sensitive-hashes)
    - [Original instance remains unchanged](#original-instance-remains-unchanged)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function sha1(bool $raw_output = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Calculates the SHA-1 hash of the string. By default a 40-character hexadecimal digest is produced. When `$raw_output` is `true`
the raw 20-byte binary hash is returned instead.

## Important notes and considerations

- **Immutable operation.** The original instance is not modified.
- **SHA-1 is considered broken.** Prefer stronger digests (e.g. SHA-256) for security-sensitive code.
- **Binary output may contain null bytes.** Treat it as opaque data.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$raw_output` | `bool` | `false` | When `true`, return the 20-byte binary hash instead of hex characters. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | `XString` containing the SHA-1 digest (hex or raw bytes). |

## Examples

### Default hexadecimal digest

<!-- test:sha1-hex -->
```php
use Orryv\XString;

$result = XString::new('password')->sha1();

#Test: self::assertSame('5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', (string) $result);
```

### Raw binary output

<!-- test:sha1-raw -->
```php
use Orryv\XString;

$result = XString::new('password')->sha1(true);

#Test: self::assertSame(20, strlen((string) $result));
#Test: self::assertSame(sha1('password', true), (string) $result);
```

### Case-sensitive hashes

<!-- test:sha1-case-sensitive -->
```php
use Orryv\XString;

$upper = XString::new('Case');
$lower = XString::new('case');

#Test: self::assertSame('9254c4bba00f5ff69304a7921d3118fcbac7e6b8', (string) $upper->sha1());
#Test: self::assertSame('6406510c31e0c9925733c7f21414bf6428333ed2', (string) $lower->sha1());
```

### Original instance remains unchanged

<!-- test:sha1-immutable -->
```php
use Orryv\XString;

$value = XString::new('immutable');
$value->sha1();

#Test: self::assertSame('immutable', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::sha1` | `public function sha1(bool $raw_output = false): self` — Compute the SHA-1 hash, optionally returning raw bytes. |
