<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\Docs\HtmlTag\Methods;

use PHPUnit\Framework\TestCase;
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

}
