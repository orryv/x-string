<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use RuntimeException;

final class CryptTest extends TestCase
{
    public function testCryptDes(): void
    {
        $value = XString::new('password');
        $result = $value->crypt('aa');
        self::assertSame(crypt('password', 'aa'), (string) $result);
    }

    public function testCryptBlowfish(): void
    {
        $value = XString::new('correct horse battery staple');
        $result = $value->crypt('$2y$10$usesomesillystringforexampl$');
        self::assertSame(60, strlen((string) $result));
        self::assertSame((string) $result, crypt('correct horse battery staple', '$2y$10$usesomesillystringforexampl$'));
    }

    public function testCryptImmutability(): void
    {
        $value = XString::new('immutable');
        $value->crypt('aa');
        self::assertSame('immutable', (string) $value);
    }

    public function testCryptEmptySalt(): void
    {
        $value = XString::new('secret');
        $this->expectException(InvalidArgumentException::class);
        $value->crypt('');
    }

    public function testCryptInvalidSalt(): void
    {
        $value = XString::new('secret');
        $this->expectException(RuntimeException::class);
        $value->crypt('*');
    }

}
