<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use RuntimeException;

final class FromFileTest extends TestCase
{
    public function testFromFileBasic(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'xstring_');
        file_put_contents($file, "Hello file!\nSecond line");
        $xstring = XString::fromFile($file);
        self::assertSame("Hello file!\nSecond line", (string) $xstring);
        self::assertSame(strlen("Hello file!\nSecond line"), $xstring->length());
        @unlink($file);
    }

    public function testFromFileSlice(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'xstring_');
        file_put_contents($file, 'abcdefghij');
        $xstring = XString::fromFile($file, length: 4, offset: 3);
        self::assertSame('defg', (string) $xstring);
        self::assertSame(4, $xstring->length());
        @unlink($file);
    }

    public function testFromFileMode(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'xstring_');
        $bytes = mb_convert_encoding('äëïöü', 'ISO-8859-1', 'UTF-8');
        file_put_contents($file, $bytes);
        $xstring = XString::fromFile($file, encoding: 'ISO-8859-1')
            ->withMode('codepoints', 'ISO-8859-1');
        $utf8 = mb_convert_encoding((string) $xstring, 'UTF-8', 'ISO-8859-1');
        self::assertSame('äëïöü', $utf8);
        self::assertSame(5, $xstring->length());
        @unlink($file);
    }

    public function testFromFileMissing(): void
    {
        $path = sys_get_temp_dir() . '/missing-' . uniqid('xstring_', true);
        $this->expectException(RuntimeException::class);
        XString::fromFile($path);
    }

    public function testFromFileInvalid(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'xstring_');
        file_put_contents($file, 'content');
        try {
        $this->expectException(InvalidArgumentException::class);
            XString::fromFile($file, length: -1);
        } finally {
            @unlink($file);
        }
    }

}
