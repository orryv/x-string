<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class LpadTest extends TestCase
{
    public function testLpadBasic(): void
    {
        $result = XString::new('42')->lpad(5, '0');
        self::assertSame('00042', (string) $result);
    }

    public function testLpadMultiFragment(): void
    {
        $result = XString::new('file')->lpad(10, '-=');
        self::assertSame('-=-=-=file', (string) $result);
    }

    public function testLpadGrapheme(): void
    {
        $result = XString::new('🙂')->lpad(4, '⭐');
        self::assertSame('⭐⭐⭐🙂', (string) $result);
    }

    public function testLpadImmutability(): void
    {
        $xstring = XString::new('cat');
        $padded = $xstring->lpad(6, '.');
        self::assertSame('cat', (string) $xstring);
        self::assertSame('...cat', (string) $padded);
    }

    public function testLpadEmptyFragment(): void
    {
        $xstring = XString::new('oops');
        $this->expectException(InvalidArgumentException::class);
        $xstring->lpad(5, '');
    }

    public function testLpadNegativeLength(): void
    {
        $xstring = XString::new('oops');
        $this->expectException(InvalidArgumentException::class);
        $xstring->lpad(-1, '.');
    }

}
