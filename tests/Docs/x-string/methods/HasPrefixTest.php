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

final class HasPrefixTest extends TestCase
{
    public function testHasPrefixString(): void
    {
        $value = XString::new('prefix-value');
        $result = $value->hasPrefix('prefix-');
        self::assertTrue($result);
        self::assertSame('prefix-value', (string) $value);
    }

    public function testHasPrefixMissing(): void
    {
        $value = XString::new('data');
        $result = $value->hasPrefix('prefix-');
        self::assertFalse($result);
    }

    public function testHasPrefixArray(): void
    {
        $value = XString::new('https://example.com');
        $result = $value->hasPrefix(['ftp://', 'https://']);
        self::assertTrue($result);
    }

    public function testHasPrefixHtmltag(): void
    {
        $value = XString::new('<strong>alert');
        $result = $value->hasPrefix(HtmlTag::new('strong'));
        self::assertTrue($result);
    }

    public function testHasPrefixRegex(): void
    {
        $value = XString::new('#12345');
        $result = $value->hasPrefix(Regex::new('/^#[0-9]+/'));
        self::assertTrue($result);
    }

    public function testHasPrefixNewline(): void
    {
        $value = XString::new("\nSummary");
        $result = $value->hasPrefix(Newline::new("\r\n"));
        self::assertTrue($result);
    }

    public function testHasPrefixImmutable(): void
    {
        $original = XString::new('value');
        $original->hasPrefix('val');
        self::assertSame('value', (string) $original);
    }

    public function testHasPrefixEmptyArray(): void
    {
        $value = XString::new('data');
        $this->expectException(InvalidArgumentException::class);
        $value->hasPrefix([]);
    }

    public function testHasPrefixNestedArray(): void
    {
        $value = XString::new('data');
        $this->expectException(InvalidArgumentException::class);
        $value->hasPrefix([['val']]);
    }

    public function testHasPrefixEmptyFragment(): void
    {
        $value = XString::new('data');
        $this->expectException(InvalidArgumentException::class);
        $value->hasPrefix('');
    }

    public function testHasPrefixInvalidRegex(): void
    {
        $value = XString::new('value');
        $this->expectException(ValueError::class);
        $value->hasPrefix(Regex::new('/[a-z+/'));
    }

}
