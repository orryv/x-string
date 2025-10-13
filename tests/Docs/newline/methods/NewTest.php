<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\Newline\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;
use Orryv\XString\Newline;

final class NewTest extends TestCase
{
    public function testNewlineNewDefault(): void
    {
        $newline = Newline::new();
        self::assertSame(PHP_EOL, (string) $newline);
    }

    public function testNewlineNewUnix(): void
    {
        $newline = Newline::new("\n");
        self::assertSame("\n", (string) $newline);
    }

    public function testNewlineNewWindows(): void
    {
        $newline = Newline::new("\r\n");
        self::assertSame("\r\n", (string) $newline);
    }

    public function testNewlineNewXstring(): void
    {
        $result = XString::new(['Header', Newline::new("\r\n"), 'Body']);
        self::assertSame("Header\r\nBody", (string) $result);
    }

    public function testNewlineNewEmpty(): void
    {
        $newline = Newline::new('');
        self::assertSame('', (string) $newline);
    }

}
