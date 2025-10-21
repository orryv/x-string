<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ToSafeFileNameTest extends TestCase
{
    public function testSafeFilenameReserved(): void
    {
        $value = XString::new('CON?.txt');
        $result = $value->toSafeFileName();
        self::assertSame('CON_.txt', (string) $result);
    }

    public function testSafeFilenameUnicode(): void
    {
        $value = XString::new('Récap/2024.txt');
        $result = $value->toSafeFileName();
        self::assertSame('Récap_2024.txt', (string) $result);
    }

    public function testSafeFilenameSpecial(): void
    {
        $value = XString::new('..');
        $result = $value->toSafeFileName();
        self::assertSame('_', (string) $result);
    }

    public function testSafeFilenameWhitespace(): void
    {
        $value = XString::new("   ");
        $result = $value->toSafeFileName();
        self::assertSame('_', (string) $result);
    }

}
