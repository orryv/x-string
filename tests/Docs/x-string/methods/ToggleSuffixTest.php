<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;

final class ToggleSuffixTest extends TestCase
{
    public function testToggleSuffixAdd(): void
    {
        $value = XString::new('document');
        $result = $value->toggleSuffix('.pdf');
        self::assertSame('document.pdf', (string) $result);
    }

    public function testToggleSuffixRemove(): void
    {
        $value = XString::new('document.pdf');
        $result = $value->toggleSuffix('.pdf');
        self::assertSame('document', (string) $result);
    }

    public function testToggleSuffixHtmltag(): void
    {
        $value = XString::new('<strong>alert</strong>');
        $result = $value->toggleSuffix(HtmlTag::closeTag('strong'));
        self::assertSame('<strong>alert', (string) $result);
    }

    public function testToggleSuffixNewline(): void
    {
        $value = XString::new('Summary');
        $result = $value->toggleSuffix(Newline::new("\r\n"));
        self::assertSame("Summary\r\n", (string) $result);
    }

    public function testToggleSuffixSingleRemoval(): void
    {
        $value = XString::new('value;;');
        $result = $value->toggleSuffix(';');
        self::assertSame('value;', (string) $result);
    }

    public function testToggleSuffixImmutable(): void
    {
        $original = XString::new('draft');
        $original->toggleSuffix('.md');
        self::assertSame('draft', (string) $original);
    }

    public function testToggleSuffixEmpty(): void
    {
        $value = XString::new('data');
        $this->expectException(InvalidArgumentException::class);
        $value->toggleSuffix('');
    }

}
