<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ReplaceFirstTest extends TestCase
{
    public function testReplaceFirstBasic(): void
    {
        $xstring = XString::new('foo bar foo bar');
        $result = $xstring->replaceFirst('foo', 'baz');
        self::assertSame('baz bar foo bar', (string) $result);
    }

    public function testReplaceFirstMultiple(): void
    {
        $xstring = XString::new('alpha beta gamma');
        $result = $xstring->replaceFirst(['delta', 'beta', 'gamma'], 'theta');
        self::assertSame('alpha theta gamma', (string) $result);
    }

    public function testReplaceFirstImmutability(): void
    {
        $xstring = XString::new('repeat me');
        $replaced = $xstring->replaceFirst('repeat', 'echo');
        self::assertSame('repeat me', (string) $xstring);
        self::assertSame('echo me', (string) $replaced);
    }

    public function testReplaceFirstNoMatch(): void
    {
        $xstring = XString::new('unchanged');
        $result = $xstring->replaceFirst('missing', 'found');
        self::assertSame('unchanged', (string) $result);
    }

}
