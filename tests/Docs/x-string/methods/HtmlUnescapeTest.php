<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class HtmlUnescapeTest extends TestCase
{
    public function testHtmlUnescapeBasic(): void
    {
        $value = XString::new('&lt;div&gt;Hello&lt;/div&gt;');
        $result = $value->htmlUnescape();
        self::assertSame('<div>Hello</div>', (string) $result);
    }

    public function testHtmlUnescapeHtml5Entities(): void
    {
        $value = XString::new('&apos;alpha&apos; &amp; &quot;beta&quot;');
        $result = $value->htmlUnescape();
        self::assertSame("'alpha' & \"beta\"", (string) $result);
    }

    public function testHtmlUnescapeBytesMode(): void
    {
        $value = XString::new('&lt;Caf&eacute;&gt;')->withMode('bytes');
        $result = $value->htmlUnescape();
        self::assertSame('<Café>', (string) $result);
    }

    public function testHtmlUnescapeMixed(): void
    {
        $value = XString::new('Fish &amp; Chips &amp; More');
        $result = $value->htmlUnescape();
        self::assertSame('Fish & Chips & More', (string) $result);
    }

    public function testHtmlUnescapeEmpty(): void
    {
        $result = XString::new('')->htmlUnescape();
        self::assertSame('', (string) $result);
    }

    public function testHtmlUnescapeImmutable(): void
    {
        $value = XString::new('&lt;b&gt;bold&lt;/b&gt;');
        $value->htmlUnescape();
        self::assertSame('&lt;b&gt;bold&lt;/b&gt;', (string) $value);
    }

}
