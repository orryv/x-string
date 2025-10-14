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

final class HasSuffixTest extends TestCase
{
    public function testHasSuffixString(): void
    {
        $value = XString::new('report.pdf');
        $result = $value->hasSuffix('.pdf');
        self::assertTrue($result);
        self::assertSame('report.pdf', (string) $value);
    }

    public function testHasSuffixMissing(): void
    {
        $value = XString::new('report.pdf');
        $result = $value->hasSuffix('.zip');
        self::assertFalse($result);
    }

    public function testHasSuffixArray(): void
    {
        $value = XString::new('archive.tar.gz');
        $result = $value->hasSuffix(['.zip', '.tar.gz']);
        self::assertTrue($result);
    }

    public function testHasSuffixHtmltag(): void
    {
        $value = XString::new('<strong>alert</strong>');
        $result = $value->hasSuffix(HtmlTag::closeTag('strong'));
        self::assertTrue($result);
    }

    public function testHasSuffixRegex(): void
    {
        $value = XString::new('invoice-2024');
        $result = $value->hasSuffix(Regex::new('/\d{4}$/'));
        self::assertTrue($result);
    }

    public function testHasSuffixNewline(): void
    {
        $value = XString::new("Summary\n");
        $result = $value->hasSuffix(Newline::new("\r\n"));
        self::assertTrue($result);
    }

    public function testHasSuffixImmutable(): void
    {
        $original = XString::new('value');
        $original->hasSuffix('ue');
        self::assertSame('value', (string) $original);
    }

    public function testHasSuffixEmptyArray(): void
    {
        $value = XString::new('data');
        $this->expectException(InvalidArgumentException::class);
        $value->hasSuffix([]);
    }

    public function testHasSuffixNestedArray(): void
    {
        $value = XString::new('data');
        $this->expectException(InvalidArgumentException::class);
        $value->hasSuffix([['.txt']]);
    }

    public function testHasSuffixEmptyFragment(): void
    {
        $value = XString::new('data');
        $this->expectException(InvalidArgumentException::class);
        $value->hasSuffix('');
    }

    public function testHasSuffixInvalidRegex(): void
    {
        $value = XString::new('value');
        $this->expectException(ValueError::class);
        $value->hasSuffix(Regex::new('/[a-z+/'));
    }

}
