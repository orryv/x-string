<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class Base64DecodeTest extends TestCase
{
    public function testBase64DecodeText(): void
    {
        $value = XString::new('U29tZSBkYXRhIQ==');
        $result = $value->base64Decode();
        self::assertSame('Some data!', (string) $result);
    }

    public function testBase64DecodeWhitespace(): void
    {
        $value = XString::new("SGV\nsbG8=");
        $result = $value->base64Decode();
        self::assertSame('Hello', (string) $result);
    }

    public function testBase64DecodeInvalid(): void
    {
        $value = XString::new('@@not-base64@@');
        $this->expectException(InvalidArgumentException::class);
        $value->base64Decode();
    }

    public function testBase64DecodeEmpty(): void
    {
        $value = XString::new('');
        $result = $value->base64Decode();
        self::assertSame('', (string) $result);
    }

    public function testBase64DecodeImmutability(): void
    {
        $value = XString::new('U2VjcmV0');
        $value->base64Decode();
        self::assertSame('U2VjcmV0', (string) $value);
    }

}
