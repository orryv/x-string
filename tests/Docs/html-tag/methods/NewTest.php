<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\HtmlTag\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString\HtmlTag;

final class NewTest extends TestCase
{
    public function testHtmlTagNewBasic(): void
    {
        $tag = HtmlTag::new('div')->withClass('card');
        self::assertSame('<div class="card">', (string) $tag);
    }

    public function testHtmlTagNewSelfClosing(): void
    {
        $fav = HtmlTag::new('link', true)->withAttribute('rel', 'preconnect')->withAttribute('href', 'https://example.com');
        self::assertSame('<link rel="preconnect" href="https://example.com" />', (string) $fav);
    }

}
