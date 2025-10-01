<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class AfterTest extends TestCase
{
    public function testAfterEmail(): void
    {
        $email = XString::new('user@example.com');
        $result = $email->after('@');
        self::assertSame('example.com', (string) $result);
        self::assertSame('user@example.com', (string) $email);
    }

    public function testAfterSkip(): void
    {
        $path = XString::new('one/two/three/four');
        $result = $path->after('/', skip: 1);
        self::assertSame('three/four', (string) $result);
    }

    public function testAfterReversed(): void
    {
        $path = XString::new('path/to/file.txt');
        $result = $path->after('/', last_occurence: true);
        self::assertSame('file.txt', (string) $result);
    }

    public function testAfterMissing(): void
    {
        $text = XString::new('no delimiter');
        $result = $text->after('|');
        self::assertSame('no delimiter', (string) $result);
    }

    public function testAfterImmutability(): void
    {
        $value = XString::new('abc-def');
        $after = $value->after('-');
        self::assertSame('abc-def', (string) $value);
        self::assertSame('def', (string) $after);
    }

    public function testAfterInvalidSkip(): void
    {
        $value = XString::new('example');
        $this->expectException(InvalidArgumentException::class);
        $value->after('e', skip: -1);
    }

}
