<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;
use Orryv\XString\Regex;

final class NewTest extends TestCase
{
    public function testXstringNewPlain(): void
    {
        $xstring = XString::new('Hello world');
        self::assertInstanceOf(XString::class, $xstring);
        self::assertSame('Hello world', (string) $xstring);
    }

    public function testXstringNewArray(): void
    {
        $parts = ['Hello', ' ', 'world', '!'];
        $result = XString::new($parts);
        self::assertSame('Hello world!', (string) $result);
        self::assertSame(['Hello', ' ', 'world', '!'], $parts);
    }

    public function testXstringNewHtmlTag(): void
    {
        $fragments = [
            HtmlTag::new('p')->withClass(['intro', 'lead']),
            'Hello',
            Newline::new(),
            HtmlTag::closeTag('p'),
        ];
        $result = XString::new($fragments);
        self::assertSame("<p class=\"intro lead\">Hello" . PHP_EOL . "</p>", (string) $result);
    }

    public function testXstringNewHtmlTagWithBody(): void
    {
        $fragment = HtmlTag::new('p')
            ->withClass(['intro', 'lead'])
            ->withBody('Hello')
            ->withEndTag();
        $result = XString::new($fragment);
        self::assertSame("<p class=\"intro lead\">Hello" . PHP_EOL . "</p>", (string) $result);
    }

    public function testXstringNewEmpty(): void
    {
        $xstring = XString::new();
        self::assertSame('', (string) $xstring);
    }

    public function testXstringNewInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        XString::new(['valid', 123]);
    }

    public function testXstringNewFromXstring(): void
    {
        $source = XString::new('immutable data');
        $clone = XString::new($source);
        self::assertSame('immutable data', (string) $clone);
        self::assertNotSame($source, $clone);
    }

    public function testXstringNewRegex(): void
    {
        $pattern = Regex::new('/foo/');
        $result = XString::new([$pattern, 'bar']);
        self::assertSame('/foo/bar', (string) $result);
    }

}
