<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class LengthTest extends TestCase
{
    public function testLengthDefaultGrapheme(): void
    {
        $value = "Cafe\u{0301}"; // "Cafe" with a combining accent
        $xstring = XString::new($value);
        self::assertSame(4, $xstring->length());
        self::assertSame($value, (string) $xstring);
    }

    public function testLengthBytes(): void
    {
        $value = "Cafe\u{0301}"; // 5 bytes in UTF-8
        $xstring = XString::new($value)->asBytes();
        self::assertSame(strlen($value), $xstring->length());
    }

    public function testLengthCodepoints(): void
    {
        $value = "Cafe\u{0301}";
        $xstring = XString::new($value)->asCodepoints();
        self::assertSame(5, $xstring->length());
    }

    public function testLengthEmoji(): void
    {
        $value = "👩‍🚀"; // Woman astronaut (single grapheme, multiple code points)
        self::assertSame(1, XString::new($value)->length());
        self::assertGreaterThan(1, XString::new($value)->asCodepoints()->length());
        self::assertSame(strlen($value), XString::new($value)->asBytes()->length());
    }

    public function testLengthEmpty(): void
    {
        $xstring = XString::new('');
        self::assertSame(0, $xstring->length());
        self::assertSame(0, $xstring->asBytes()->length());
        self::assertSame(0, $xstring->asCodepoints()->length());
    }

}
