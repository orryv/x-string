<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XStringType\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XStringType;

final class HtmlCloseTagTest extends TestCase
{
    public function testXstringTypeHtmlclosetagBasic(): void
    {
        $closing = XStringType::htmlCloseTag('article');
        self::assertSame('</article>', (string) $closing);
    }

    public function testXstringTypeHtmlclosetagCase(): void
    {
        $closing = XStringType::htmlCloseTag('MyComponent', case_sensitive: true);
        self::assertSame('</MyComponent>', (string) $closing);
    }

    public function testXstringTypeHtmlclosetagSearch(): void
    {
        $fragment = XString::new('<section><p>Body</p></section>');
        self::assertTrue($fragment->contains(XStringType::htmlCloseTag('section')));
        self::assertFalse($fragment->contains(XStringType::htmlCloseTag('article')));
    }

    public function testXstringTypeHtmlclosetagInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        XStringType::htmlCloseTag('');
    }

}
