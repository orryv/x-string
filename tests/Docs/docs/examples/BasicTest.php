<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\Docs\Examples;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class BasicTest extends TestCase
{
    public function testBasic(): void
    {
        $str = new XString(" Hello, World! \n");
        self::assertTrue($str instanceof XString);
        self::assertEquals(" Hello, World! \n", (string)$str);
        $trimmed = $str->trim();
        echo $trimmed; // Outputs: "Hello, World!"
        self::assertEquals("Hello, World!", (string)$trimmed);
    }

}
