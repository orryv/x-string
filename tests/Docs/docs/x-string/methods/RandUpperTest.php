<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs;

use PHPUnit\Framework\TestCase;
use Orryv\XString\Exceptions\InvalidLengthException;
use Orryv\XString\XString;

final class RandUpperTest extends TestCase
{
    public function testRandUpperBasic(): void
    {
        $token = XString::randUpper(10);
        self::assertSame(10, $token->length());
        self::assertMatchesRegularExpression('/^[A-Z]{10}$/', (string) $token);
    }

    public function testRandUpperWithDigits(): void
    {
        $token = XString::randUpper(14, true);
        self::assertSame(14, $token->length());
        self::assertMatchesRegularExpression('/^[A-Z0-9]{14}$/', (string) $token);
    }

    public function testRandUpperInvalidLength(): void
    {
        $this->expectException(InvalidLengthException::class);
        XString::randUpper(0);
    }

}
