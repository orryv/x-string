<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\HtmlTag\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;
use Stringable;

final class WithBodyTest extends TestCase
{
    public function testHtmlTagWithBodyBasic(): void
    {
        $tag = HtmlTag::new('span')->withBody('Hello world');
        self::assertSame('<span>Hello world', (string) $tag);
    }

    public function testHtmlTagWithBodyFragments(): void
    {
        $tag = HtmlTag::new('div')
            ->withBody([
                HtmlTag::new('strong')->withBody('Title')->withEndTag(false),
                Newline::new(),
                new class implements Stringable {
                    public function __toString(): string
                    {
                        return 'Summary';
                    }
                },
            ]);
        self::assertSame('<div><strong>Title</strong>' . PHP_EOL . 'Summary', (string) $tag);
    }

    public function testHtmlTagWithBodyAppend(): void
    {
        $tag = HtmlTag::new('p')
            ->withBody('First ')
            ->withBody('Second');
        self::assertSame('<p>First Second', (string) $tag);
    }

    public function testHtmlTagWithBodyImmutable(): void
    {
        $original = HtmlTag::new('article');
        $clone = $original->withBody('Summary');
        self::assertSame('<article>', (string) $original);
        self::assertSame('<article>Summary', (string) $clone);
    }

    public function testHtmlTagWithBodySelfClosing(): void
    {
        $tag = HtmlTag::new('img', true);
        $this->expectException(InvalidArgumentException::class);
        $tag->withBody('image');
    }

    public function testHtmlTagWithBodyClosing(): void
    {
        $closing = HtmlTag::closeTag('div');
        $this->expectException(InvalidArgumentException::class);
        $closing->withBody('nope');
    }

}
