<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;
use Orryv\XString\Exceptions\InvalidLengthException;

final class RandLowerTest extends TestCase
{
    public function testRandLowerBasic(): void
    {
        $token = XString::randLower(12);
        self::assertSame(12, $token->length());
        self::assertMatchesRegularExpression('/^[a-z]{12}$/', (string) $token);
    }

    public function testRandLowerWithDigits(): void
    {
        $token = XString::randLower(16, true);
        self::assertSame(16, $token->length());
        self::assertMatchesRegularExpression('/^[a-z0-9]{16}$/', (string) $token);
    }

    public function testRandLowerInvalidLength(): void
    {
        $this->expectException(InvalidLengthException::class);
        XString::randLower(0);
    }

}
