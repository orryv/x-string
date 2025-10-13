<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class IsValidEncodingTest extends TestCase
{
    public function testIsValidEncodingUtf8(): void
    {
        $value = XString::new('Café');
        self::assertTrue($value->isValidEncoding('UTF-8'));
    }

    public function testIsValidEncodingAscii(): void
    {
        $value = XString::new('naïve');
        self::assertFalse($value->isValidEncoding('ASCII'));
    }

    public function testIsValidEncodingDefault(): void
    {
        $value = XString::new('emoji 😊');
        self::assertTrue($value->isValidEncoding());
    }

    public function testIsValidEncodingEmpty(): void
    {
        $value = XString::new('data');
        $this->expectException(InvalidArgumentException::class);
        $value->isValidEncoding('   ');
    }

}
