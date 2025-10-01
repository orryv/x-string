<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\Docs\HtmlTag\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString\HtmlTag;

final class WithIdTest extends TestCase
{
    public function testHtmlTagWithIdBasic(): void
    {
        $tag = HtmlTag::new('nav')->withId('primary-nav');
        self::assertSame('<nav id="primary-nav">', (string) $tag);
    }

}
