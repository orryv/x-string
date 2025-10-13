<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use RuntimeException;

final class ToUtf8Test extends TestCase
{
    public function testToUtf8Latin1(): void
    {
        $latin1 = iconv('UTF-8', 'ISO-8859-1', 'Café déjà vu');
        $value = XString::new($latin1);
        $result = $value->toUtf8('ISO-8859-1');
        self::assertSame('Café déjà vu', (string) $result);
        self::assertNotSame($value, $result);
    }

    public function testToUtf8Detect(): void
    {
        $source = iconv('UTF-8', 'ISO-8859-1', 'Mañana será otro día');
        $value = XString::new($source);
        $result = $value->toUtf8();
        self::assertSame('Mañana será otro día', (string) $result);
    }

    public function testToUtf8Empty(): void
    {
        $value = XString::new('text');
        $this->expectException(InvalidArgumentException::class);
        $value->toUtf8('');
    }

    public function testToUtf8Runtime(): void
    {
        $value = XString::new('sample');
        $this->expectException(RuntimeException::class);
        $value->toUtf8('INVALID-ENCODING');
    }

    public function testToUtf8Immutability(): void
    {
        $value = XString::new('Übermäßig');
        $value->toUtf8('UTF-8');
        self::assertSame('Übermäßig', (string) $value);
    }

}
