<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class ReplaceTest extends TestCase
{
    public function testReplaceAll(): void
    {
        $xstring = XString::new('lorem ipsum lorem ipsum');
        $result = $xstring->replace('lorem', 'dolor');
        self::assertSame('dolor ipsum dolor ipsum', (string) $result);
    }

    public function testReplaceLimit(): void
    {
        $xstring = XString::new('aaa aaa aaa');
        $result = $xstring->replace('aaa', 'bbb', limit: 2);
        self::assertSame('bbb bbb aaa', (string) $result);
    }

    public function testReplaceReversed(): void
    {
        $xstring = XString::new('2024-05-01 2024-06-01 2024-07-01');
        $result = $xstring->replace('2024', '2025', limit: 2, reversed: true);
        self::assertSame('2024-05-01 2025-06-01 2025-07-01', (string) $result);
    }

    public function testReplaceMultipleSearch(): void
    {
        $xstring = XString::new('red green blue red');
        $result = $xstring->replace(['red', 'blue'], 'X');
        self::assertSame('X green X X', (string) $result);
    }

    public function testReplaceImmutability(): void
    {
        $xstring = XString::new('alpha beta gamma');
        $replaced = $xstring->replace('beta', 'delta');
        self::assertSame('alpha beta gamma', (string) $xstring);
        self::assertSame('alpha delta gamma', (string) $replaced);
    }

    public function testReplaceZeroLimit(): void
    {
        $xstring = XString::new('unchanged text');
        $result = $xstring->replace('text', 'content', limit: 0);
        self::assertSame('unchanged text', (string) $result);
    }

    public function testReplaceInvalidLimit(): void
    {
        $xstring = XString::new('example');
        $this->expectException(InvalidArgumentException::class);
        $xstring->replace('example', 'test', limit: -1);
    }

    public function testReplaceEmptySearch(): void
    {
        $xstring = XString::new('example');
        $this->expectException(InvalidArgumentException::class);
        $xstring->replace('', 'test');
    }

}
