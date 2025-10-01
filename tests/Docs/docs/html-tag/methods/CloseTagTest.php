<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\Docs\HtmlTag\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString\HtmlTag;

final class CloseTagTest extends TestCase
{
    public function testHtmlTagCloseBasic(): void
    {
        $closing = HtmlTag::closeTag('article');
        self::assertSame('</article>', (string) $closing);
    }

    public function testHtmlTagCloseCase(): void
    {
        $closing = HtmlTag::closeTag('MyComponent', true);
        self::assertSame('</MyComponent>', (string) $closing);
    }

}
