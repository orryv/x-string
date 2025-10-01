<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class IndentTest extends TestCase
{
    public function testIndentBasic(): void
    {
        $xstring = XString::new("first line\nsecond line");
        $result = $xstring->indent(spaces: 4);
        self::assertSame("    first line\n    second line", (string) $result);
    }

    public function testIndentLineLimit(): void
    {
        $xstring = XString::new("alpha\nbeta\ngamma");
        $result = $xstring->indent(spaces: 2, lines: 1);
        self::assertSame("  alpha\nbeta\ngamma", (string) $result);
    }

    public function testIndentTabs(): void
    {
        $xstring = XString::new("item 1\nitem 2");
        $result = $xstring->indent(spaces: 0, tabs: 1);
        self::assertSame("\titem 1\n\titem 2", (string) $result);
    }

    public function testIndentNoop(): void
    {
        $xstring = XString::new("left alone");
        $result = $xstring->indent(spaces: 0, tabs: 0);
        self::assertSame('left alone', (string) $result);
    }

    public function testIndentEmpty(): void
    {
        $xstring = XString::new('');
        $result = $xstring->indent(spaces: 2);
        self::assertSame('', (string) $result);
    }

    public function testIndentImmutability(): void
    {
        $xstring = XString::new("line A\nline B");
        $indented = $xstring->indent(spaces: 2, tabs: 1);
        self::assertSame("line A\nline B", (string) $xstring);
        self::assertSame("  \tline A\n  \tline B", (string) $indented);
    }

    public function testIndentInvalidParams(): void
    {
        $xstring = XString::new('oops');
        $this->expectException(InvalidArgumentException::class);
        $xstring->indent(spaces: -1);
    }

    public function testIndentNegativeLines(): void
    {
        $xstring = XString::new("first\nsecond\nthird\nfourth");
        $result = $xstring->indent(spaces: 2, lines: -2);
        self::assertSame("first\nsecond\n  third\n  fourth", (string) $result);
    }

}
