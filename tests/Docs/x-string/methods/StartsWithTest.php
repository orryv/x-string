<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;
use Orryv\XString\Regex;

final class StartsWithTest extends TestCase
{
    public function testStartsWithBasic(): void
    {
        $title = XString::new('Framework: XString');
        self::assertTrue($title->startsWith('Framework'));
        self::assertFalse($title->startsWith('XString'));
        self::assertSame('Framework: XString', (string) $title);
    }

    public function testStartsWithArray(): void
    {
        $slug = XString::new('feature/add-index-method');
        self::assertTrue($slug->startsWith(['feature/', 'hotfix/']));
        self::assertFalse($slug->startsWith(['bugfix/', 'release/']));
    }

    public function testStartsWithRegex(): void
    {
        $version = XString::new('Version: 1.2.3');
        self::assertTrue($version->startsWith(Regex::new('/^Version:/')));
        self::assertFalse($version->startsWith(Regex::new('/^Release:/')));
    }

    public function testStartsWithHtml(): void
    {
        $document = XString::new('<h1 id="title">Heading</h1>');
        $heading = HtmlTag::new('h1')->withId('title');
        self::assertTrue($document->startsWith($heading));
        self::assertFalse($document->startsWith(HtmlTag::new('section')));
    }

    public function testStartsWithNewline(): void
    {
        $block = XString::new("    Item A\nItem B");
        $lineMatcher = Newline::new()->startsWith('Item', trim: true);
        self::assertTrue($block->startsWith($lineMatcher));
        self::assertFalse(XString::new('    Other')->startsWith($lineMatcher));
    }

    public function testStartsWithEmpty(): void
    {
        $string = XString::new('content');
        $this->expectException(InvalidArgumentException::class);
        $string->startsWith('');
    }

}
