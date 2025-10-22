<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class EncodeUnixFolderNameTest extends TestCase
{
    public function testUnixEncodeFolderSlash(): void
    {
        $value = XString::new('config/nginx');
        $result = $value->encodeUnixFolderName();
        self::assertSame('config%2Fnginx', (string) $result);
    }

    public function testUnixEncodeFolderPercent(): void
    {
        $value = XString::new('cache%data');
        $result = $value->encodeUnixFolderName();
        self::assertSame('cache%25data', (string) $result);
    }

    public function testUnixEncodeFolderNull(): void
    {
        $value = XString::new("\0tmp");
        $result = $value->encodeUnixFolderName();
        self::assertSame('%00tmp', (string) $result);
    }

    public function testUnixEncodeFolderUnicode(): void
    {
        $value = XString::new('データ');
        $result = $value->encodeUnixFolderName();
        self::assertSame('データ', (string) $result);
    }

    public function testUnixEncodeFolderNameDoubleEncodeToggle(): void
    {
        $value = XString::new('Backups%202024/reports');
        $noDouble = $value->encodeUnixFolderName();
        $double = $value->encodeUnixFolderName(true);
        self::assertSame('Backups%202024%2Freports', (string) $noDouble);
        self::assertSame('Backups%252024%2Freports', (string) $double);
    }

}
