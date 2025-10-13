# XString::encrypt()

## Table of Contents
- [XString::encrypt()](#xstringencrypt)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Round-trip with AES-256-GCM](#round-trip-with-aes-256-gcm)
    - [Default cipher falls back to AES when libsodium is unavailable](#default-cipher-falls-back-to-aes-when-libsodium-is-unavailable)
    - [Requesting sodium_xchacha20 gracefully degrades](#requesting-sodium_xchacha20-gracefully-degrades)
    - [Original plaintext instance remains unchanged](#original-plaintext-instance-remains-unchanged)
    - [Ciphertext is opaque random-looking data](#ciphertext-is-opaque-random-looking-data)
    - [Unsupported cipher name throws](#unsupported-cipher-name-throws)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function encrypt(string $password, string $cipher = 'sodium_xchacha20'): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Encrypts the string using authenticated encryption. The result is a versioned envelope that contains the salt, nonce, authentication tag, algorithm identifier, and ciphertext, encoded as Base64. When libsodium (XChaCha20-Poly1305) is not available the method transparently falls back to OpenSSL AES-256-GCM.

## Important notes and considerations

- **Strong key derivation.** Uses Argon2id (libsodium) when available, otherwise PBKDF2-HMAC-SHA256.
- **Authenticated encryption.** Integrity is enforced by the authentication tag; tampering causes `decrypt()` to throw.
- **Randomized output.** A fresh salt and nonce are generated each call, so encrypting the same plaintext twice yields different ciphertexts.
- **Immutable API.** The original `XString` remains untouched.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$password` | `string` | — | The password or passphrase used to derive the encryption key. |
| `$cipher` | `string` | `'sodium_xchacha20'` | Preferred cipher. Falls back to AES-256-GCM if libsodium is unavailable. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Base64-encoded envelope containing all materials needed for `decrypt()`. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | Unsupported `$cipher` name. |
| `RuntimeException` | Encryption failed or the required crypto backend is unavailable. |

## Examples

### Round-trip with AES-256-GCM

<!-- test:encrypt-round-trip -->
```php
use Orryv\XString;

$plaintext = XString::new('Sensitive payload');
$ciphertext = $plaintext->encrypt('password123', 'aes-256-gcm');
$decrypted = $ciphertext->decrypt('password123', 'aes-256-gcm');

#Test: self::assertSame('Sensitive payload', (string) $decrypted);
```

### Default cipher prefers libsodium but falls back when unavailable

<!-- test:encrypt-default-fallback -->
```php
use Orryv\XString;

$ciphertext = XString::new('fallback-demo')->encrypt('hunter2');
$binary = base64_decode((string) $ciphertext, true);

#Test: self::assertIsString($binary);
if (function_exists('sodium_crypto_aead_xchacha20poly1305_ietf_encrypt')) {
    #Test: self::assertSame(1, ord($binary[1])); // libsodium preferred
} else {
    #Test: self::assertSame(2, ord($binary[1])); // AES-256-GCM fallback
}
```

### Requesting sodium_xchacha20 gracefully degrades

<!-- test:encrypt-sodium-degrades -->
```php
use Orryv\XString;

$ciphertext = XString::new('libsodium missing?')->encrypt('secret', 'sodium_xchacha20');
$binary = base64_decode((string) $ciphertext, true);

if (function_exists('sodium_crypto_aead_xchacha20poly1305_ietf_encrypt')) {
    #Test: self::assertSame(1, ord($binary[1]));
} else {
    #Test: self::assertSame(2, ord($binary[1]));
}
```

### Original plaintext instance remains unchanged

<!-- test:encrypt-immutability -->
```php
use Orryv\XString;

$plaintext = XString::new('unchanged');
$plaintext->encrypt('password');

#Test: self::assertSame('unchanged', (string) $plaintext);
```

### Ciphertext is opaque random-looking data

<!-- test:encrypt-randomized -->
```php
use Orryv\XString;

$ciphertextA = XString::new('repeatable')->encrypt('pa55w0rd', 'aes-256-gcm');
$ciphertextB = XString::new('repeatable')->encrypt('pa55w0rd', 'aes-256-gcm');

#Test: self::assertNotSame((string) $ciphertextA, (string) $ciphertextB);
```

### Unsupported cipher name throws

<!-- test:encrypt-invalid-cipher -->
```php
use InvalidArgumentException;
use Orryv\XString;

$value = XString::new('data');

#Test: $this->expectException(InvalidArgumentException::class);
$value->encrypt('password', 'rc4');
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::encrypt` | `public function encrypt(string $password, string $cipher = 'sodium_xchacha20'): self` — Encrypt the string using authenticated encryption (XChaCha20-Poly1305 or AES-256-GCM). |
