<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use InvalidArgumentException;
use Orryv\XString;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class EncryptTest extends TestCase
{
    public function testEncryptRoundTrip(): void
    {
        $plaintext = XString::new('Sensitive payload');
        $ciphertext = $plaintext->encrypt('password123', 'aes-256-gcm');
        $decrypted = $ciphertext->decrypt('password123', 'aes-256-gcm');
        self::assertSame('Sensitive payload', (string) $decrypted);
    }

    public function testEncryptDefaultRequiresLibsodium(): void
    {
        if (!function_exists('sodium_crypto_aead_xchacha20poly1305_ietf_encrypt')) {
            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage('libsodium support is required to encrypt using sodium_xchacha20.');
        }
        $ciphertext = XString::new('fallback-demo')->encrypt('hunter2');
        $binary = base64_decode((string) $ciphertext, true);
        self::assertIsString($binary);
        if (function_exists('sodium_crypto_aead_xchacha20poly1305_ietf_encrypt')) {
            self::assertSame(1, ord($binary[1]));
        }
    }

    public function testEncryptImmutability(): void
    {
        $plaintext = XString::new('unchanged');
        $plaintext->encrypt('password', 'aes-256-gcm');
        self::assertSame('unchanged', (string) $plaintext);
    }

    public function testEncryptRandomized(): void
    {
        $ciphertextA = XString::new('repeatable')->encrypt('pa55w0rd', 'aes-256-gcm');
        $ciphertextB = XString::new('repeatable')->encrypt('pa55w0rd', 'aes-256-gcm');
        self::assertNotSame((string) $ciphertextA, (string) $ciphertextB);
    }

    public function testEncryptInvalidCipher(): void
    {
        $value = XString::new('data');
        $this->expectException(InvalidArgumentException::class);
        $value->encrypt('password', 'rc4');
    }

}
