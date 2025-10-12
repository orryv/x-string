<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class TrimTest extends TestCase
{
    public function testTrimBasic(): void
    {
        $xstring = XString::new("\t  Hello World!\r\n");
        $result = $xstring->trim();
        self::assertSame('Hello World!', (string) $result);
    }

    public function testTrimDisableNewline(): void
    {
        $xstring = XString::new("Line with trailing newline\n");
        $result = $xstring->trim(newline: false);
        self::assertSame("Line with trailing newline\n", (string) $result);
    }

    public function testTrimDisabled(): void
    {
        $xstring = XString::new("  keep my spaces  ");
        $result = $xstring->trim(newline: false, space: false, tab: false);
        self::assertSame('  keep my spaces  ', (string) $result);
    }

    public function testTrimWhitespaceOnly(): void
    {
        $xstring = XString::new("\t\n  \r");
        $result = $xstring->trim();
        self::assertSame('', (string) $result);
    }

    public function testTrimImmutability(): void
    {
        $xstring = XString::new("  Mutable?  ");
        $trimmed = $xstring->trim();
        self::assertSame('  Mutable?  ', (string) $xstring);
        self::assertSame('Mutable?', (string) $trimmed);
    }

}
