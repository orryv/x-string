<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class DecodeSafeFolderNameTest extends TestCase
{
    public function testSafeDecodeFolderSlash(): void
    {
        $value = XString::new('config%2Fnginx');
        $result = $value->decodeSafeFolderName();
        self::assertSame('config/nginx', (string) $result);
    }

    public function testSafeDecodeFolderColon(): void
    {
        $value = XString::new('cache%3Atmp');
        $result = $value->decodeSafeFolderName();
        self::assertSame('cache:tmp', (string) $result);
    }

    public function testSafeDecodeFolderTrailing(): void
    {
        $value = XString::new('data%20%2E');
        $result = $value->decodeSafeFolderName();
        self::assertSame('data .', (string) $result);
    }

    public function testSafeDecodeFolderPercent(): void
    {
        $value = XString::new('cache%25data');
        $result = $value->decodeSafeFolderName();
        self::assertSame('cache%data', (string) $result);
    }

}
