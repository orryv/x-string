<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\HtmlTag\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;

final class WithBodyTest extends TestCase
{
    public function testHtmlTagWithBodyBasic(): void
    {
        $tag = HtmlTag::new('span')->withBody('Hello world');
        self::assertSame('<span>Hello world', (string) $tag);
    }

    public function testHtmlTagWithBodyMultiple(): void
    {
        $tag = HtmlTag::new('div')
            ->withBody('Hello ')
            ->withBody([
                HtmlTag::new('strong')->withBody('World')->withEndTag(false),
                Newline::new(),
            ]);
        self::assertSame('<div>Hello <strong>World</strong>' . PHP_EOL, (string) $tag);
    }

}
