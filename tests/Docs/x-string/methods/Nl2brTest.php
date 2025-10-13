<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class Nl2brTest extends TestCase
{
    public function testNl2brUnix(): void
    {
        $result = XString::new("Line 1\nLine 2")->nl2br();
        self::assertSame("Line 1<br />\nLine 2", (string) $result);
    }

    public function testNl2brHtml4(): void
    {
        $result = XString::new("One\nTwo")->nl2br(false);
        self::assertSame("One<br>\nTwo", (string) $result);
    }

    public function testNl2brWindows(): void
    {
        $result = XString::new("First\r\nSecond")->nl2br();
        self::assertSame("First<br />\r\nSecond", (string) $result);
    }

    public function testNl2brConsecutive(): void
    {
        $result = XString::new("Top\n\nBottom")->nl2br();
        self::assertSame("Top<br />\n<br />\nBottom", (string) $result);
    }

    public function testNl2brBytesMode(): void
    {
        $value = XString::new("Σ\nσ")->withMode('bytes');
        $result = $value->nl2br();
        self::assertSame("Σ<br />\nσ", (string) $result);
    }

    public function testNl2brEmpty(): void
    {
        $result = XString::new('')->nl2br();
        self::assertSame('', (string) $result);
    }

    public function testNl2brImmutable(): void
    {
        $value = XString::new("Keep\nOriginal");
        $value->nl2br();
        self::assertSame("Keep\nOriginal", (string) $value);
    }

}
