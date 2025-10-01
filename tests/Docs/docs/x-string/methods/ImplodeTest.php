<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;

final class ImplodeTest extends TestCase
{
    public function testImplodeNoGlue(): void
    {
        $result = XString::implode(['foo', 'bar', 'baz']);
        self::assertSame('foobarbaz', (string) $result);
    }

    public function testImplodeWithGlue(): void
    {
        $result = XString::implode(['apples', 'bananas', 'cherries'], ', ');
        self::assertSame('apples, bananas, cherries', (string) $result);
    }

    public function testImplodeWithNewline(): void
    {
        $fragments = [
            'Line 1',
            Newline::new(),
            'Line 2',
            HtmlTag::new('br', true),
            Newline::new("\r\n"),
            'Line 3',
        ];
        $result = XString::implode($fragments);
        self::assertSame("Line 1\nLine 2<br />\r\nLine 3", (string) $result);
    }

    public function testImplodeEmpty(): void
    {
        $result = XString::implode([]);
        self::assertSame('', (string) $result);
    }

    public function testImplodeInvalidFragment(): void
    {
        $this->expectException(InvalidArgumentException::class);
        XString::implode(['ok', 123]);
    }

}
