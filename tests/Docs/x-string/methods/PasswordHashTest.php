<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;
use ValueError;

final class PasswordHashTest extends TestCase
{
    public function testPasswordHashDefault(): void
    {
        $password = XString::new('secret');
        $hash = $password->passwordHash();
        self::assertTrue(password_verify('secret', (string) $hash));
        self::assertSame('bcrypt', password_get_info((string) $hash)['algoName']);
    }

    public function testPasswordHashCost(): void
    {
        $password = XString::new('letmein');
        $hash = $password->passwordHash(PASSWORD_BCRYPT, ['cost' => 11]);
        self::assertTrue(password_verify('letmein', (string) $hash));
        self::assertSame('bcrypt', password_get_info((string) $hash)['algoName']);
        self::assertSame(11, password_get_info((string) $hash)['options']['cost']);
    }

    public function testPasswordHashImmutability(): void
    {
        $password = XString::new('unchanged');
        $password->passwordHash();
        self::assertSame('unchanged', (string) $password);
    }

    public function testPasswordHashInvalid(): void
    {
        $password = XString::new('secret');
        $this->expectException(ValueError::class);
        $password->passwordHash(PASSWORD_BCRYPT, ['cost' => 2]);
    }

}
