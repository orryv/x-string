<?php

declare(strict_types=1);

namespace \Tests\Docs\\examples\;

use PHPUnit\Framework\TestCase;
use Orryv\XString;
use Orryv\XString\Newline;

final class NewlinesTest extends TestCase
{
    public function test0(): void
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
