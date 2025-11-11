<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ToStringTest extends TestCase
{
    public function testToStringBasic(): void
    {
        $xstring = XString::new('Hello, world!');
        $value = $xstring->toString();
        self::assertSame('Hello, world!', $value);
    }

    public function testToStringCasting(): void
    {
        $xstring = XString::new('ßeta');
        self::assertSame((string) $xstring, $xstring->toString());
        self::assertSame($xstring->__toString(), $xstring->toString());
    }

    public function testToStringChained(): void
    {
        $result = XString::new('  padded ')
            ->trim()
            ->toUpper()
            ->toString();
        self::assertSame('PADDED', $result);
    }

}
