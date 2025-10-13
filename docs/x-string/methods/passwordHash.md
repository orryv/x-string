# XString::passwordHash()

## Table of Contents
- [XString::passwordHash()](#xstringpasswordhash)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Default bcrypt hash](#default-bcrypt-hash)
    - [Custom cost option](#custom-cost-option)
    - [Original instance remains unchanged](#original-instance-remains-unchanged-1)
    - [Invalid cost triggers ValueError](#invalid-cost-triggers-valueerror)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function passwordHash(int|string $algo = PASSWORD_BCRYPT, array $options = []): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Creates a password hash using PHP's `password_hash()` API. By default the bcrypt algorithm is used, but any algorithm supported by the current PHP build can be selected via `$algo` and `$options`.

## Important notes and considerations

- **Immutable operation.** Returns a fresh `XString` containing the hash.
- **Use strong passwords.** This method does not validate password strength.
- **Options are passed through.** You can forward options such as `['cost' => 12]` for bcrypt or algorithm-specific tuning parameters.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$algo` | `int\|string` | `PASSWORD_BCRYPT` | Identifier of the password hashing algorithm to use. |
| `$options` | `array` | `[]` | Additional options forwarded to `password_hash()`. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | New `XString` containing the password hash string. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `RuntimeException` | `password_hash()` returned `false`, indicating hashing failed. |
| `ValueError` | Raised by `password_hash()` when `$options` are invalid for the chosen algorithm. |

## Examples

### Default bcrypt hash

<!-- test:password-hash-default -->
```php
use Orryv\XString;

$password = XString::new('secret');
$hash = $password->passwordHash();

#Test: self::assertTrue(password_verify('secret', (string) $hash));
#Test: self::assertSame('bcrypt', password_get_info((string) $hash)['algoName']);
```

### Custom cost option

<!-- test:password-hash-cost -->
```php
use Orryv\XString;

$password = XString::new('letmein');
$hash = $password->passwordHash(PASSWORD_BCRYPT, ['cost' => 11]);

#Test: self::assertTrue(password_verify('letmein', (string) $hash));
#Test: self::assertSame('bcrypt', password_get_info((string) $hash)['algoName']);
#Test: self::assertSame(11, password_get_info((string) $hash)['options']['cost']);
```

### Original instance remains unchanged

<!-- test:password-hash-immutability -->
```php
use Orryv\XString;

$password = XString::new('unchanged');
$password->passwordHash();

#Test: self::assertSame('unchanged', (string) $password);
```

### Invalid cost triggers ValueError

<!-- test:password-hash-invalid -->
```php
use Orryv\XString;
use ValueError;

$password = XString::new('secret');

#Test: $this->expectException(ValueError::class);
$password->passwordHash(PASSWORD_BCRYPT, ['cost' => 2]);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::passwordHash` | `public function passwordHash(int\|string $algo = PASSWORD_BCRYPT, array $options = []): self` — Hash the string with `password_hash()`, exposing algorithm and option control. |
