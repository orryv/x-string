<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\HtmlTag;

final class BetweenAllTest extends TestCase
{
    public function testBetweenAllBasic(): void
    {
        $text = XString::new('[one][two][three]');
        $segments = $text->betweenAll('[', ']');
        self::assertSame(['one', 'two', 'three'], $segments);
        self::assertSame('[one][two][three]', (string) $text);
    }

    public function testBetweenAllReversed(): void
    {
        $text = XString::new('<a>first</a><a>second</a><a>third</a>');
        $segments = $text->betweenAll('<a>', '</a>', reversed: true);
        self::assertSame(['third', 'second', 'first'], $segments);
    }

    public function testBetweenAllAlternatives(): void
    {
        $text = XString::new('[one]{two}(three)');
        $segments = $text->betweenAll(['[', '{', '('], [']', '}', ')']);
        self::assertSame(['one', 'two', 'three'], $segments);
    }

    public function testBetweenAllSequence(): void
    {
        $html = XString::new('<article><section><p>Alpha</p></section></article><article><section><p>Beta</p></section></article>');
        $segments = $html->betweenAll([['<article>', '<section>', '<p>']], [['</p>', '</section>', '</article>']]);
        self::assertSame(['Alpha', 'Beta'], $segments);
    }

    public function testBetweenAllHtml(): void
    {
        $template = XString::new('<div class="note">First</div><div class="note">Second</div>');
        $segments = $template->betweenAll(HtmlTag::new('div')->withClass('note'), HtmlTag::closeTag('div'));
        self::assertSame(['First', 'Second'], $segments);
    }

    public function testBetweenAllEmpty(): void
    {
        $segments = XString::new('no markers here')->betweenAll('[', ']');
        self::assertSame([], $segments);
    }

    public function testBetweenAllInvalid(): void
    {
        $text = XString::new('example');
        $this->expectException(InvalidArgumentException::class);
        $text->betweenAll(['', 'start'], 'end');
    }

}
