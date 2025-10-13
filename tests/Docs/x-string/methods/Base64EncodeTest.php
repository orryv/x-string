<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class Base64EncodeTest extends TestCase
{
    public function testBase64EncodeText(): void
    {
        $value = XString::new('Hello, World!');
        $result = $value->base64Encode();
        self::assertSame('SGVsbG8sIFdvcmxkIQ==', (string) $result);
    }

    public function testBase64EncodeBinary(): void
    {
        $value = XString::new("\x00\xFF\x10\x80");
        $result = $value->base64Encode();
        self::assertSame('AP8QgA==', (string) $result);
    }

    public function testBase64EncodeMode(): void
    {
        $value = XString::new('data')->withMode('bytes');
        $result = $value->base64Encode();
        self::assertSame('ZGF0YQ==', (string) $result);
    }

    public function testBase64EncodeImmutability(): void
    {
        $value = XString::new('secret');
        $value->base64Encode();
        self::assertSame('secret', (string) $value);
    }

}
