<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class RepeatTest extends TestCase
{
    public function testRepeatBasic(): void
    {
        $value = XString::new('Hi! ');
        $result = $value->repeat(3);
        self::assertSame('Hi! Hi! Hi! ', (string) $result);
    }

    public function testRepeatZero(): void
    {
        $value = XString::new('abc');
        $result = $value->repeat(0);
        self::assertSame('', (string) $result);
    }

    public function testRepeatNegative(): void
    {
        $value = XString::new('oops');
        $this->expectException(InvalidArgumentException::class);
        $value->repeat(-1);
    }

    public function testRepeatGrapheme(): void
    {
        $value = XString::new('🍰');
        $result = $value->repeat(4);
        self::assertSame('🍰🍰🍰🍰', (string) $result);
        self::assertSame(4, $result->length());
    }

    public function testRepeatByteMode(): void
    {
        $value = XString::new("a\u{0301}")->withMode('bytes');
        $result = $value->repeat(2);
        self::assertSame("a\u{0301}a\u{0301}", (string) $result);
        self::assertSame(6, $result->length());
    }

    public function testRepeatImmutable(): void
    {
        $value = XString::new('loop');
        $result = $value->repeat(2);
        self::assertSame('loop', (string) $value);
        self::assertSame('looploop', (string) $result);
    }

}
