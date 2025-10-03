<?php

declare(strict_types=1);

namespace Orryv\XString;

use InvalidArgumentException;
use Stringable;

final class Newline implements Stringable
{
    private string $value;
    /** @var array{prefix: string, trim: bool}|null */
    private ?array $startsWithConfig;

    private function __construct(string $value, ?array $starts_with_config = null)
    {
        $this->value = $value;
        $this->startsWithConfig = $starts_with_config;
    }

    public static function new(?string $newline = null): self
    {
        return new self($newline ?? PHP_EOL);
    }

    public function startsWith(string $prefix, bool $trim = false): self
    {
        $normalized_prefix = $trim ? ltrim($prefix, " \t") : $prefix;

        if ($normalized_prefix === '') {
            throw new InvalidArgumentException('Prefix for startsWith cannot be empty.');
        }

        return new self(
            $this->value,
            [
                'prefix' => $normalized_prefix,
                'trim' => $trim,
            ]
        );
    }

    /**
     * @return array{prefix: string, trim: bool}|null
     */
    public function getStartsWithConfig(): ?array
    {
        return $this->startsWithConfig;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
