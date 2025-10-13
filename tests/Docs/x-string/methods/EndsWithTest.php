<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;
use Orryv\XString\Regex;

final class EndsWithTest extends TestCase
{
    public function testEndsWithBasic(): void
    {
        $file = XString::new('archive.tar.gz');
        self::assertTrue($file->endsWith('.gz'));
        self::assertFalse($file->endsWith('.tar'));
        self::assertSame('archive.tar.gz', (string) $file);
    }

    public function testEndsWithArray(): void
    {
        $report = XString::new('summary-final.txt');
        self::assertTrue($report->endsWith(['.pdf', '.txt']));
        self::assertFalse($report->endsWith(['.docx', '.xlsx']));
    }

    public function testEndsWithRegex(): void
    {
        $invoice = XString::new('Invoice-2025.pdf');
        self::assertTrue($invoice->endsWith(Regex::new('/\d+\.pdf$/')));
        self::assertFalse($invoice->endsWith(Regex::new('/\.zip$/')));
    }

    public function testEndsWithHtml(): void
    {
        $markup = XString::new('<p>Hello</p>');
        self::assertTrue($markup->endsWith(HtmlTag::closeTag('p')));
        self::assertFalse($markup->endsWith(HtmlTag::closeTag('div')));
    }

    public function testEndsWithNewline(): void
    {
        $log = XString::new("Line 1\n");
        self::assertTrue($log->endsWith(Newline::new("\r\n")));
        self::assertTrue($log->endsWith(Newline::new("\n")));
    }

    public function testEndsWithEmpty(): void
    {
        $string = XString::new('content');
        $this->expectException(InvalidArgumentException::class);
        $string->endsWith('');
    }

}
