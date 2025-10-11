<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class OutdentTest extends TestCase
{
    public function testOutdentBasic(): void
    {
        $xstring = XString::new("    apple\n    banana");
        $result = $xstring->outdent(spaces: 4);
        self::assertSame("apple\nbanana", (string) $result);
    }

    public function testOutdentMixed(): void
    {
        $xstring = XString::new("  \tsection\n  \tcontent");
        $result = $xstring->outdent(spaces: 2, tabs: 1);
        self::assertSame("section\ncontent", (string) $result);
    }

    public function testOutdentLimit(): void
    {
        $xstring = XString::new("    first\n    second\n    third");
        $result = $xstring->outdent(spaces: 4, lines: 2);
        self::assertSame("first\nsecond\n    third", (string) $result);
    }

    public function testOutdentNoChange(): void
    {
        $xstring = XString::new("no indent\nalready flush");
        $result = $xstring->outdent(spaces: 2);
        self::assertSame("no indent\nalready flush", (string) $result);
    }

    public function testOutdentImmutability(): void
    {
        $xstring = XString::new("    keep\n    change");
        $outdented = $xstring->outdent(spaces: 2);
        self::assertSame("    keep\n    change", (string) $xstring);
        self::assertSame("  keep\n  change", (string) $outdented);
    }

    public function testOutdentInvalid(): void
    {
        $xstring = XString::new('fail');
        $this->expectException(InvalidArgumentException::class);
        $xstring->outdent(tabs: -1);
    }

    public function testOutdentNegativeLines(): void
    {
        $xstring = XString::new("    keep\n    trim\n    trim more");
        $result = $xstring->outdent(spaces: 4, lines: -2);
        self::assertSame("    keep\ntrim\ntrim more", (string) $result);
    }

}
