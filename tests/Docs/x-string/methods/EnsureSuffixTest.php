<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;

final class EnsureSuffixTest extends TestCase
{
    public function testEnsureSuffixAdd(): void
    {
        $value = XString::new('report');
        $result = $value->ensureSuffix('.pdf');
        self::assertSame('report.pdf', (string) $result);
    }

    public function testEnsureSuffixExisting(): void
    {
        $value = XString::new('report.pdf');
        $result = $value->ensureSuffix('.pdf');
        self::assertSame('report.pdf', (string) $result);
    }

    public function testEnsureSuffixHtmltag(): void
    {
        $value = XString::new('<strong>important');
        $result = $value->ensureSuffix(HtmlTag::closeTag('strong'));
        self::assertSame('<strong>important</strong>', (string) $result);
    }

    public function testEnsureSuffixNewline(): void
    {
        $value = XString::new("Line");
        $result = $value->ensureSuffix(Newline::new("\r\n"));
        self::assertSame("Line\r\n", (string) $result);
    }

    public function testEnsureSuffixEmpty(): void
    {
        $value = XString::new('data');
        $this->expectException(InvalidArgumentException::class);
        $value->ensureSuffix('');
    }

    public function testEnsureSuffixImmutable(): void
    {
        $original = XString::new('value');
        $original->ensureSuffix(';');
        self::assertSame('value', (string) $original);
    }

}
