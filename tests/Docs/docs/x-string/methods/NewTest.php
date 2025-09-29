<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs;

use PHPUnit\Framework\TestCase;
use Orryv\XString\XString;

final class NewTest extends TestCase
{
    public function testXstringNewPlain(): void
    {
        $xstring = XString::new('Hello world');
        self::assertInstanceOf(XString::class, $xstring);
        self::assertSame('Hello world', (string) $xstring);
    }

    public function testXstringNewArray(): void
    {
        $parts = ['Hello', ' ', 'world', '!'];
        $result = XString::new($parts);
        self::assertSame('Hello world!', (string) $result);
        self::assertSame(['Hello', ' ', 'world', '!'], $parts);
    }

    public function testXstringNewEmpty(): void
    {
        $xstring = XString::new();
        self::assertSame('', (string) $xstring);
    }

    public function testXstringNewInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        XString::new(['valid', 123]);
    }

}
