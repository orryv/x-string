<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class RpadTest extends TestCase
{
    public function testRpadBasic(): void
    {
        $result = XString::new('data')->rpad(8, '.');
        self::assertSame('data....', (string) $result);
    }

    public function testRpadMultiFragment(): void
    {
        $result = XString::new('topic')->rpad(12, '->');
        self::assertSame('topic->->->-', (string) $result);
    }

    public function testRpadByteMode(): void
    {
        $xstring = XString::new('á')->withMode('bytes');
        $result = $xstring->rpad(4, '*');
        self::assertSame('á**', (string) $result);
    }

    public function testRpadImmutability(): void
    {
        $xstring = XString::new('cat');
        $padded = $xstring->rpad(6, '.');
        self::assertSame('cat', (string) $xstring);
        self::assertSame('cat...', (string) $padded);
    }

    public function testRpadEmptyFragment(): void
    {
        $xstring = XString::new('oops');
        $this->expectException(InvalidArgumentException::class);
        $xstring->rpad(5, '');
    }

    public function testRpadNegativeLength(): void
    {
        $xstring = XString::new('oops');
        $this->expectException(InvalidArgumentException::class);
        $xstring->rpad(-1, '.');
    }

}
