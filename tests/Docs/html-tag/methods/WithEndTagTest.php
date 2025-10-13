<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\HtmlTag\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString\HtmlTag;

final class WithEndTagTest extends TestCase
{
    public function testHtmlTagWithEndTagDefault(): void
    {
        $tag = HtmlTag::new('p')
            ->withBody('Hello there')
            ->withEndTag();
        self::assertSame("<p>Hello there" . PHP_EOL . "</p>", (string) $tag);
    }

    public function testHtmlTagWithEndTagInline(): void
    {
        $tag = HtmlTag::new('span')
            ->withBody('Inline content')
            ->withEndTag(false);
        self::assertSame('<span>Inline content</span>', (string) $tag);
    }

    public function testHtmlTagWithEndTagEmptyBody(): void
    {
        $tag = HtmlTag::new('div')->withEndTag();
        self::assertSame('<div>' . PHP_EOL . '</div>', (string) $tag);
    }

    public function testHtmlTagWithEndTagImmutable(): void
    {
        $original = HtmlTag::new('article');
        $clone = $original->withEndTag(false);
        self::assertSame('<article>', (string) $original);
        self::assertSame('<article></article>', (string) $clone);
    }

    public function testHtmlTagWithEndTagSelfClosing(): void
    {
        $tag = HtmlTag::new('img', true);
        $this->expectException(InvalidArgumentException::class);
        $tag->withEndTag();
    }

    public function testHtmlTagWithEndTagClosing(): void
    {
        $closing = HtmlTag::closeTag('div');
        $this->expectException(InvalidArgumentException::class);
        $closing->withEndTag();
    }

}
