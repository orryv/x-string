<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;

final class TogglePrefixTest extends TestCase
{
    public function testTogglePrefixAdd(): void
    {
        $value = XString::new('example.com');
        $result = $value->togglePrefix('https://');
        self::assertSame('https://example.com', (string) $result);
    }

    public function testTogglePrefixRemove(): void
    {
        $value = XString::new('https://example.com');
        $result = $value->togglePrefix('https://');
        self::assertSame('example.com', (string) $result);
    }

    public function testTogglePrefixHtmltag(): void
    {
        $value = XString::new('<strong>alert');
        $result = $value->togglePrefix(HtmlTag::new('strong'));
        self::assertSame('alert', (string) $result);
    }

    public function testTogglePrefixNewline(): void
    {
        $value = XString::new('Title');
        $result = $value->togglePrefix(Newline::new("\n"));
        self::assertSame("\nTitle", (string) $result);
    }

    public function testTogglePrefixSingleRemoval(): void
    {
        $value = XString::new('##heading');
        $result = $value->togglePrefix('#');
        self::assertSame('#heading', (string) $result);
    }

    public function testTogglePrefixImmutable(): void
    {
        $original = XString::new('subject');
        $original->togglePrefix('> ');
        self::assertSame('subject', (string) $original);
    }

    public function testTogglePrefixEmpty(): void
    {
        $value = XString::new('data');
        $this->expectException(InvalidArgumentException::class);
        $value->togglePrefix('');
    }

}
