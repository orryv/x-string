<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class ReplaceLastTest extends TestCase
{
    public function testReplaceLastBasic(): void
    {
        $xstring = XString::new('one two one two');
        $result = $xstring->replaceLast('one', '1');
        self::assertSame('one two 1 two', (string) $result);
        self::assertSame('one two one two', (string) $xstring);
    }

    public function testReplaceLastArray(): void
    {
        $xstring = XString::new('alpha beta gamma beta alpha');
        $result = $xstring->replaceLast(['alpha', 'beta'], 'X');
        self::assertSame('alpha beta gamma beta X', (string) $result);
    }

    public function testReplaceLastImmutability(): void
    {
        $xstring = XString::new('repeat repeat repeat');
        $updated = $xstring->replaceLast('repeat', 'done');
        self::assertSame('repeat repeat repeat', (string) $xstring);
        self::assertSame('repeat repeat done', (string) $updated);
    }

    public function testReplaceLastNoMatch(): void
    {
        $xstring = XString::new('hello world');
        $result = $xstring->replaceLast('absent', 'x');
        self::assertSame('hello world', (string) $result);
    }

    public function testReplaceLastEmptySearch(): void
    {
        $xstring = XString::new('example');
        $this->expectException(InvalidArgumentException::class);
        $xstring->replaceLast('', 'test');
    }

}
