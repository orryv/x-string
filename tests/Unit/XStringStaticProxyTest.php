<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Unit;

use BadMethodCallException;
use Orryv\XString;
use PHPUnit\Framework\TestCase;

final class XStringStaticProxyTest extends TestCase
{
    public function testCallStaticDelegatesToInstanceMethod(): void
    {
        $result = XString::repeat('#', 3);

        self::assertSame('###', (string) $result);
    }

    public function testCallStaticSupportsTransformers(): void
    {
        $result = XString::append('foo', 'bar');

        self::assertSame('foobar', (string) $result);
    }

    public function testCallStaticPreservesReturnValues(): void
    {
        $length = XString::length('hello world');

        self::assertSame(11, $length);
    }

    public function testCallStaticGuardsUnknownMethods(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Call to undefined method Orryv\\XString::doesNotExist()');

        XString::doesNotExist('anything');
    }
}
