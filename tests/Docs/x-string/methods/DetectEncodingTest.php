<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class DetectEncodingTest extends TestCase
{
    public function testDetectEncodingUtf8(): void
    {
        $value = XString::new('Café');
        $result = $value->detectEncoding();
        self::assertSame('UTF-8', $result);
    }

    public function testDetectEncodingCustom(): void
    {
        $value = XString::new("Plain ASCII text");
        $result = $value->detectEncoding(['ISO-8859-1', 'ASCII']);
        self::assertSame('ISO-8859-1', $result);
    }

    public function testDetectEncodingFalse(): void
    {
        $value = XString::new("\x00\x81\xFF");
        $result = $value->detectEncoding(['ASCII']);
        self::assertFalse($result);
    }

    public function testDetectEncodingEmpty(): void
    {
        $value = XString::new('content');
        $this->expectException(InvalidArgumentException::class);
        $value->detectEncoding([]);
    }

    public function testDetectEncodingImmutability(): void
    {
        $value = XString::new('data');
        $value->detectEncoding();
        self::assertSame('data', (string) $value);
    }

}
