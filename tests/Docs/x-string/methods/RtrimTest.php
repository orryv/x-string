<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class RtrimTest extends TestCase
{
    public function testRtrimBasic(): void
    {
        $xstring = XString::new("  Hello World!\n\t");
        $result = $xstring->rtrim();
        self::assertSame('  Hello World!', (string) $result);
    }

    public function testRtrimDisableNewline(): void
    {
        $xstring = XString::new("Line ending\n");
        $result = $xstring->rtrim(newline: false);
        self::assertSame("Line ending\n", (string) $result);
    }

    public function testRtrimLeadingUntouched(): void
    {
        $xstring = XString::new("  keep head\t   ");
        $result = $xstring->rtrim();
        self::assertSame('  keep head', (string) $result);
    }

    public function testRtrimImmutability(): void
    {
        $xstring = XString::new("trim me   ");
        $trimmed = $xstring->rtrim();
        self::assertSame('trim me   ', (string) $xstring);
        self::assertSame('trim me', (string) $trimmed);
    }

    public function testRtrimEmpty(): void
    {
        $xstring = XString::new('');
        $result = $xstring->rtrim();
        self::assertSame('', (string) $result);
    }

}
