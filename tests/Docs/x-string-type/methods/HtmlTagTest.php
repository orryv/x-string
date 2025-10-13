<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XStringType\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XStringType;

final class HtmlTagTest extends TestCase
{
    public function testXstringTypeHtmltagAttributes(): void
    {
        $tag = XStringType::htmlTag('section')
            ->withClass('hero')
            ->withAttribute('data-theme', 'dark');
        self::assertSame('<section class="hero" data-theme="dark">', (string) $tag);
    }

    public function testXstringTypeHtmltagSelfClosing(): void
    {
        $meta = XStringType::htmlTag('meta', self_closing: true)
            ->withAttribute('charset', 'utf-8');
        self::assertSame('<meta charset="utf-8" />', (string) $meta);
    }

    public function testXstringTypeHtmltagImmutable(): void
    {
        $original = XStringType::htmlTag('div');
        $modified = $original->withClass('card');
        self::assertSame('<div>', (string) $original);
        self::assertSame('<div class="card">', (string) $modified);
    }

    public function testXstringTypeHtmltagCompose(): void
    {
        $markup = XString::new([
            XStringType::htmlTag('h1')->withBody('Docs Ready')->withEndTag(false),
            XStringType::htmlTag('p')->withBody('Generated from examples.')->withEndTag(false),
        ]);
        self::assertSame('<h1>Docs Ready</h1><p>Generated from examples.</p>', (string) $markup);
    }

    public function testXstringTypeHtmltagInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        XStringType::htmlTag('1-invalid');
    }

}
