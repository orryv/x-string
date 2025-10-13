<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use RuntimeException;

final class ToAsciiTest extends TestCase
{
    public function testToAsciiBasic(): void
    {
        $value = XString::new('Český Krumlov');
        $result = $value->toAscii();
        self::assertSame('Cesky Krumlov', (string) $result);
    }

    public function testToAsciiExplicit(): void
    {
        $source = iconv('UTF-8', 'ISO-8859-1', 'Málaga, España');
        $value = XString::new($source);
        $result = $value->toAscii('ISO-8859-1');
        self::assertSame('Malaga, Espana', (string) $result);
    }

    public function testToAsciiPlaceholder(): void
    {
        $value = XString::new('ÆØÅ på ferie');
        $result = $value->toAscii();
        self::assertSame('AEOA pa ferie', (string) $result);
    }

    public function testToAsciiEmpty(): void
    {
        $value = XString::new('text');
        $this->expectException(InvalidArgumentException::class);
        $value->toAscii('');
    }

    public function testToAsciiRuntime(): void
    {
        $value = XString::new('example');
        $this->expectException(RuntimeException::class);
        $value->toAscii('UNKNOWN');
    }

    public function testToAsciiImmutability(): void
    {
        $value = XString::new('À la carte');
        $value->toAscii();
        self::assertSame('À la carte', (string) $value);
    }

}
