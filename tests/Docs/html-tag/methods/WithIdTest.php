<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\HtmlTag\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString\HtmlTag;

final class WithIdTest extends TestCase
{
    public function testHtmlTagWithIdBasic(): void
    {
        $tag = HtmlTag::new('nav')->withId('primary-nav');
        self::assertSame('<nav id="primary-nav">', (string) $tag);
    }

    public function testHtmlTagWithIdTrim(): void
    {
        $tag = HtmlTag::new('section')->withId("  hero  ");
        self::assertSame('<section id="hero">', (string) $tag);
    }

    public function testHtmlTagWithIdImmutable(): void
    {
        $original = HtmlTag::new('aside');
        $clone = $original->withId('sidebar');
        self::assertSame('<aside>', (string) $original);
        self::assertSame('<aside id="sidebar">', (string) $clone);
    }

    public function testHtmlTagWithIdReplace(): void
    {
        $tag = HtmlTag::new('form')
            ->withId('login-form')
            ->withId('signup-form');
        self::assertSame('<form id="signup-form">', (string) $tag);
    }

    public function testHtmlTagWithIdClosing(): void
    {
        $closing = HtmlTag::closeTag('section');
        $this->expectException(InvalidArgumentException::class);
        $closing->withId('nope');
    }

    public function testHtmlTagWithIdEmpty(): void
    {
        $tag = HtmlTag::new('div');
        $this->expectException(InvalidArgumentException::class);
        $tag->withId('   ');
    }

}
