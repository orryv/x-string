<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;
use Orryv\XString\Exceptions\InvalidValueConversionException;

final class ToBoolTest extends TestCase
{
    public function testToBoolAffirmative(): void
    {
        $value = XString::new('YeS');
        self::assertTrue($value->toBool());
    }

    public function testToBoolNegative(): void
    {
        $value = XString::new('failed');
        self::assertFalse($value->toBool());
    }

    public function testToBoolNumeric(): void
    {
        $positive = XString::new('2');
        $negative = XString::new('-1');
        self::assertTrue($positive->toBool());
        self::assertFalse($negative->toBool());
    }

    public function testToBoolEmpty(): void
    {
        $value = XString::new("   ");
        self::assertFalse($value->toBool());
    }

    public function testToBoolAmbiguous(): void
    {
        $value = XString::new('perhaps');
        $this->expectException(InvalidValueConversionException::class);
        $value->toBool();
    }

}
