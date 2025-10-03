<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class BeforeTest extends TestCase
{
    public function testBeforeEmail(): void
    {
        $email = XString::new('user@example.com');
        $result = $email->before('@');
        self::assertSame('user', (string) $result);
        self::assertSame('user@example.com', (string) $email);
    }

    public function testBeforeSkip(): void
    {
        $path = XString::new('one/two/three/four');
        $result = $path->before('/', skip: 2);
        self::assertSame('one/two/three', (string) $result);
    }

    public function testBeforeReversed(): void
    {
        $path = XString::new('path/to/file.txt');
        $result = $path->before('/', last_occurence: true);
        self::assertSame('path/to', (string) $result);
    }

    public function testBeforeMissing(): void
    {
        $text = XString::new('no delimiter');
        $result = $text->before('|');
        self::assertSame('no delimiter', (string) $result);
    }

    public function testBeforeImmutability(): void
    {
        $value = XString::new('abc-def');
        $before = $value->before('-');
        self::assertSame('abc-def', (string) $value);
        self::assertSame('abc', (string) $before);
    }

    public function testBeforeInvalidSkip(): void
    {
        $value = XString::new('example');
        $this->expectException(InvalidArgumentException::class);
        $value->before('e', skip: -1);
    }

}
