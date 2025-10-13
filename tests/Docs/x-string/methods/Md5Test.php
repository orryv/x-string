<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class Md5Test extends TestCase
{
    public function testMd5Hex(): void
    {
        $value = XString::new('password');
        $result = $value->md5();
        self::assertSame('5f4dcc3b5aa765d61d8327deb882cf99', (string) $result);
    }

    public function testMd5Raw(): void
    {
        $value = XString::new('password');
        $result = $value->md5(true);
        self::assertSame(16, strlen((string) $result));
        self::assertSame(md5('password', true), (string) $result);
    }

    public function testMd5Different(): void
    {
        $upper = XString::new('Case');
        $lower = XString::new('case');
        self::assertSame('0819eb30cc2cd18cf6b02042458c5da1', (string) $upper->md5());
        self::assertSame('cd14c323902024e72c850aa828d634a7', (string) $lower->md5());
    }

    public function testMd5Immutability(): void
    {
        $value = XString::new('immutable');
        $value->md5();
        self::assertSame('immutable', (string) $value);
    }

}
