<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class DecodeMacOSPathTest extends TestCase
{
    public function testMacDecodePathColon(): void
    {
        $value = XString::new('Applications/Audio%3AMix');
        $result = $value->decodeMacOSPath();
        self::assertSame('Applications/Audio:Mix', (string) $result);
    }

    public function testMacDecodePathPercent(): void
    {
        $value = XString::new('Volumes/data%25/raw');
        $result = $value->decodeMacOSPath();
        self::assertSame('Volumes/data%/raw', (string) $result);
    }

    public function testMacDecodePathTrailing(): void
    {
        $value = XString::new('/Users/');
        $result = $value->decodeMacOSPath();
        self::assertSame('/Users/', (string) $result);
    }

    public function testMacDecodePathNull(): void
    {
        $value = XString::new('/tmp/%00cache');
        $result = $value->decodeMacOSPath();
        self::assertSame("/tmp/" . "\0" . "cache", (string) $result);
    }

}
