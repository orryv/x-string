<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;
use Orryv\XString\Exceptions\InvalidValueConversionException;

final class ToIntTest extends TestCase
{
    public function testToIntBasic(): void
    {
        $value = XString::new('42');
        self::assertSame(42, $value->toInt());
    }

    public function testToIntSigned(): void
    {
        $value = XString::new("  -17  ");
        self::assertSame(-17, $value->toInt());
    }

    public function testToIntGrouped(): void
    {
        $value = XString::new('1_234_567');
        self::assertSame(1234567, $value->toInt());
    }

    public function testToIntFractional(): void
    {
        $value = XString::new('123.987');
        $scientific = XString::new('9.5e1');
        self::assertSame(123, $value->toInt());
        self::assertSame(95, $scientific->toInt());
    }

    public function testToIntInvalid(): void
    {
        $value = XString::new('12 apples');
        $this->expectException(InvalidValueConversionException::class);
        $value->toInt();
    }

    public function testToIntOverflow(): void
    {
        $value = XString::new((string) PHP_INT_MAX . '0');
        $this->expectException(InvalidValueConversionException::class);
        $value->toInt();
    }

}
