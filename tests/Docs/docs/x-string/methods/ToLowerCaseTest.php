<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ToLowerCaseTest extends TestCase
{
    public function testToLowerCaseBasic(): void
    {
        $xstring = XString::new('ALIAS');
        $result = $xstring->toLowerCase();
        self::assertSame('alias', (string) $result);
    }

    public function testToLowerCaseMultibyte(): void
    {
        $xstring = XString::new('ĞİŞ');
        $result = $xstring->toLowerCase();
        self::assertSame('ğiş', (string) $result);
    }

    public function testToLowerCaseImmutable(): void
    {
        $xstring = XString::new('UNCHANGED');
        $lower = $xstring->toLowerCase();
        self::assertSame('UNCHANGED', (string) $xstring);
        self::assertSame('unchanged', (string) $lower);
    }

    public function testToLowerCaseCodepointMode(): void
    {
        $xstring = XString::new('MIXED')->withMode('codepoints');
        $result = $xstring->toLowerCase();
        self::assertSame('mixed', (string) $result);
    }

}
