<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ToLowerTest extends TestCase
{
    public function testToLowerBasic(): void
    {
        $xstring = XString::new('HELLO WORLD');
        $lower = $xstring->toLower();
        self::assertSame('hello world', (string) $lower);
    }

    public function testToLowerMultibyte(): void
    {
        $xstring = XString::new('ŻÓŁĆ');
        $lower = $xstring->toLower();
        self::assertSame('żółć', (string) $lower);
    }

    public function testToLowerAlias(): void
    {
        $xstring = XString::new('Alias');
        self::assertSame((string) $xstring->toLower(), (string) $xstring->toLowerCase());
    }

    public function testToLowerImmutability(): void
    {
        $xstring = XString::new('KEEP ME');
        $lower = $xstring->toLower();
        self::assertSame('KEEP ME', (string) $xstring);
        self::assertSame('keep me', (string) $lower);
    }

    public function testToLowerByteMode(): void
    {
        $xstring = XString::new('MIXED')->withMode('bytes');
        $result = $xstring->toLower();
        self::assertSame('mixed', (string) $result);
    }

    public function testToLowerEmpty(): void
    {
        $xstring = XString::new('');
        $result = $xstring->toLower();
        self::assertSame('', (string) $result);
    }

}
