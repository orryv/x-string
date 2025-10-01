<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;

final class JoinTest extends TestCase
{
    public function testJoinBasic(): void
    {
        $result = XString::join(['foo', 'bar', 'baz'], '-');
        self::assertSame('foo-bar-baz', (string) $result);
    }

    public function testJoinAlias(): void
    {
        $fragments = ['left', 'right'];
        $implode = XString::implode($fragments, ' / ');
        $join = XString::join($fragments, ' / ');
        self::assertSame((string) $implode, (string) $join);
    }

    public function testJoinNewline(): void
    {
        $parts = [
            'Line 1',
            Newline::new(),
            'Line 2',
            HtmlTag::new('br', true),
            Newline::new("\r\n"),
            'Line 3',
        ];
        $result = XString::join($parts);
        self::assertSame("Line 1\nLine 2<br />\r\nLine 3", (string) $result);
    }

    public function testJoinEmpty(): void
    {
        $result = XString::join([]);
        self::assertSame('', (string) $result);
    }

    public function testJoinInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        XString::join(['ok', 123]);
    }

}
