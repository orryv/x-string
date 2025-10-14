<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;
use Orryv\XString\Regex;
use ValueError;

final class SplitTest extends TestCase
{
    public function testSplitBasicString(): void
    {
        $value = XString::new('first,second,third');
        $result = $value->split(',');
        self::assertSame(['first', 'second', 'third'], $result);
    }

    public function testSplitLimit(): void
    {
        $value = XString::new('a,b,c,d');
        $result = $value->split(',', 3);
        self::assertSame(['a', 'b', 'c,d'], $result);
    }

    public function testSplitMultipleDelimiters(): void
    {
        $value = XString::new('one,two;three');
        $result = $value->split([',', ';']);
        self::assertSame(['one', 'two', 'three'], $result);
    }

    public function testSplitRegex(): void
    {
        $value = XString::new("word1  word2\tword3");
        $result = $value->split(Regex::new('/\s+/'));
        self::assertSame(['word1', 'word2', 'word3'], $result);
    }

    public function testSplitNewline(): void
    {
        $value = XString::new("first\nsecond");
        $result = $value->split(Newline::new("\r\n"));
        self::assertSame(['first', 'second'], $result);
    }

    public function testSplitHtmltag(): void
    {
        $value = XString::new('first<br>second<br>third');
        $result = $value->split(HtmlTag::new('br', true));
        self::assertSame(['first', 'second', 'third'], $result);
    }

    public function testSplitMode(): void
    {
        $value = XString::new('ä|ö|ü')->withMode('bytes');
        $result = $value->split('|');
        self::assertSame(['ä', 'ö', 'ü'], $result);
    }

    public function testSplitEmptyString(): void
    {
        $value = XString::new('');
        $result = $value->split(',');
        self::assertSame([], $result);
    }

    public function testSplitInvalidLimit(): void
    {
        $value = XString::new('a,b');
        $this->expectException(InvalidArgumentException::class);
        $value->split(',', 0);
    }

    public function testSplitEmptyArray(): void
    {
        $value = XString::new('a,b');
        $this->expectException(InvalidArgumentException::class);
        $value->split([]);
    }

    public function testSplitEmptyFragment(): void
    {
        $value = XString::new('a,b');
        $this->expectException(InvalidArgumentException::class);
        $value->split('');
    }

    public function testSplitRegexEmptyMatch(): void
    {
        $value = XString::new('abc');
        $this->expectException(InvalidArgumentException::class);
        $value->split(Regex::new('/a*/'));
    }

    public function testSplitInvalidRegex(): void
    {
        $value = XString::new('abc');
        $this->expectException(ValueError::class);
        $value->split(Regex::new('/[a-z+/'));
    }

}
