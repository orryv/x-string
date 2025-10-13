<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\HtmlTag\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString\HtmlTag;

final class WithAttributeTest extends TestCase
{
    public function testHtmlTagWithAttributeBasic(): void
    {
        $tag = HtmlTag::new('input', true)
            ->withAttribute('type', 'email')
            ->withAttribute('required');
        self::assertSame('<input type="email" required />', (string) $tag);
    }

    public function testHtmlTagWithAttributeCase(): void
    {
        $tag = HtmlTag::new('div')->withAttribute('Data-State', 'active', true);
        self::assertSame('<div Data-State="active">', (string) $tag);
    }

    public function testHtmlTagWithAttributeClass(): void
    {
        $tag = HtmlTag::new('div')->withClass('card')->withAttribute('class', 'primary shadow');
        self::assertSame('<div class="card primary shadow">', (string) $tag);
    }

    public function testHtmlTagWithAttributeId(): void
    {
        $tag = HtmlTag::new('section')->withAttribute('id', ' hero ');
        self::assertSame('<section id="hero">', (string) $tag);
    }

    public function testHtmlTagWithAttributeImmutable(): void
    {
        $original = HtmlTag::new('a')->withAttribute('href', '#top');
        $clone = $original->withAttribute('target', '_blank');
        self::assertSame('<a href="#top">', (string) $original);
        self::assertSame('<a href="#top" target="_blank">', (string) $clone);
    }

    public function testHtmlTagWithAttributeClosing(): void
    {
        $closing = HtmlTag::closeTag('div');
        $this->expectException(InvalidArgumentException::class);
        $closing->withAttribute('data-test', 'nope');
    }

    public function testHtmlTagWithAttributeEmptyName(): void
    {
        $tag = HtmlTag::new('span');
        $this->expectException(InvalidArgumentException::class);
        $tag->withAttribute('   ', 'value');
    }

    public function testHtmlTagWithAttributeClassNull(): void
    {
        $tag = HtmlTag::new('div');
        $this->expectException(InvalidArgumentException::class);
        $tag->withAttribute('class');
    }

}
