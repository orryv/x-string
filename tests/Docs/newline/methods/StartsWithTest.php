<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\Newline\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\Newline;

final class StartsWithTest extends TestCase
{
    public function testNewlineStartswithReplace(): void
    {
        $log = <<<LOG
        ERROR: Disk full
        INFO: Recovery complete
        ERROR: Backup failed
        LOG;
        $result = XString::new($log)
            ->replace(Newline::new("\n")->startsWith('ERROR:'), '[redacted]');
        self::assertSame("[redacted]\nINFO: Recovery complete\n[redacted]", (string) $result);
        self::assertSame($log, XString::new($log)->__toString());
    }

    public function testNewlineStartswithTrim(): void
    {
        $list = "  - apple\n\t- banana\nsummary";
        $result = XString::new($list)
            ->replace(Newline::new("\n")->startsWith('-', trim: true), '<item>');
        self::assertSame("<item>\n<item>\nsummary", (string) $result);
    }

    public function testNewlineStartswithNoTrim(): void
    {
        $tasks = "Task: root\n  Task: child\nTask: tail";
        $result = XString::new($tasks)
            ->replace(Newline::new("\n")->startsWith('Task:'), '[done]');
        self::assertSame("[done]\n  Task: child\n[done]", (string) $result);
    }

    public function testNewlineStartswithImmutability(): void
    {
        $base = Newline::new("\n");
        $matcher = $base->startsWith('Item:');
        $constraint = $matcher->getLineConstraint();
        self::assertNull($base->getLineConstraint());
        self::assertSame(['type' => 'starts_with', 'needle' => 'Item:', 'trim' => false], $constraint);
        self::assertSame("\n", (string) $base);
        self::assertSame("\n", (string) $matcher);
    }

    public function testNewlineStartswithEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Newline::new("\n")->startsWith("   \t  ", trim: true);
    }

    public function testNewlineStartswithCrossPlatform(): void
    {
        $notes = "alpha\nbeta\n";
        $result = XString::new($notes)
            ->replace(Newline::new("\r\n")->startsWith('beta'), 'ROW');
        self::assertSame("alpha\r\nROW\r\n", (string) $result);
    }

}
