<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;
use Orryv\XString\Exceptions\InvalidLengthException;

final class RandHexTest extends TestCase
{
    public function testRandHexBasic(): void
    {
        $token = XString::randHex(32);
        self::assertSame(32, $token->length());
        self::assertMatchesRegularExpression('/^[0-9a-f]{32}$/', (string) $token);
    }

    public function testRandHexHex2bin(): void
    {
        $token = XString::randHex(16);
        $bytes = hex2bin((string) $token);
        self::assertSame(16, $token->length());
        self::assertNotFalse($bytes);
        self::assertSame(8, strlen($bytes));
    }

    public function testRandHexInvalidLength(): void
    {
        $this->expectException(InvalidLengthException::class);
        XString::randHex(0);
    }

}
