<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class PadTest extends TestCase
{
    public function testPadLeftDefault(): void
    {
        $xstring = XString::new('42');
        $result = $xstring->pad(5, '0');
        self::assertSame('00042', (string) $result);
    }

    public function testPadRight(): void
    {
        $xstring = XString::new('data');
        $result = $xstring->pad(7, '.', left: false, right: true);
        self::assertSame('data...', (string) $result);
    }

    public function testPadBoth(): void
    {
        $xstring = XString::new('cat');
        $result = $xstring->pad(8, '_', left: true, right: true);
        self::assertSame('__cat___', (string) $result);
    }

    public function testPadGrapheme(): void
    {
        $result = XString::new('🙂')->pad(3, '⭐');
        self::assertSame('⭐⭐🙂', (string) $result);
    }

    public function testPadByteMode(): void
    {
        $xstring = XString::new('猫')->withMode('bytes');
        $result = $xstring->pad(5, '?');
        self::assertSame('??猫', (string) $result);
        self::assertSame(5, $result->length());
    }

    public function testPadImmutability(): void
    {
        $xstring = XString::new('core');
        $padded = $xstring->pad(6, '*', left: false, right: true);
        self::assertSame('core', (string) $xstring);
        self::assertSame('core**', (string) $padded);
    }

    public function testPadNoChange(): void
    {
        $xstring = XString::new('sample');
        $result = $xstring->pad(3, '0');
        self::assertSame('sample', (string) $result);
    }

    public function testPadEmptyPadString(): void
    {
        $xstring = XString::new('fail');
        $this->expectException(InvalidArgumentException::class);
        $xstring->pad(10, '');
    }

    public function testPadNoSides(): void
    {
        $xstring = XString::new('fail');
        $this->expectException(InvalidArgumentException::class);
        $xstring->pad(10, '.', left: false, right: false);
    }

    public function testPadNegativeLength(): void
    {
        $xstring = XString::new('fail');
        $this->expectException(InvalidArgumentException::class);
        $xstring->pad(-1, '.');
    }

}
