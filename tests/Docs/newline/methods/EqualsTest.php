<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\Newline\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;
use Orryv\XString\Newline;

final class EqualsTest extends TestCase
{
    public function testNewlineEqualsReplace(): void
    {
        $list = "TODO\nShip product\nTODO\n";
        $result = XString::new($list)
            ->replace(Newline::new("\n")->equals('TODO'), '[done]');
        self::assertSame("[done]\nShip product\n[done]\n", (string) $result);
    }

    public function testNewlineEqualsBlank(): void
    {
        $message = XString::new("Header\n\nBody");
        self::assertTrue($message->contains(Newline::new("\n")->equals('')));
        self::assertFalse(XString::new('Single line')->contains(Newline::new("\n")->equals('')));
    }

    public function testNewlineEqualsEquals(): void
    {
        $value = XString::new("DONE\n");
        self::assertTrue($value->equals(Newline::new("\n")->equals('DONE')));
        self::assertFalse($value->equals(Newline::new("\n")->equals('FAIL')));
    }

    public function testNewlineEqualsImmutability(): void
    {
        $base = Newline::new("\n");
        $matcher = $base->equals('Ready');
        $constraint = $matcher->getLineConstraint();
        self::assertNull($base->getLineConstraint());
        self::assertSame(['type' => 'equals', 'needle' => 'Ready'], $constraint);
        self::assertSame("\n", (string) $base);
        self::assertSame("\n", (string) $matcher);
    }

    public function testNewlineEqualsCrossPlatform(): void
    {
        $content = "alpha\nbeta\n";
        $result = XString::new($content)
            ->replace(Newline::new("\r\n")->equals('beta'), 'ROW');
        self::assertSame("alpha\r\nROW\r\n", (string) $result);
    }

}
