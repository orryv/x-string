# XString::uuid()

## Table of Contents
- [XString::uuid()](#xstringuuid)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Generate a version 4 UUID (default)](#generate-a-version-4-uuid-default)
    - [Generate a time-based version 1 UUID](#generate-a-time-based-version-1-uuid)
    - [Generate deterministic version 3 UUIDs](#generate-deterministic-version-3-uuids)
    - [Generate deterministic version 5 UUIDs](#generate-deterministic-version-5-uuids)
    - [Missing namespace triggers an exception](#missing-namespace-triggers-an-exception)
    - [Invalid namespace format is rejected](#invalid-namespace-format-is-rejected)
    - [Unsupported versions are rejected](#unsupported-versions-are-rejected)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public static function uuid(int $version = 4, ?string $namespace = null, ?string $name = null): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Static | ✓ | Public |

## Description

Generates a RFC 4122–compliant UUID (Universally Unique Identifier) in versions 1, 3, 4, or 5. Version 4 uses cryptographically
secure random bytes. Version 1 encodes the current timestamp plus a randomly generated clock sequence and node identifier.
Versions 3 and 5 derive deterministic UUIDs from a namespace UUID and a name using MD5 and SHA-1 hashing respectively.

**Algorithm overview:**

- Validate the requested version (must be 1, 3, 4, or 5).
- For version 1:
  - Compute the 100-nanosecond timestamp offset from the UUID epoch.
  - Generate a random 14-bit clock sequence and 48-bit node identifier (with the multicast bit set).
  - Format the UUID with the correct version and variant bits.
- For versions 3 and 5:
  - Validate that `$namespace` is a valid UUID string and `$name` is a non-empty string.
  - Concatenate the namespace bytes with the name and hash using MD5 (v3) or SHA-1 (v5).
  - Format the first 128 bits of the hash as a UUID with the correct version and variant bits.
- For version 4: generate 16 random bytes and set version/variant bits appropriately.
- Wrap the resulting UUID string in a new immutable `XString`.

## Important notes and considerations

- **Determinism vs randomness.** Versions 3 and 5 return stable values for the same namespace+name pair. Versions 1 and 4 return
a different UUID each call.
- **Namespace validation.** Namespace UUIDs may be provided with or without braces/hyphens; they are validated strictly before use.
- **String result.** The returned UUID is lowercase hexadecimal with canonical hyphen placement.

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$version` | `4` | `int` | UUID version to generate. Supported values: 1, 3, 4, 5. |
| `$namespace` | `null` | `?string` | Required for versions 3 and 5. Must be a valid UUID string (with or without hyphens/braces). Ignored otherwise. |
| `$name` | `null` | `?string` | Required for versions 3 and 5. Arbitrary non-empty string used alongside the namespace. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | New `XString` instance containing the generated UUID string. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$version` is not 1, 3, 4, or 5; `$namespace` or `$name` is missing for v3/v5; or `$namespace` is not a valid UUID string. |

## Examples

### Generate a version 4 UUID (default)

<!-- test:uuid-v4 -->
```php
use Orryv\XString;

$uuid = XString::uuid();
#Test: self::assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', (string) $uuid);
```

### Generate a time-based version 1 UUID

<!-- test:uuid-v1 -->
```php
use Orryv\XString;

$uuid = XString::uuid(1);
#Test: self::assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-1[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', (string) $uuid);
#Test: self::assertNotSame(XString::uuid(1)->__toString(), (string) $uuid);
```

### Generate deterministic version 3 UUIDs

<!-- test:uuid-v3-deterministic -->
```php
use Orryv\XString;

$namespace = '6ba7b810-9dad-11d1-80b4-00c04fd430c8'; // DNS namespace
$first = XString::uuid(3, $namespace, 'example.com');
$second = XString::uuid(3, $namespace, 'example.com');
#Test: self::assertSame('9073926b-929f-31c2-abc9-fad77ae3e8eb', (string) $first);
#Test: self::assertSame((string) $first, (string) $second);
```

### Generate deterministic version 5 UUIDs

<!-- test:uuid-v5-deterministic -->
```php
use Orryv\XString;

$namespace = '6ba7b810-9dad-11d1-80b4-00c04fd430c8'; // DNS namespace
$uuid = XString::uuid(5, $namespace, 'example.com');
#Test: self::assertSame('cfbff0d1-9375-5685-968c-48ce8b15ae17', (string) $uuid);
```

### Missing namespace triggers an exception

<!-- test:uuid-missing-namespace -->
```php
use InvalidArgumentException;
use Orryv\XString;

#Test: $this->expectException(InvalidArgumentException::class);
XString::uuid(3, null, 'name');
```

### Invalid namespace format is rejected

<!-- test:uuid-invalid-namespace -->
```php
use InvalidArgumentException;
use Orryv\XString;

#Test: $this->expectException(InvalidArgumentException::class);
XString::uuid(5, 'not-a-uuid', 'name');
```

### Unsupported versions are rejected

<!-- test:uuid-invalid-version -->
```php
use InvalidArgumentException;
use Orryv\XString;

#Test: $this->expectException(InvalidArgumentException::class);
XString::uuid(2);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::uuid` | `public static function uuid(int $version = 4, ?string $namespace = null, ?string $name = null): self` — Generate RFC 4122 UUIDs (v1, v3, v4, v5) with validation. |
