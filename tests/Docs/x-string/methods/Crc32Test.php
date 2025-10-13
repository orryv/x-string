<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class Crc32Test extends TestCase
{
    public function testCrc32Hex(): void
    {
        $result = XString::new('password')->crc32();
        self::assertSame(hash('crc32b', 'password'), (string) $result);
    }

    public function testCrc32Raw(): void
    {
        $result = XString::new('password')->crc32(true);
        self::assertSame(4, strlen((string) $result));
        self::assertSame(hash('crc32b', 'password', true), (string) $result);
    }

    public function testCrc32Empty(): void
    {
        $result = XString::new('')->crc32();
        self::assertSame(hash('crc32b', ''), (string) $result);
    }

    public function testCrc32Immutable(): void
    {
        $value = XString::new('immutable');
        $value->crc32();
        self::assertSame('immutable', (string) $value);
    }

}
