<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\HtmlTag\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
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

    public function testHtmlTagCloseWithXstring(): void
    {
        $fragment = XString::new('<section><p>Body</p></section>');
        self::assertTrue($fragment->contains(HtmlTag::closeTag('section')));
        self::assertFalse($fragment->contains(HtmlTag::closeTag('article')));
    }

    public function testHtmlTagCloseInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        HtmlTag::closeTag('');
    }

}
