<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;
use Orryv\XString\Exceptions\InvalidLengthException;

final class RandIntTest extends TestCase
{
    public function testRandIntDefault(): void
    {
        $pin = XString::randInt(6);
        self::assertSame(6, $pin->length());
        self::assertMatchesRegularExpression('/^[0-9]{6}$/', (string) $pin);
    }

    public function testRandIntCustomRange(): void
    {
        $digits = XString::randInt(8, 3, 7);
        self::assertSame(8, $digits->length());
        self::assertMatchesRegularExpression('/^[3-7]{8}$/', (string) $digits);
    }

    public function testRandIntInvalidLength(): void
    {
        $this->expectException(InvalidLengthException::class);
        XString::randInt(0);
    }

    public function testRandIntInvalidRange(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        XString::randInt(4, 9, 3);
    }

}
