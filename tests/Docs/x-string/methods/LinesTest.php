<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class LinesTest extends TestCase
{
    public function testLinesBasic(): void
    {
        $text = XString::new("first\nsecond\nthird");
        $lines = $text->lines();
        self::assertSame(['first', 'second', 'third'], $lines);
        self::assertSame("first\nsecond\nthird", (string) $text);
    }

    public function testLinesTrim(): void
    {
        $text = XString::new("  alpha  \n\tbeta \n gamma\t");
        $lines = $text->lines(trim: true);
        self::assertSame(['alpha', 'beta', 'gamma'], $lines);
    }

    public function testLinesLimit(): void
    {
        $text = XString::new("a\nb\nc\nd");
        $lines = $text->lines(limit: 3);
        self::assertSame(['a', 'b', "c\nd"], $lines);
    }

    public function testLinesMixedNewlines(): void
    {
        $text = XString::new("line1\r\nline2\nline3\rline4");
        $lines = $text->lines();
        self::assertSame(['line1', 'line2', 'line3', 'line4'], $lines);
    }

    public function testLinesTrailing(): void
    {
        $text = XString::new("hello\n");
        $lines = $text->lines();
        self::assertSame(['hello', ''], $lines);
    }

    public function testLinesEmpty(): void
    {
        $lines = XString::new('')->lines();
        self::assertSame([], $lines);
    }

    public function testLinesMode(): void
    {
        $xstring = XString::new("α\nβ")->withMode('codepoints');
        $lines = $xstring->lines();
        self::assertSame(['α', 'β'], $lines);
    }

    public function testLinesInvalidLimit(): void
    {
        $text = XString::new("line one\nline two");
        $this->expectException(InvalidArgumentException::class);
        $text->lines(limit: 0);
    }

}
