<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class DecodeMacOSFolderNameTest extends TestCase
{
    public function testMacDecodeFolderSlash(): void
    {
        $value = XString::new('config%2Fnginx');
        $result = $value->decodeMacOSFolderName();
        self::assertSame('config/nginx', (string) $result);
    }

    public function testMacDecodeFolderColon(): void
    {
        $value = XString::new('cache%3Atmp');
        $result = $value->decodeMacOSFolderName();
        self::assertSame('cache:tmp', (string) $result);
    }

    public function testMacDecodeFolderPercent(): void
    {
        $value = XString::new('cache%25data');
        $result = $value->decodeMacOSFolderName();
        self::assertSame('cache%data', (string) $result);
    }

    public function testMacDecodeFolderNull(): void
    {
        $value = XString::new('%00tmp');
        $result = $value->decodeMacOSFolderName();
        self::assertSame("\0tmp", (string) $result);
    }

}
