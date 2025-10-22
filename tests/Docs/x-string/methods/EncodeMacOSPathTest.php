<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class EncodeMacOSPathTest extends TestCase
{
    public function testMacEncodePathColon(): void
    {
        $value = XString::new('Applications/Audio:Mix');
        $result = $value->encodeMacOSPath();
        self::assertSame('Applications/Audio%3AMix', (string) $result);
    }

    public function testMacEncodePathPercent(): void
    {
        $value = XString::new('Volumes/data%/raw');
        $result = $value->encodeMacOSPath();
        self::assertSame('Volumes/data%25/raw', (string) $result);
    }

    public function testMacEncodePathTrailing(): void
    {
        $value = XString::new('/Users/');
        $result = $value->encodeMacOSPath();
        self::assertSame('/Users/', (string) $result);
    }

    public function testMacEncodePathNull(): void
    {
        $value = XString::new("/tmp/" . "\0" . "cache");
        $result = $value->encodeMacOSPath();
        self::assertSame('/tmp/%00cache', (string) $result);
    }

    public function testMacEncodePathDoubleEncodeToggle(): void
    {
        $value = XString::new('Projects%202024/Design:Draft');
        $noDouble = $value->encodeMacOSPath();
        $double = $value->encodeMacOSPath(true);
        self::assertSame('Projects%202024/Design%3ADraft', (string) $noDouble);
        self::assertSame('Projects%252024/Design%253ADraft', (string) $double);
    }

}
