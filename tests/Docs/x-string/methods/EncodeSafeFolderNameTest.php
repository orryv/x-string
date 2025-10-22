<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class EncodeSafeFolderNameTest extends TestCase
{
    public function testSafeEncodeFolderSlash(): void
    {
        $value = XString::new('config/nginx');
        $result = $value->encodeSafeFolderName();
        self::assertSame('config%2Fnginx', (string) $result);
    }

    public function testSafeEncodeFolderColon(): void
    {
        $value = XString::new('cache:tmp');
        $result = $value->encodeSafeFolderName();
        self::assertSame('cache%3Atmp', (string) $result);
    }

    public function testSafeEncodeFolderTrailing(): void
    {
        $value = XString::new('data .');
        $result = $value->encodeSafeFolderName();
        self::assertSame('data%20%2E', (string) $result);
    }

    public function testSafeEncodeFolderPercent(): void
    {
        $value = XString::new('cache%data');
        $result = $value->encodeSafeFolderName();
        self::assertSame('cache%25data', (string) $result);
    }

    public function testSafeEncodeFolderNameDoubleEncodeToggle(): void
    {
        $value = XString::new('Reports%202024?');
        $noDouble = $value->encodeSafeFolderName();
        $double = $value->encodeSafeFolderName(true);
        self::assertSame('Reports%202024%3F', (string) $noDouble);
        self::assertSame('Reports%252024%253F', (string) $double);
    }

}
