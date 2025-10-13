<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;
use Orryv\XString\Regex;

final class RemovePrefixTest extends TestCase
{
    public function testRemovePrefixString(): void
    {
        $value = XString::new('prefix-value');
        $result = $value->removePrefix('prefix-');
        self::assertSame('value', (string) $result);
    }

    public function testRemovePrefixArray(): void
    {
        $value = XString::new('https://example.com');
        $result = $value->removePrefix(['http://', 'https://']);
        self::assertSame('example.com', (string) $result);
    }

    public function testRemovePrefixHtmltag(): void
    {
        $value = XString::new('<p>Hello</p>');
        $result = $value->removePrefix(HtmlTag::new('p'));
        self::assertSame('Hello</p>', (string) $result);
    }

    public function testRemovePrefixRegex(): void
    {
        $value = XString::new('123-Report');
        $result = $value->removePrefix(Regex::new('/^\d+-/'));
        self::assertSame('Report', (string) $result);
    }

    public function testRemovePrefixNewline(): void
    {
        $value = XString::new("\r\nAgenda");
        $result = $value->removePrefix(Newline::new("\r\n"));
        self::assertSame('Agenda', (string) $result);
    }

    public function testRemovePrefixNoMatch(): void
    {
        $value = XString::new('data');
        $result = $value->removePrefix('prefix-');
        self::assertSame('data', (string) $result);
    }

    public function testRemovePrefixImmutable(): void
    {
        $original = XString::new('token');
        $original->removePrefix('pre');
        self::assertSame('token', (string) $original);
    }

    public function testRemovePrefixEmptyArray(): void
    {
        $value = XString::new('content');
        $this->expectException(InvalidArgumentException::class);
        $value->removePrefix([]);
    }

    public function testRemovePrefixNestedArray(): void
    {
        $value = XString::new('content');
        $this->expectException(InvalidArgumentException::class);
        $value->removePrefix([['pre']]);
    }

}
