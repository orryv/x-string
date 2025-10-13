<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class UrlEncodeTest extends TestCase
{
    public function testUrlEncodeBasic(): void
    {
        $result = XString::new('hello world')->urlEncode();
        self::assertSame('hello+world', (string) $result);
    }

    public function testUrlEncodeRaw(): void
    {
        $result = XString::new('a+b c')->urlEncode(true);
        self::assertSame('a%2Bb%20c', (string) $result);
    }

    public function testUrlEncodeUtf8(): void
    {
        $result = XString::new('café')->urlEncode();
        self::assertSame('caf%C3%A9', (string) $result);
    }

    public function testUrlEncodeGraphemeMode(): void
    {
        $value = XString::new('alpha-123_.-')->withMode('graphemes');
        $result = $value->urlEncode();
        self::assertSame('alpha-123_.-', (string) $result);
    }

    public function testUrlEncodeEmpty(): void
    {
        $result = XString::new('')->urlEncode();
        self::assertSame('', (string) $result);
    }

    public function testUrlEncodeImmutable(): void
    {
        $value = XString::new('keep me safe');
        $value->urlEncode();
        self::assertSame('keep me safe', (string) $value);
    }

}
