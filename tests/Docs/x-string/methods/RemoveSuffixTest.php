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

final class RemoveSuffixTest extends TestCase
{
    public function testRemoveSuffixString(): void
    {
        $value = XString::new('report.txt');
        $result = $value->removeSuffix('.txt');
        self::assertSame('report', (string) $result);
    }

    public function testRemoveSuffixArray(): void
    {
        $value = XString::new('archive.tar.gz');
        $result = $value->removeSuffix(['.zip', '.tar.gz']);
        self::assertSame('archive', (string) $result);
    }

    public function testRemoveSuffixHtmltag(): void
    {
        $value = XString::new('<strong>alert</strong>');
        $result = $value->removeSuffix(HtmlTag::closeTag('strong'));
        self::assertSame('<strong>alert', (string) $result);
    }

    public function testRemoveSuffixRegex(): void
    {
        $value = XString::new('Invoice 2024');
        $result = $value->removeSuffix(Regex::new('/\s\d+$/'));
        self::assertSame('Invoice', (string) $result);
    }

    public function testRemoveSuffixNewline(): void
    {
        $value = XString::new("Summary\n");
        $result = $value->removeSuffix(Newline::new("\r\n"));
        self::assertSame('Summary', (string) $result);
    }

    public function testRemoveSuffixNoMatch(): void
    {
        $value = XString::new('document.pdf');
        $result = $value->removeSuffix(['.zip', '.tar']);
        self::assertSame('document.pdf', (string) $result);
    }

    public function testRemoveSuffixImmutable(): void
    {
        $original = XString::new('notes.md');
        $original->removeSuffix('.md');
        self::assertSame('notes.md', (string) $original);
    }

    public function testRemoveSuffixEmptyArray(): void
    {
        $value = XString::new('data.csv');
        $this->expectException(InvalidArgumentException::class);
        $value->removeSuffix([]);
    }

    public function testRemoveSuffixNestedArray(): void
    {
        $value = XString::new('data.csv');
        $this->expectException(InvalidArgumentException::class);
        $value->removeSuffix([['.csv']]);
    }

    public function testRemoveSuffixEmptyFragment(): void
    {
        $value = XString::new('data.csv');
        $this->expectException(InvalidArgumentException::class);
        $value->removeSuffix('');
    }

    public function testRemoveSuffixInvalidRegex(): void
    {
        $value = XString::new('log-01');
        $this->expectException(ValueError::class);
        $value->removeSuffix(Regex::new('/[a-z+/'));
    }

}
