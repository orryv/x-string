<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;
use Orryv\XString\HtmlTag;

final class WrapTest extends TestCase
{
    public function testWrapBasic(): void
    {
        $xstring = XString::new('welcome');
        $result = $xstring->wrap('**');
        self::assertSame('**welcome**', (string) $result);
    }

    public function testWrapDifferentBeforeAfter(): void
    {
        $xstring = XString::new('title');
        $result = $xstring->wrap('<', '>');
        self::assertSame('<title>', (string) $result);
    }

    public function testWrapHtmltag(): void
    {
        $xstring = XString::new('Important');
        $result = $xstring->wrap(HtmlTag::new('strong'), HtmlTag::closeTag('strong'));
        self::assertSame('<strong>Important</strong>', (string) $result);
    }

    public function testWrapEmpty(): void
    {
        $xstring = XString::new('');
        $result = $xstring->wrap('(', ')');
        self::assertSame('()', (string) $result);
    }

    public function testWrapImmutability(): void
    {
        $xstring = XString::new('immutable');
        $wrapped = $xstring->wrap('[', ']');
        self::assertSame('immutable', (string) $xstring);
        self::assertSame('[immutable]', (string) $wrapped);
    }

    public function testWrapByteMode(): void
    {
        $xstring = XString::new('Å')->withMode('bytes');
        $result = $xstring->wrap('[', ']');
        self::assertSame('[Å]', (string) $result);
        self::assertSame(4, $result->length());
    }

}
