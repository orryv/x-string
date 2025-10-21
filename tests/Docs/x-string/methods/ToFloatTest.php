<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;
use Orryv\XString\Exceptions\InvalidValueConversionException;

final class ToFloatTest extends TestCase
{
    public function testToFloatBasic(): void
    {
        $value = XString::new('3.1415');
        self::assertSame(3.1415, $value->toFloat());
    }

    public function testToFloatScientific(): void
    {
        $value = XString::new('  2.5e3  ');
        self::assertSame(2500.0, $value->toFloat());
    }

    public function testToFloatGrouped(): void
    {
        $value = XString::new('1_234.75');
        self::assertSame(1234.75, $value->toFloat());
    }

    public function testToFloatInvalid(): void
    {
        $value = XString::new('not-a-number');
        $this->expectException(InvalidValueConversionException::class);
        $value->toFloat();
    }

}
