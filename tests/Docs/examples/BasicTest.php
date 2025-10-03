<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\Examples;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class BasicTest extends TestCase
{
    public function testBasic(): void
    {
        $str = XString::new(" Hello, World! \n");
        self::assertTrue($str instanceof XString);
        self::assertEquals(" Hello, World! \n", (string)$str);
        $trimmed = $str->trim();
        self::assertEquals("Hello, World!", (string)$trimmed);
    }

}
