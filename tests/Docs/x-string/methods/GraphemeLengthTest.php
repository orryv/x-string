<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class GraphemeLengthTest extends TestCase
{
    public function testGraphemeLengthBasic(): void
    {
        $xstring = XString::new("Cafe\u{0301}");
        self::assertSame(4, $xstring->graphemeLength());
        self::assertSame("Cafe\u{0301}", (string) $xstring);
    }

    public function testGraphemeLengthModes(): void
    {
        $value = "mañana";
        $xstring = XString::new($value);
        self::assertSame(6, $xstring->graphemeLength());
        self::assertSame(6, $xstring->asBytes()->graphemeLength());
        self::assertSame(6, $xstring->asCodepoints()->graphemeLength());
    }

    public function testGraphemeLengthEmoji(): void
    {
        $astronaut = "👩‍🚀";
        self::assertSame(1, XString::new($astronaut)->graphemeLength());
        self::assertSame(3, XString::new($astronaut)->asCodepoints()->length());
    }

    public function testGraphemeLengthEmpty(): void
    {
        $xstring = XString::new('');
        self::assertSame(0, $xstring->graphemeLength());
        self::assertSame('', (string) $xstring);
    }

}
