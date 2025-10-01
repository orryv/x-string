<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class CenterTest extends TestCase
{
    public function testCenterBasic(): void
    {
        $xstring = XString::new('cat');
        $result = $xstring->center(8, '-');
        self::assertSame('--cat---', (string) $result);
    }

    public function testCenterMultiChar(): void
    {
        $xstring = XString::new('menu');
        $result = $xstring->center(12, '[]');
        self::assertSame('[][]menu[][]', (string) $result);
    }

    public function testCenterByteMode(): void
    {
        $xstring = XString::new('猫')->withMode('bytes');
        $result = $xstring->center(7, '.');
        self::assertSame('..猫..', (string) $result);
        self::assertSame(7, $result->length());
    }

    public function testCenterNoChange(): void
    {
        $xstring = XString::new('long text');
        $result = $xstring->center(4, '*');
        self::assertSame('long text', (string) $result);
    }

    public function testCenterImmutability(): void
    {
        $xstring = XString::new('core');
        $centered = $xstring->center(10, '~');
        self::assertSame('core', (string) $xstring);
        self::assertSame('~~~core~~~', (string) $centered);
    }

    public function testCenterInvalidPad(): void
    {
        $xstring = XString::new('oops');
        $this->expectException(InvalidArgumentException::class);
        $xstring->center(6, '');
    }

}
