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

final class ExplodeTest extends TestCase
{
    public function testExplodeBasic(): void
    {
        $xstring = XString::new('alpha,beta,gamma');
        $parts = $xstring->explode(',');
        self::assertSame(['alpha', 'beta', 'gamma'], $parts);
        self::assertSame('alpha,beta,gamma', (string) $xstring);
    }

    public function testExplodeLimit(): void
    {
        $date = XString::new('2024-01-01-UTC');
        $parts = $date->explode('-', limit: 3);
        self::assertSame(['2024', '01', '01-UTC'], $parts);
    }

    public function testExplodeMultipleDelimiters(): void
    {
        $tokens = XString::new('one|two/three');
        $parts = $tokens->explode(['|', '/']);
        self::assertSame(['one', 'two', 'three'], $parts);
    }

    public function testExplodeRegex(): void
    {
        $slug = XString::new('foo--bar--baz');
        $parts = $slug->explode(Regex::new('/--/'));
        self::assertSame(['foo', 'bar', 'baz'], $parts);
    }

    public function testExplodeHtmlTag(): void
    {
        $html = XString::new('Hello<br>World<br />!');
        $parts = $html->explode(HtmlTag::new('br', true));
        self::assertSame(['Hello', 'World', '!'], $parts);
    }

    public function testExplodeNewline(): void
    {
        $log = XString::new("first\nsecond\nthird");
        $parts = $log->explode(Newline::new("\r\n"));
        self::assertSame(['first', 'second', 'third'], $parts);
    }

    public function testExplodeEmpty(): void
    {
        $result = XString::new('')->explode(',');
        self::assertSame([], $result);
    }

    public function testExplodeMode(): void
    {
        $xstring = XString::new('Å-ß-ç')->withMode('bytes');
        $parts = $xstring->explode('-');
        self::assertSame(['Å', 'ß', 'ç'], $parts);
    }

    public function testExplodeInvalidLimit(): void
    {
        $xstring = XString::new('one,two');
        $this->expectException(InvalidArgumentException::class);
        $xstring->explode(',', limit: 0);
    }

    public function testExplodeEmptyDelimiter(): void
    {
        $xstring = XString::new('one two');
        $this->expectException(InvalidArgumentException::class);
        $xstring->explode('');
    }

    public function testExplodeEmptyRegex(): void
    {
        $xstring = XString::new('value');
        $this->expectException(InvalidArgumentException::class);
        $xstring->explode(Regex::new('/\s*/'));
    }

    public function testExplodeInvalidRegex(): void
    {
        $xstring = XString::new('value');
        $this->expectException(ValueError::class);
        $xstring->explode(Regex::new('/[unbalanced/'));
    }

}
