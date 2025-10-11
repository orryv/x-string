<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class LcfirstTest extends TestCase
{
    public function testLcfirstBasic(): void
    {
        $xstring = XString::new('Hello World');
        $result = $xstring->lcfirst();
        self::assertSame('hello World', (string) $result);
    }

    public function testLcfirstMultibyte(): void
    {
        $xstring = XString::new('Éclair');
        $result = $xstring->lcfirst();
        self::assertSame('éclair', (string) $result);
    }

    public function testLcfirstImmutable(): void
    {
        $xstring = XString::new('Already lower');
        $result = $xstring->lcfirst();
        self::assertSame('Already lower', (string) $xstring);
        self::assertSame('already lower', (string) $result);
    }

    public function testLcfirstGraphemeMode(): void
    {
        $xstring = XString::new('Ωmega')->withMode('graphemes');
        $result = $xstring->lcfirst();
        self::assertSame('ωmega', (string) $result);
    }

    public function testLcfirstEmpty(): void
    {
        $xstring = XString::new('');
        $result = $xstring->lcfirst();
        self::assertSame('', (string) $result);
    }

}
