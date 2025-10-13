<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;
use Orryv\XString\Regex;

final class EqualsTest extends TestCase
{
    public function testEqualsBasic(): void
    {
        $status = XString::new('Ready');
        self::assertTrue($status->equals('Ready'));
        self::assertFalse($status->equals('ready'));
        self::assertSame('Ready', (string) $status);
    }

    public function testEqualsCaseInsensitive(): void
    {
        $status = XString::new('Ready');
        self::assertTrue($status->equals('ready', case_sensitive: false));
        self::assertFalse($status->equals('waiting', case_sensitive: false));
    }

    public function testEqualsArray(): void
    {
        $env = XString::new('ENV=production');
        self::assertTrue($env->equals(['env=prod', 'ENV=production']));
        self::assertFalse($env->equals(['ENV=staging', 'ENV=testing']));
    }

    public function testEqualsRegex(): void
    {
        $invoice = XString::new('Invoice-12345');
        self::assertTrue($invoice->equals(Regex::new('/^Invoice-\d+$/')));
        self::assertFalse($invoice->equals(Regex::new('/Invoice/')));
    }

    public function testEqualsNewline(): void
    {
        $newline = XString::new("\n");
        self::assertTrue($newline->equals(Newline::new("\r\n")));
        self::assertTrue($newline->equals(Newline::new("\n")));
    }

    public function testEqualsHtml(): void
    {
        $html = XString::new('<p>Hello</p>');
        $tag = HtmlTag::new('p')->withBody('Hello')->withEndTag(false);
        self::assertTrue($html->equals($tag));
        self::assertFalse($html->equals(HtmlTag::new('p')->withBody('Hello')));
    }

    public function testEqualsEmptyArray(): void
    {
        $value = XString::new('anything');
        $this->expectException(InvalidArgumentException::class);
        $value->equals([]);
    }

}
