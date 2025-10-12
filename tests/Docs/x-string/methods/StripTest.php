<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\HtmlTag;
use Orryv\XString\Regex;

final class StripTest extends TestCase
{
    public function testStripBasic(): void
    {
        $value = XString::new('Hello World!');
        $result = $value->strip('World');
        self::assertSame('Hello !', (string) $result);
    }

    public function testStripMultiple(): void
    {
        $result = XString::new('lorem ipsum dolor')->strip(['lorem', 'dolor']);
        self::assertSame(' ipsum ', (string) $result);
    }

    public function testStripLimit(): void
    {
        $value = XString::new('foo bar foo bar foo');
        $result = $value->strip('foo', 2);
        self::assertSame(' bar  bar foo', (string) $result);
    }

    public function testStripReversed(): void
    {
        $value = XString::new('one two two two');
        $result = $value->strip('two', 1, true);
        self::assertSame('one two two ', (string) $result);
    }

    public function testStripHtmltag(): void
    {
        $value = XString::new('<strong>bold</strong> text');
        $result = $value->strip([HtmlTag::new('strong'), HtmlTag::closeTag('strong')]);
        self::assertSame('bold text', (string) $result);
    }

    public function testStripRegex(): void
    {
        $value = XString::new('IDs: #42, #100, #7');
        $result = $value->strip(Regex::new('/#\d+/'));
        self::assertSame('IDs: , , ', (string) $result);
    }

    public function testStripZeroLimit(): void
    {
        $value = XString::new('keep me');
        $result = $value->strip('keep', 0);
        self::assertSame('keep me', (string) $result);
    }

    public function testStripEmptySearch(): void
    {
        $value = XString::new('content');
        $this->expectException(InvalidArgumentException::class);
        $value->strip('');
    }

    public function testStripImmutable(): void
    {
        $original = XString::new('remove me once');
        $original->strip('once');
        self::assertSame('remove me once', (string) $original);
    }

}
