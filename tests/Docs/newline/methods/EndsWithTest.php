<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\Newline\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\Newline;

final class EndsWithTest extends TestCase
{
    public function testNewlineEndswithReplace(): void
    {
        $report = <<<TEXT
        Build: ok
        Deploy: failed
        Audit: failed
        TEXT;
        $result = XString::new($report)
            ->replace(Newline::new("\n")->endsWith('failed'), '[redacted]');
        self::assertSame("Build: ok\n[redacted]\n[redacted]", (string) $result);
    }

    public function testNewlineEndswithTrim(): void
    {
        $checklist = "pass  \nretry\t\nfail";
        $result = XString::new($checklist)
            ->replace(Newline::new("\n")->endsWith('retry', trim: true), '[again]');
        self::assertSame("pass  \n[again]\nfail", (string) $result);
    }

    public function testNewlineEndswithContains(): void
    {
        $log = XString::new("INFO ready\nWARN: Disk slow   \nOK\n");
        self::assertTrue($log->contains(Newline::new("\n")->endsWith('slow', trim: true)));
        self::assertFalse($log->contains(Newline::new("\n")->endsWith('offline', trim: true)));
    }

    public function testNewlineEndswithImmutability(): void
    {
        $base = Newline::new("\n");
        $matcher = $base->endsWith('!');
        $constraint = $matcher->getLineConstraint();
        self::assertNull($base->getLineConstraint());
        self::assertSame(['type' => 'ends_with', 'needle' => '!', 'trim' => false], $constraint);
        self::assertSame("\n", (string) $base);
        self::assertSame("\n", (string) $matcher);
    }

    public function testNewlineEndswithEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Newline::new("\n")->endsWith("  \t  ", trim: true);
    }

    public function testNewlineEndswithCrossPlatform(): void
    {
        $notes = "alpha\nbeta\n";
        $result = XString::new($notes)
            ->replace(Newline::new("\r\n")->endsWith('beta'), 'ROW');
        self::assertSame("alpha\r\nROW\r\n", (string) $result);
    }

}
