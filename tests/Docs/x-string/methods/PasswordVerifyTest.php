<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class PasswordVerifyTest extends TestCase
{
    public function testPasswordVerifyMatch(): void
    {
        $password = XString::new('secret');
        $hash = $password->passwordHash();
        $result = XString::new('secret')->passwordVerify((string) $hash);
        self::assertTrue($result);
    }

    public function testPasswordVerifyMismatch(): void
    {
        $hash = XString::new('correct-horse')->passwordHash();
        $result = XString::new('Tr0ub4dor&3')->passwordVerify((string) $hash);
        self::assertFalse($result);
    }

    public function testPasswordVerifyInvalid(): void
    {
        $result = XString::new('secret')->passwordVerify('not-a-real-hash');
        self::assertFalse($result);
    }

    public function testPasswordVerifyImmutability(): void
    {
        $value = XString::new('immutable');
        $value->passwordVerify('irrelevant');
        self::assertSame('immutable', (string) $value);
    }

}
