<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\Newline;

final class InsertAtIntervalTest extends TestCase
{
    public function testInsertIntervalBasic(): void
    {
        $value = XString::new('123456789');
        $result = $value->insertAtInterval('-', 3);
        self::assertSame('123-456-789', (string) $result);
    }

    public function testInsertIntervalUneven(): void
    {
        $value = XString::new('abcdefg');
        $result = $value->insertAtInterval('.', 2);
        self::assertSame('ab.cd.ef.g', (string) $result);
    }

    public function testInsertIntervalGrapheme(): void
    {
        $value = XString::new('🍎🍐🍊🍋🍌');
        $result = $value->insertAtInterval('|', 2);
        self::assertSame('🍎🍐|🍊🍋|🍌', (string) $result);
    }

    public function testInsertIntervalBytes(): void
    {
        $value = XString::new("a\u{0301}b")->withMode('bytes');
        $result = $value->insertAtInterval('.', 1);
        self::assertSame('612ecc2e812e62', bin2hex((string) $result));
    }

    public function testInsertIntervalFragment(): void
    {
        $value = XString::new('line1line2line3');
        $result = $value->insertAtInterval(Newline::new(), 5);
        self::assertSame('line1' . PHP_EOL . 'line2' . PHP_EOL . 'line3', (string) $result);
    }

    public function testInsertIntervalEmpty(): void
    {
        $value = XString::new('');
        $result = $value->insertAtInterval('-', 3);
        self::assertSame('', (string) $result);
    }

    public function testInsertIntervalInvalid(): void
    {
        $value = XString::new('error');
        $this->expectException(InvalidArgumentException::class);
        $value->insertAtInterval('*', 0);
    }

    public function testInsertIntervalImmutable(): void
    {
        $value = XString::new('ABCD');
        $result = $value->insertAtInterval(':', 2);
        self::assertSame('ABCD', (string) $value);
        self::assertSame('AB:CD', (string) $result);
    }

}
