<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class CollapseWhitespaceTest extends TestCase
{
    public function testCollapseWhitespaceSpaces(): void
    {
        $text = XString::new('Multiple    spaces   here');
        $result = $text->collapseWhitespace();
        self::assertSame('Multiple spaces here', (string) $result);
        self::assertSame('Multiple    spaces   here', (string) $text);
    }

    public function testCollapseWhitespaceTabs(): void
    {
        $text = XString::new("Tabs\t\t\tand  spaces");
        $result = $text->collapseWhitespace(space: true, tab: true, newline: false);
        self::assertSame("Tabs\tand spaces", (string) $result);
    }

    public function testCollapseWhitespaceNewlines(): void
    {
        $text = XString::new("Line 1\n\n\nLine 2\r\n\r\nLine 3");
        $result = $text->collapseWhitespace(newline: true);
        self::assertSame("Line 1\nLine 2\r\nLine 3", (string) $result);
    }

    public function testCollapseWhitespaceMixed(): void
    {
        $text = XString::new("\n\n\t\t  ");
        $result = $text->collapseWhitespace(newline: true);
        self::assertSame("\n\t ", (string) $result);
    }

    public function testCollapseWhitespaceCrlf(): void
    {
        $text = XString::new("\n\r\n");
        $result = $text->collapseWhitespace(newline: true);
        self::assertSame("\r\n", (string) $result);
    }

    public function testCollapseWhitespaceDisabled(): void
    {
        $text = XString::new("Keep\n\n  everything\t\t as-is");
        $result = $text->collapseWhitespace(space: false, tab: false, newline: false);
        self::assertSame("Keep\n\n  everything\t\t as-is", (string) $result);
    }

    public function testCollapseWhitespaceEmpty(): void
    {
        $text = XString::new('');
        $result = $text->collapseWhitespace();
        self::assertSame('', (string) $result);
    }

    public function testCollapseWhitespaceImmutability(): void
    {
        $text = XString::new("Original\t\tvalue");
        $collapsed = $text->collapseWhitespace();
        self::assertSame("Original\t\tvalue", (string) $text);
        self::assertSame("Original\tvalue", (string) $collapsed);
    }

}
