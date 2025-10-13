<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;
use ValueError;

final class HtmlEscapeTest extends TestCase
{
    public function testHtmlEscapeBasic(): void
    {
        $value = XString::new("<a href='/?q=1&2'>Link</a>");
        $result = $value->htmlEscape();
        self::assertSame('&lt;a href=&apos;/?q=1&amp;2&apos;&gt;Link&lt;/a&gt;', (string) $result);
    }

    public function testHtmlEscapeNoquotes(): void
    {
        $value = XString::new('"quoted" & <tag>');
        $result = $value->htmlEscape(ENT_NOQUOTES);
        self::assertSame('"quoted" &amp; &lt;tag&gt;', (string) $result);
    }

    public function testHtmlEscapeEncoding(): void
    {
        $bytes = "\xA0"; // non-breaking space (valid ISO-8859-1, invalid UTF-8)
        $result = XString::new($bytes)->htmlEscape(ENT_QUOTES | ENT_HTML5, 'ISO-8859-1');
        self::assertSame('a0', bin2hex((string) $result));
    }

    public function testHtmlEscapeEmpty(): void
    {
        $result = XString::new('')->htmlEscape();
        self::assertSame('', (string) $result);
    }

    public function testHtmlEscapeInvalid(): void
    {
        $this->expectException(ValueError::class);
        XString::new('text')->htmlEscape(ENT_QUOTES, 'FAKE-ENCODING');
    }

    public function testHtmlEscapeImmutability(): void
    {
        $value = XString::new('<b>bold</b>');
        $value->htmlEscape();
        self::assertSame('<b>bold</b>', (string) $value);
    }

}
