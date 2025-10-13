<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\Newline\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\Newline;

final class ContainsTest extends TestCase
{
    public function testNewlineContainsRedact(): void
    {
        $notes = <<<TEXT
        user: alice
        password: hunter2
        remember: never share passwords
        TEXT;
        $result = XString::new($notes)
            ->replace(Newline::new("\n")->contains('password'), '[redacted]');
        self::assertSame("user: alice\n[redacted]\n[redacted]", (string) $result);
    }

    public function testNewlineContainsDetect(): void
    {
        $log = XString::new("INFO ready\nWARN: disk slow\nERROR: backup failed\n");
        self::assertTrue($log->contains(Newline::new("\n")->contains('ERROR: ')));
        self::assertFalse($log->contains(Newline::new("\n")->contains('CRITICAL: ')));
    }

    public function testNewlineContainsStartswith(): void
    {
        $script = <<<SH
        echo "Start"
        echo "Process complete"
        SH;
        $result = XString::new($script)
            ->contains(Newline::new("\n")->contains('Start'))
            && XString::new($script)
                ->startsWith(Newline::new("\n")->contains('Start'));
        self::assertTrue($result);
        self::assertFalse(XString::new($script)->startsWith(Newline::new("\n")->contains('Process')));
    }

    public function testNewlineContainsImmutability(): void
    {
        $base = Newline::new("\n");
        $matcher = $base->contains('TODO');
        $constraint = $matcher->getLineConstraint();
        self::assertNull($base->getLineConstraint());
        self::assertSame(['type' => 'contains', 'needle' => 'TODO'], $constraint);
        self::assertSame("\n", (string) $base);
        self::assertSame("\n", (string) $matcher);
    }

    public function testNewlineContainsEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Newline::new("\n")->contains('');
    }

    public function testNewlineContainsCrossPlatform(): void
    {
        $body = "alpha\nbeta\ngamma";
        self::assertTrue(XString::new($body)->contains(Newline::new("\r\n")->contains('beta')));
        self::assertFalse(XString::new($body)->contains(Newline::new("\r\n")->contains('delta')));
    }

}
