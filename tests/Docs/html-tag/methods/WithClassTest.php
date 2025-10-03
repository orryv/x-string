<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\HtmlTag\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString\HtmlTag;

final class WithClassTest extends TestCase
{
    public function testHtmlTagWithClassSingle(): void
    {
        $tag = HtmlTag::new('section')->withClass('hero');
        self::assertSame('<section class="hero">', (string) $tag);
    }

    public function testHtmlTagWithClassMultiple(): void
    {
        $tag = HtmlTag::new('p')->withClass(['intro', 'lead'], 'highlight');
        self::assertSame('<p class="intro lead highlight">', (string) $tag);
    }

}
