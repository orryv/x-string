<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class UrlDecodeTest extends TestCase
{
    public function testUrlDecodeBasic(): void
    {
        $result = XString::new('hello+world%21')->urlDecode();
        self::assertSame('hello world!', (string) $result);
    }

    public function testUrlDecodeRaw(): void
    {
        $value = XString::new('a+b%20c')->withMode('codepoints');
        $result = $value->urlDecode(true);
        self::assertSame('a+b c', (string) $result);
    }

    public function testUrlDecodeUtf8(): void
    {
        $result = XString::new('caf%C3%A9')->urlDecode();
        self::assertSame('café', (string) $result);
    }

    public function testUrlDecodeInvalid(): void
    {
        $result = XString::new('price%ZZtag')->urlDecode();
        self::assertSame('price%ZZtag', (string) $result);
    }

    public function testUrlDecodeEmpty(): void
    {
        $result = XString::new('')->urlDecode();
        self::assertSame('', (string) $result);
    }

    public function testUrlDecodeImmutable(): void
    {
        $value = XString::new('immutable%3F');
        $value->urlDecode();
        self::assertSame('immutable%3F', (string) $value);
    }

}
