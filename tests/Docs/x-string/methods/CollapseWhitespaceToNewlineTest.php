<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class CollapseWhitespaceToNewlineTest extends TestCase
{
    public function testCollapseWhitespaceToNewlineBasic(): void
    {
        $values = XString::new("first second\tthird");
        $result = $values->collapseWhitespaceToNewline();
        self::assertSame("first\nsecond\nthird", (string) $result);
    }

    public function testCollapseWhitespaceToNewlineMultiple(): void
    {
        $text = XString::new("line1\n\n\nline2");
        $result = $text->collapseWhitespaceToNewline();
        self::assertSame("line1\nline2", (string) $result);
    }

    public function testCollapseWhitespaceToNewlineCrlf(): void
    {
        $text = XString::new("a\r\nb\r\n\rc");
        $result = $text->collapseWhitespaceToNewline();
        self::assertSame("a\nb\nc", (string) $result);
        self::assertSame("a\r\nb\r\n\rc", (string) $text);
    }

    public function testCollapseWhitespaceToNewlineEmpty(): void
    {
        $text = XString::new('');
        $result = $text->collapseWhitespaceToNewline();
        self::assertSame('', (string) $result);
    }

    public function testCollapseWhitespaceToNewlineImmutability(): void
    {
        $value = XString::new(" head \t tail ");
        $collapsed = $value->collapseWhitespaceToNewline();
        self::assertSame(" head \t tail ", (string) $value);
        self::assertSame("\nhead\ntail\n", (string) $collapsed);
    }

}
