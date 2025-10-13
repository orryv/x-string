<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\HtmlTag\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString\HtmlTag;

final class WithClassTest extends TestCase
{
    public function testHtmlTagWithClassMultipleStrings(): void
    {
        $tag = HtmlTag::new('button')
            ->withClass('btn primary is-active');
        self::assertSame('<button class="btn primary is-active">', (string) $tag);
    }

    public function testHtmlTagWithClassDeduplicate(): void
    {
        $tag = HtmlTag::new('article', false, true)
            ->withClass(['feature', 'card'], ['card', 'muted']);
        self::assertSame('<article class="feature card muted">', (string) $tag);
    }

    public function testHtmlTagWithClassImmutable(): void
    {
        $original = HtmlTag::new('section')->withClass('hero');
        $clone = $original->withClass('padded');
        self::assertSame('<section class="hero">', (string) $original);
        self::assertSame('<section class="hero padded">', (string) $clone);
    }

    public function testHtmlTagWithClassMissingArgs(): void
    {
        $tag = HtmlTag::new('nav');
        $this->expectException(InvalidArgumentException::class);
        $tag->withClass();
    }

    public function testHtmlTagWithClassClosingException(): void
    {
        $closing = HtmlTag::closeTag('div');
        $this->expectException(InvalidArgumentException::class);
        $closing->withClass('nope');
    }

    public function testHtmlTagWithClassInvalidArray(): void
    {
        $tag = HtmlTag::new('div');
        $this->expectException(InvalidArgumentException::class);
        $tag->withClass(['bad class']);
    }

}
