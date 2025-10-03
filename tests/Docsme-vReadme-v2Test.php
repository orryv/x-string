<?php

declare(strict_types=1);

namespace \Tests\Docs\MeV;

use PHPUnit\Framework\TestCase;
use Orryv\XString;
use Orryv\XString\Newline;

final class Readme-v2Test extends TestCase
{
    public function test0(): void
    {
        $str = new XString(" Hello, World! \n");
        self::assertTrue($str instanceof XString);
        self::assertEquals(" Hello, World! \n", (string)$str);
        $trimmed = $str->trim();
        echo $trimmed; // Outputs: "Hello, World!"
        self::assertEquals("Hello, World!", (string)$trimmed);
        ```
        <!-- test:newlines -->
        ```php
        $str = <<<EOT
         Line1 - blabla
        Hello, World!
        EOT;
        $string = new XString($str);
        self::assertEquals($str, (string)$string);
        $string->after(Newline::new()->startsWith('Line1', trim:true));
        echo $string; // Outputs: "Hello, World!"
        self::assertEquals("Hello, World!", (string)$string);
    }

}
