<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs;

use PHPUnit\Framework\TestCase;

final class ReadmeTest extends TestCase
{
    public function testMainTes(): void
    {
        echo 'hello';
        self::assertEquals('hello', (string) new \XString\XString('hello'));
    }

}
