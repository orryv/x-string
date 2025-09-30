<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;
use Orryv\XString\Exceptions\InvalidLengthException;

final class RandAlphaTest extends TestCase
{
    public function testRandAlphaBasic(): void
    {
        $token = XString::randAlpha(20);
        self::assertSame(20, $token->length());
        self::assertMatchesRegularExpression('/^[A-Za-z]{20}$/', (string) $token);
    }

    public function testRandAlphaInvalidLength(): void
    {
        $this->expectException(InvalidLengthException::class);
        XString::randAlpha(0);
    }

}
