<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;
use Orryv\XString\Regex;

final class CountOccurrencesTest extends TestCase
{
    public function testCountOccurrencesBasic(): void
    {
        $text = XString::new('banana bread banana');
        self::assertSame(2, $text->countOccurrences('banana'));
        self::assertSame('banana bread banana', (string) $text);
    }

    public function testCountOccurrencesArray(): void
    {
        $palette = XString::new('Colors: red, blue, red, green');
        self::assertSame(3, $palette->countOccurrences(['red', 'blue']));
        self::assertSame(0, $palette->countOccurrences(['yellow']));
    }

    public function testCountOccurrencesRegex(): void
    {
        $list = XString::new('IDs: A-10, B-20, C-30');
        self::assertSame(3, $list->countOccurrences(Regex::new('/[A-Z]-\d+/')));
    }

    public function testCountOccurrencesHtml(): void
    {
        $html = XString::new('<ul><li>One</li><li>Two</li><li>Three</li></ul>');
        self::assertSame(3, $html->countOccurrences(HtmlTag::new('li')));
        self::assertSame(0, $html->countOccurrences(HtmlTag::new('section')));
    }

    public function testCountOccurrencesNewline(): void
    {
        $lines = XString::new("Line 1\nLine 2\n");
        self::assertSame(2, $lines->countOccurrences(Newline::new("\r\n")));
        self::assertSame(2, $lines->countOccurrences(Newline::new("\n")));
    }

    public function testCountOccurrencesNone(): void
    {
        $text = XString::new('abc');
        self::assertSame(0, $text->countOccurrences('z'));
    }

    public function testCountOccurrencesEmpty(): void
    {
        $value = XString::new('content');
        $this->expectException(InvalidArgumentException::class);
        $value->countOccurrences('');
    }

    public function testCountOccurrencesEmptyRegex(): void
    {
        $value = XString::new('anything');
        $this->expectException(InvalidArgumentException::class);
        $value->countOccurrences(Regex::new('/^/m'));
    }

}
