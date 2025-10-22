<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class DecodeUnixFolderNameTest extends TestCase
{
    public function testUnixDecodeFolderSlash(): void
    {
        $value = XString::new('config%2Fnginx');
        $result = $value->decodeUnixFolderName();
        self::assertSame('config/nginx', (string) $result);
    }

    public function testUnixDecodeFolderPercent(): void
    {
        $value = XString::new('cache%25data');
        $result = $value->decodeUnixFolderName();
        self::assertSame('cache%data', (string) $result);
    }

    public function testUnixDecodeFolderNull(): void
    {
        $value = XString::new('%00tmp');
        $result = $value->decodeUnixFolderName();
        self::assertSame("\0tmp", (string) $result);
    }

    public function testUnixDecodeFolderUnicode(): void
    {
        $value = XString::new('データ');
        $result = $value->decodeUnixFolderName();
        self::assertSame('データ', (string) $result);
    }

}
