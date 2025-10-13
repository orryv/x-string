# XString::crypt()

## Table of Contents
- [XString::crypt()](#xstringcrypt)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Hash using a legacy DES salt](#hash-using-a-legacy-des-salt)
    - [Modern Blowfish salt yields a longer hash](#modern-blowfish-salt-yields-a-longer-hash)
    - [Original instance remains unchanged](#original-instance-remains-unchanged)
    - [Empty salts are rejected](#empty-salts-are-rejected)
    - [Invalid salts that force crypt() to fail](#invalid-salts-that-force-crypt-to-fail)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function crypt(string $salt): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Hashes the string using PHP's built-in `crypt()` function with the provided salt. The resulting hash format depends entirely on the salt (DES, Blowfish, Argon2, etc.).

## Important notes and considerations

- **Immutable operation.** Returns a new `XString` while leaving the original untouched.
- **Salt controls the algorithm.** Supply a salt that matches the hashing scheme you want (e.g. `$2y$` for Blowfish, `$6$` for SHA-512).
- **crypt() failure detection.** Invalid salts cause `crypt()` to return `"*0"` or `"*1"`; this method converts that to a `RuntimeException`.

## Parameters

| Name | Type | Description |
| --- | --- | --- |
| `$salt` | `string` | The salt string passed directly to `crypt()`. Must not be empty. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | New `XString` containing the hash returned by `crypt()`. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | The salt is empty. |
| `RuntimeException` | `crypt()` failed (returned an invalid hash marker). |

## Examples

### Hash using a legacy DES salt

<!-- test:crypt-des -->
```php
use Orryv\XString;

$value = XString::new('password');
$result = $value->crypt('aa');

#Test: self::assertSame(crypt('password', 'aa'), (string) $result);
```

### Modern Blowfish salt yields a longer hash

<!-- test:crypt-blowfish -->
```php
use Orryv\XString;

$value = XString::new('correct horse battery staple');
$result = $value->crypt('$2y$10$usesomesillystringforexampl$');

#Test: self::assertSame(60, strlen((string) $result));
#Test: self::assertSame((string) $result, crypt('correct horse battery staple', '$2y$10$usesomesillystringforexampl$'));
```

### Original instance remains unchanged

<!-- test:crypt-immutability -->
```php
use Orryv\XString;

$value = XString::new('immutable');
$value->crypt('aa');

#Test: self::assertSame('immutable', (string) $value);
```

### Empty salts are rejected

<!-- test:crypt-empty-salt -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('secret');

#Test: $this->expectException(InvalidArgumentException::class);
$value->crypt('');
```

### Invalid salts that force crypt() to fail

<!-- test:crypt-invalid-salt -->
```php
use Orryv\XString;
use RuntimeException;

$value = XString::new('secret');

#Test: $this->expectException(RuntimeException::class);
$value->crypt('*');
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::crypt` | `public function crypt(string $salt): self` — Hash the string using `crypt()` with the provided salt, throwing on failure. |
