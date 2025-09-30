<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class UcfirstTest extends TestCase
{
    public function testUcfirstBasic(): void
    {
        $xstring = XString::new('hello world');
        $result = $xstring->ucfirst();
        self::assertSame('Hello world', (string) $result);
    }

    public function testUcfirstMultibyte(): void
    {
        $xstring = XString::new('éclair');
        $result = $xstring->ucfirst();
        self::assertSame('Éclair', (string) $result);
    }

    public function testUcfirstImmutable(): void
    {
        $xstring = XString::new('already capitalized');
        $result = $xstring->ucfirst();
        self::assertSame('already capitalized', (string) $xstring);
        self::assertSame('Already capitalized', (string) $result);
    }

    public function testUcfirstCodepointMode(): void
    {
        $xstring = XString::new('ßharp')->withMode('codepoints');
        $result = $xstring->ucfirst();
        self::assertSame('SSharp', (string) $result);
    }

    public function testUcfirstEmpty(): void
    {
        $xstring = XString::new('');
        $result = $xstring->ucfirst();
        self::assertSame('', (string) $result);
    }

}
