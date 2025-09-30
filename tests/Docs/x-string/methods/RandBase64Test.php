<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;
use Orryv\XString\Exceptions\InvalidLengthException;

final class RandBase64Test extends TestCase
{
    public function testRandBase64Basic(): void
    {
        $token = XString::randBase64(24);
        self::assertSame(24, $token->length());
        self::assertMatchesRegularExpression('/^[A-Za-z0-9+\/]{24}$/', (string) $token);
    }

    public function testRandBase64CharacterSet(): void
    {
        $token = XString::randBase64(8);
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
        $overlap = strspn((string) $token, $alphabet);
        self::assertSame(8, $overlap);
        self::assertStringNotContainsString('=', (string) $token);
    }

    public function testRandBase64InvalidLength(): void
    {
        $this->expectException(InvalidLengthException::class);
        XString::randBase64(0);
    }

}
