<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use RuntimeException;

final class DecryptTest extends TestCase
{
    public function testDecryptRoundTrip(): void
    {
        $ciphertext = XString::new('Secret')->encrypt('open-sesame', 'aes-256-gcm');
        $plaintext = $ciphertext->decrypt('open-sesame', 'aes-256-gcm');
        self::assertSame('Secret', (string) $plaintext);
    }

    public function testDecryptWrongPassword(): void
    {
        $ciphertext = XString::new('Top secret')->encrypt('correct-password', 'aes-256-gcm');
        $this->expectException(RuntimeException::class);
        $ciphertext->decrypt('wrong-password', 'aes-256-gcm');
    }

    public function testDecryptTampered(): void
    {
        $ciphertext = XString::new('Integrity matters')->encrypt('pa55word', 'aes-256-gcm');
        $data = (string) $ciphertext;
        $payload = base64_decode($data, true);
        $payload[10] = chr(ord($payload[10]) ^ 0xFF); // flip a bit
        $mutated = XString::new(base64_encode($payload));
        $this->expectException(RuntimeException::class);
        $mutated->decrypt('pa55word', 'aes-256-gcm');
    }

    public function testDecryptInvalidBase64(): void
    {
        $this->expectException(InvalidArgumentException::class);
        XString::new('@@not-base64@@')->decrypt('irrelevant', 'aes-256-gcm');
    }

    public function testDecryptImmutability(): void
    {
        $ciphertext = XString::new('Plaintext')->encrypt('stay', 'aes-256-gcm');
        $before = (string) $ciphertext;
        $ciphertext->decrypt('stay', 'aes-256-gcm');
        self::assertSame($before, (string) $ciphertext);
    }

}
