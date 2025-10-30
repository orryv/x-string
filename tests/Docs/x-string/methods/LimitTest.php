<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class LimitTest extends TestCase
{
    public function testLimitDefaultSuffix(): void
    {
        $result = XString::new('Hello World')->limit(5);
        self::assertSame('Hello...', (string) $result);
    }

    public function testLimitWithoutTruncation(): void
    {
        $result = XString::new('Hi')->limit(5);
        self::assertSame('Hi', (string) $result);
    }

    public function testLimitGraphemeAware(): void
    {
        $result = XString::new('👩‍💻 coding')->limit(2, '…');
        self::assertSame('👩‍💻 …', (string) $result);
    }

    public function testLimitZeroLength(): void
    {
        $result = XString::new('Content')->limit(0, '[more]');
        self::assertSame('[more]', (string) $result);
    }

}
