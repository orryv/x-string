<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\Docs\HtmlTag\Methods;

use PHPUnit\Framework\TestCase;
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

}
