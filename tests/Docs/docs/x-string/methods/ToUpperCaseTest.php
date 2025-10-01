<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ToUpperCaseTest extends TestCase
{
    public function testToUpperCaseBasic(): void
    {
        $xstring = XString::new('alias');
        $result = $xstring->toUpperCase();
        self::assertSame('ALIAS', (string) $result);
    }

    public function testToUpperCaseMultibyte(): void
    {
        $xstring = XString::new('żółć');
        $result = $xstring->toUpperCase();
        self::assertSame('ŻÓŁĆ', (string) $result);
    }

    public function testToUpperCaseImmutable(): void
    {
        $xstring = XString::new('stay lowercase');
        $upper = $xstring->toUpperCase();
        self::assertSame('stay lowercase', (string) $xstring);
        self::assertSame('STAY LOWERCASE', (string) $upper);
    }

    public function testToUpperCaseByteMode(): void
    {
        $xstring = XString::new('mode')->withMode('bytes');
        $result = $xstring->toUpperCase();
        self::assertSame('MODE', (string) $result);
    }

}
