<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs;

use PHPUnit\Framework\TestCase;
use Orryv\XString;
use Orryv\XString\Newline;

final class ReadmeV2Test extends TestCase
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

    public function testNewlines(): void
    {
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
