<?php

declare(strict_types=1);

namespace Orryv\XString;

use Stringable;

final class Newline implements Stringable
{
    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function new(?string $newline = null): self
    {
        return new self($newline ?? "\n");
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
