# XString::decrypt()

## Table of Contents
- [XString::decrypt()](#xstringdecrypt)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Decrypt data produced by encrypt()](#decrypt-data-produced-by-encrypt)
    - [Wrong password raises an exception](#wrong-password-raises-an-exception)
    - [Tampering with the payload is detected](#tampering-with-the-payload-is-detected)
    - [Invalid Base64 input is rejected](#invalid-base64-input-is-rejected)
    - [Ciphertext instance remains unchanged](#ciphertext-instance-remains-unchanged)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function decrypt(string $password, string $cipher = 'sodium_xchacha20'): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Decrypts an envelope created by `encrypt()`. The method validates the version and algorithm identifier stored in the payload, derives the key using the supplied password, and performs authenticated decryption. Any authentication failure results in an exception.

## Important notes and considerations

- **Cipher auto-detection.** The algorithm identifier embedded in the envelope determines which backend is used.
- **libsodium required for sodium payloads.** Payloads encrypted with `sodium_xchacha20` require the `sodium` extension to decrypt; ensure it's installed everywhere the ciphertext is processed.
- **Authentication enforced.** Modified ciphertexts will fail to decrypt.
- **Immutable API.** The ciphertext `XString` remains unchanged; a new instance with the plaintext is returned.

## Parameters

| Name | Type | Default | Description |
| --- | --- | --- | --- |
| `$password` | `string` | — | Password used to derive the encryption key. Must match the one used for encryption. |
| `$cipher` | `string` | `'sodium_xchacha20'` | Preferred cipher hint. Used for validation but the embedded algorithm identifier ultimately decides. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | Decrypted plaintext wrapped in a new `XString`. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `InvalidArgumentException` | Input is not valid Base64 or the envelope header is malformed. |
| `RuntimeException` | Authentication failed, the crypto backend is unavailable, or the version is unsupported. |

## Examples

### Decrypt data produced by encrypt()

<!-- test:decrypt-round-trip -->
```php
use Orryv\XString;

$ciphertext = XString::new('Secret')->encrypt('open-sesame', 'aes-256-gcm');
$plaintext = $ciphertext->decrypt('open-sesame', 'aes-256-gcm');

#Test: self::assertSame('Secret', (string) $plaintext);
```

### Wrong password raises an exception

<!-- test:decrypt-wrong-password -->
```php
use Orryv\XString;
use RuntimeException;

$ciphertext = XString::new('Top secret')->encrypt('correct-password', 'aes-256-gcm');

#Test: $this->expectException(RuntimeException::class);
$ciphertext->decrypt('wrong-password', 'aes-256-gcm');
```

### Tampering with the payload is detected

<!-- test:decrypt-tampered -->
```php
use Orryv\XString;
use RuntimeException;

$ciphertext = XString::new('Integrity matters')->encrypt('pa55word', 'aes-256-gcm');
$data = (string) $ciphertext;
$payload = base64_decode($data, true);
$payload[10] = chr(ord($payload[10]) ^ 0xFF); // flip a bit
$mutated = XString::new(base64_encode($payload));

#Test: $this->expectException(RuntimeException::class);
$mutated->decrypt('pa55word', 'aes-256-gcm');
```

### Invalid Base64 input is rejected

<!-- test:decrypt-invalid-base64 -->
```php
use InvalidArgumentException;
use Orryv\XString;

#Test: $this->expectException(InvalidArgumentException::class);
XString::new('@@not-base64@@')->decrypt('irrelevant', 'aes-256-gcm');
```

### Ciphertext instance remains unchanged

<!-- test:decrypt-immutability -->
```php
use Orryv\XString;

$ciphertext = XString::new('Plaintext')->encrypt('stay', 'aes-256-gcm');
$before = (string) $ciphertext;
$ciphertext->decrypt('stay', 'aes-256-gcm');

#Test: self::assertSame($before, (string) $ciphertext);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::decrypt` | `public function decrypt(string $password, string $cipher = 'sodium_xchacha20'): self` — Decrypt an envelope produced by `encrypt()`, verifying integrity before returning the plaintext. |
