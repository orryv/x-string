<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString\Newline;
use Orryv\XString\XString;

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
            Newline::new("\r\n"),
            'Line 3',
        ];
        $result = XString::implode($fragments);
        self::assertSame("Line 1\nLine 2\r\nLine 3", (string) $result);
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
