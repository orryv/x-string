<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class LtrimTest extends TestCase
{
    public function testLtrimBasic(): void
    {
        $xstring = XString::new("\n\t  Hello World!  ");
        $result = $xstring->ltrim();
        self::assertSame('Hello World!  ', (string) $result);
    }

    public function testLtrimDisableNewline(): void
    {
        $xstring = XString::new("\nTabbed line");
        $result = $xstring->ltrim(newline: false);
        self::assertSame("\nTabbed line", (string) $result);
    }

    public function testLtrimTrailingUntouched(): void
    {
        $xstring = XString::new("  keep tail   \t");
        $result = $xstring->ltrim();
        self::assertSame("keep tail   \t", (string) $result);
    }

    public function testLtrimImmutability(): void
    {
        $xstring = XString::new("   example   ");
        $trimmed = $xstring->ltrim();
        self::assertSame('   example   ', (string) $xstring);
        self::assertSame('example   ', (string) $trimmed);
    }

    public function testLtrimDisabled(): void
    {
        $xstring = XString::new("\t preserve me");
        $result = $xstring->ltrim(newline: false, space: false, tab: false);
        self::assertSame("\t preserve me", (string) $result);
    }

}
