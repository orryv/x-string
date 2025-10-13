<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class Sha1Test extends TestCase
{
    public function testSha1Hex(): void
    {
        $result = XString::new('password')->sha1();
        self::assertSame('5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', (string) $result);
    }

    public function testSha1Raw(): void
    {
        $result = XString::new('password')->sha1(true);
        self::assertSame(20, strlen((string) $result));
        self::assertSame(sha1('password', true), (string) $result);
    }

    public function testSha1CaseSensitive(): void
    {
        $upper = XString::new('Case');
        $lower = XString::new('case');
        self::assertSame('9254c4bba00f5ff69304a7921d3118fcbac7e6b8', (string) $upper->sha1());
        self::assertSame('6406510c31e0c9925733c7f21414bf6428333ed2', (string) $lower->sha1());
    }

    public function testSha1Immutable(): void
    {
        $value = XString::new('immutable');
        $value->sha1();
        self::assertSame('immutable', (string) $value);
    }

}
