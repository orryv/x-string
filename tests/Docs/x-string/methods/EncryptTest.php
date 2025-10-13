<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class EncryptTest extends TestCase
{
    public function testEncryptRoundTrip(): void
    {
        $plaintext = XString::new('Sensitive payload');
        $ciphertext = $plaintext->encrypt('password123', 'aes-256-gcm');
        $decrypted = $ciphertext->decrypt('password123', 'aes-256-gcm');
        self::assertSame('Sensitive payload', (string) $decrypted);
    }

    public function testEncryptDefaultFallback(): void
    {
        $ciphertext = XString::new('fallback-demo')->encrypt('hunter2');
        $binary = base64_decode((string) $ciphertext, true);
        self::assertIsString($binary);
        self::assertSame(2, ord($binary[1])); // algorithm id 2 => AES-256-GCM
    }

    public function testEncryptSodiumDegrades(): void
    {
        $ciphertext = XString::new('libsodium missing?')->encrypt('secret', 'sodium_xchacha20');
        $binary = base64_decode((string) $ciphertext, true);
        self::assertSame(2, ord($binary[1]));
    }

    public function testEncryptImmutability(): void
    {
        $plaintext = XString::new('unchanged');
        $plaintext->encrypt('password');
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
