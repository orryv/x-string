<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class CollapseWhitespaceToSpaceTest extends TestCase
{
    public function testCollapseWhitespaceToSpaceBasic(): void
    {
        $paragraph = XString::new("Line 1\tLine 2\nLine 3");
        $result = $paragraph->collapseWhitespaceToSpace();
        self::assertSame('Line 1 Line 2 Line 3', (string) $result);
        self::assertSame("Line 1\tLine 2\nLine 3", (string) $paragraph);
    }

    public function testCollapseWhitespaceToSpaceRuns(): void
    {
        $text = XString::new("Too     many\t\t\tspaces    here");
        $result = $text->collapseWhitespaceToSpace();
        self::assertSame('Too many spaces here', (string) $result);
    }

    public function testCollapseWhitespaceToSpaceCrlf(): void
    {
        $text = XString::new("One\r\nTwo\r\n\nThree");
        $result = $text->collapseWhitespaceToSpace();
        self::assertSame('One Two Three', (string) $result);
    }

    public function testCollapseWhitespaceToSpaceEmpty(): void
    {
        $text = XString::new('');
        $result = $text->collapseWhitespaceToSpace();
        self::assertSame('', (string) $result);
    }

    public function testCollapseWhitespaceToSpaceImmutability(): void
    {
        $value = XString::new(" \t spaced \n ");
        $collapsed = $value->collapseWhitespaceToSpace();
        self::assertSame(" \t spaced \n ", (string) $value);
        self::assertSame(' spaced ', (string) $collapsed);
    }

}
