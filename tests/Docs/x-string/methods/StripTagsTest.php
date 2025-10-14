<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\HtmlTag;

final class StripTagsTest extends TestCase
{
    public function testStripTagsBasic(): void
    {
        $value = XString::new('<div>Hello <strong>World</strong></div>');
        $result = $value->stripTags();
        self::assertSame('Hello World', (string) $result);
    }

    public function testStripTagsAllowString(): void
    {
        $value = XString::new('<p>Intro</p><span class="note">note</span>');
        $result = $value->stripTags('p');
        self::assertSame('<p>Intro</p>note', (string) $result);
    }

    public function testStripTagsAllowHtmltag(): void
    {
        $value = XString::new('Keep <em>emphasis</em> but remove <strong>strong</strong>');
        $result = $value->stripTags(HtmlTag::new('em'));
        self::assertSame('Keep <em>emphasis</em> but remove strong', (string) $result);
    }

    public function testStripTagsAllowArray(): void
    {
        $value = XString::new('<p><em>Rich</em> <strong>text</strong></p>');
        $result = $value->stripTags(['p', 'em']);
        self::assertSame('<p><em>Rich</em> text</p>', (string) $result);
    }

    public function testStripTagsAllowSelfClosing(): void
    {
        $value = XString::new('Line one<br/>Line two<hr/>Done');
        $result = $value->stripTags(['br']);
        self::assertSame('Line one<br/>Line twoDone', (string) $result);
    }

    public function testStripTagsNestedArray(): void
    {
        $value = XString::new('<p>content</p>');
        $this->expectException(InvalidArgumentException::class);
        $value->stripTags([['p']]);
    }

    public function testStripTagsImmutable(): void
    {
        $original = XString::new('<span>note</span>');
        $original->stripTags();
        self::assertSame('<span>note</span>', (string) $original);
    }

}
