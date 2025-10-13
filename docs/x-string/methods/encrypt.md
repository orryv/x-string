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
    - [Default cipher requires libsodium](#default-cipher-requires-libsodium)
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

Encrypts the string using authenticated encryption. The result is a versioned envelope that contains the salt, nonce, authentication tag, algorithm identifier, and ciphertext, encoded as Base64. The default cipher uses libsodium's XChaCha20-Poly1305 and requires the `sodium` extension; AES-256-GCM via OpenSSL is available by explicitly requesting the `aes-256-gcm` cipher.

## Important notes and considerations

- **Strong key derivation.** Uses Argon2id (libsodium) when available, otherwise PBKDF2-HMAC-SHA256.
- **libsodium required for default.** The default `'sodium_xchacha20'` cipher requires the `sodium` PHP extension. Install it on every environment that needs to encrypt or decrypt.
- **Authenticated encryption.** Integrity is enforced by the authentication tag; tampering causes `decrypt()` to throw.
- **Randomized output.** A fresh salt and nonce are generated each call, so encrypting the same plaintext twice yields different ciphertexts.
- **Immutable API.** The original `XString` remains untouched.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$password` | `string` | — | The password or passphrase used to derive the encryption key. |
| `$cipher` | `string` | `'sodium_xchacha20'` | Preferred cipher. Requires libsodium; pass `'aes-256-gcm'` to use the OpenSSL backend. |

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

### Default cipher requires libsodium

<!-- test:encrypt-default-requires-libsodium -->
```php
use Orryv\XString;

if (!function_exists('sodium_crypto_aead_xchacha20poly1305_ietf_encrypt')) {
#Test: self::markTestSkipped('libsodium extension must be installed to use the default cipher.');
}

$ciphertext = XString::new('fallback-demo')->encrypt('hunter2');
$binary = base64_decode((string) $ciphertext, true);

#Test: self::assertIsString($binary);
#Test: self::assertSame(1, ord($binary[1]));
```

### Original plaintext instance remains unchanged

<!-- test:encrypt-immutability -->
```php
use Orryv\XString;

if (!function_exists('sodium_crypto_aead_xchacha20poly1305_ietf_encrypt')) {
#Test: self::markTestSkipped('libsodium extension must be installed to use the default cipher.');
}

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
| `XString::encrypt` | `public function encrypt(string $password, string $cipher = 'sodium_xchacha20'): self` — Encrypt the string using authenticated encryption (XChaCha20-Poly1305 requires libsodium; AES-256-GCM available on demand). |
