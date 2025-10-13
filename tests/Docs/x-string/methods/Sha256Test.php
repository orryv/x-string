<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class Sha256Test extends TestCase
{
    public function testSha256Hex(): void
    {
        $result = XString::new('password')->sha256();
        self::assertSame('5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', (string) $result);
    }

    public function testSha256Raw(): void
    {
        $result = XString::new('password')->sha256(true);
        self::assertSame(32, strlen((string) $result));
        self::assertSame(hash('sha256', 'password', true), (string) $result);
    }

    public function testSha256Different(): void
    {
        $first = XString::new('alpha');
        $second = XString::new('beta');
        self::assertNotSame((string) $first->sha256(), (string) $second->sha256());
    }

    public function testSha256Immutable(): void
    {
        $value = XString::new('immutable');
        $value->sha256();
        self::assertSame('immutable', (string) $value);
    }

}
