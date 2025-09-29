<?php

declare(strict_types=1);

namespace Orryv;

use InvalidArgumentException;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;
use Orryv\XString\Regex;
use Orryv\XString\Exceptions\EmptyCharacterSetException;
use Orryv\XString\Exceptions\InvalidLengthException;
use RuntimeException;
use Stringable;

final class XString implements Stringable
{
    private const DEFAULT_ENCODING = 'UTF-8';
    private const DEFAULT_MODE = 'graphemes';
    /** @var array<int, string> */
    private const VALID_MODES = ['bytes', 'codepoints', 'graphemes'];

    private string $value;
    private string $mode;
    private string $encoding;

    private function __construct(string $value, string $mode = self::DEFAULT_MODE, string $encoding = self::DEFAULT_ENCODING)
    {
        $this->value = $value;
        $this->mode = self::normalizeMode($mode);
        $this->encoding = self::normalizeEncoding($encoding);
    }

    /**
     * @param array<int, HtmlTag|Newline|Regex|Stringable|string> $data
     */
    public static function new(HtmlTag|Newline|Regex|Stringable|string|array $data = ''): self
    {
        if (is_array($data)) {
            return new self(self::concatenateFragments($data));
        }

        return new self(self::normalizeFragment($data));
    }

    public static function rand(int $length, string $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): self
    {
        if ($length < 1) {
            throw new InvalidLengthException('Random length must be at least 1.');
        }

        $pool = self::splitCharacters($characters);
        if (empty($pool)) {
            throw new EmptyCharacterSetException('Random generator requires at least one character.');
        }

        $buffer = '';
        $maxIndex = count($pool) - 1;
        for ($i = 0; $i < $length; $i++) {
            $buffer .= $pool[random_int(0, $maxIndex)];
        }

        return new self($buffer);
    }

    public static function randInt(int $length, int $int_min = 0, int $int_max = 9): self
    {
        if ($length < 1) {
            throw new InvalidLengthException('Random length must be at least 1.');
        }

        if ($int_min > $int_max) {
            throw new InvalidArgumentException('The minimum digit cannot be greater than the maximum digit.');
        }

        if ($int_min < 0 || $int_max > 9) {
            throw new InvalidArgumentException('randInt only supports digits in the inclusive range 0-9.');
        }

        $buffer = '';
        for ($i = 0; $i < $length; $i++) {
            $buffer .= (string) random_int($int_min, $int_max);
        }

        return new self($buffer);
    }

    public static function randLower(int $length, bool $include_numbers = false): self
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        if ($include_numbers) {
            $characters .= '0123456789';
        }

        return self::rand($length, $characters);
    }

    public static function randUpper(int $length, bool $include_numbers = false): self
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($include_numbers) {
            $characters .= '0123456789';
        }

        return self::rand($length, $characters);
    }

    public static function randAlpha(int $length): self
    {
        return self::rand($length, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
    }

    public static function randHex(int $length): self
    {
        return self::rand($length, '0123456789abcdef');
    }

    public static function randBase64(int $length): self
    {
        return self::rand($length, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/');
    }

    public static function randBase62(int $length): self
    {
        return self::rand($length, '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
    }

    public static function uuid(int $version = 4, ?string $namespace = null, ?string $name = null): self
    {
        return new self(self::generateUuid($version, $namespace, $name));
    }

    /**
     * @param array<int, HtmlTag|Newline|Regex|Stringable|string> $data
     */
    public static function implode(array $data, string $glue = ''): self
    {
        $normalized = [];
        foreach ($data as $fragment) {
            $normalized[] = self::normalizeFragment($fragment);
        }

        return new self(implode($glue, $normalized));
    }

    /**
     * @param array<int, HtmlTag|Newline|Regex|Stringable|string> $data
     */
    public static function join(array $data, string $glue = ''): self
    {
        return self::implode($data, $glue);
    }

    public static function fromFile(
        string $file_path,
        ?int $length = null,
        ?int $offset = 0,
        string $encoding = self::DEFAULT_ENCODING
    ): self {
        if (!is_file($file_path) || !is_readable($file_path)) {
            throw new RuntimeException(sprintf('File "%s" is not readable.', $file_path));
        }

        $offset = $offset ?? 0;
        if ($offset < 0) {
            throw new InvalidArgumentException('Offset must be greater than or equal to 0.');
        }

        if ($length !== null && $length < 0) {
            throw new InvalidArgumentException('Length must be greater than or equal to 0.');
        }

        $encoding = self::normalizeEncoding($encoding);

        $content = $length === null
            ? file_get_contents($file_path, false, null, $offset)
            : file_get_contents($file_path, false, null, $offset, $length);

        if ($content === false) {
            throw new RuntimeException(sprintf('Failed to read file "%s".', $file_path));
        }

        return new self($content, self::DEFAULT_MODE, $encoding);
    }

    public function length(): int
    {
        return match ($this->mode) {
            'bytes' => strlen($this->value),
            'codepoints' => function_exists('mb_strlen')
                ? mb_strlen($this->value, $this->encoding)
                : strlen($this->value),
            default => $this->graphemeLengthOrFallback(),
        };
    }

    public function withMode(string $mode = self::DEFAULT_MODE, string $encoding = self::DEFAULT_ENCODING): self
    {
        return new self($this->value, $mode, $encoding);
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string>|null $data
     */
    public function append(HtmlTag|Newline|Regex|Stringable|string|array|null $data): self
    {
        $additional = is_array($data)
            ? self::concatenateFragments($data)
            : self::normalizeFragment($data);

        return new self($this->value . $additional, $this->mode, $this->encoding);
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string>|null $data
     */
    public function prepend(HtmlTag|Newline|Regex|Stringable|string|array|null $data): self
    {
        $additional = is_array($data)
            ? self::concatenateFragments($data)
            : self::normalizeFragment($data);

        return new self($additional . $this->value, $this->mode, $this->encoding);
    }

    public function toUpper(): self
    {
        $upper = function_exists('mb_strtoupper')
            ? mb_strtoupper($this->value, $this->encoding)
            : strtoupper($this->value);

        return new self($upper, $this->mode, $this->encoding);
    }

    public function toUpperCase(): self
    {
        return $this->toUpper();
    }

    public function toLower(): self
    {
        if (function_exists('mb_convert_case')) {
            $lower = mb_convert_case($this->value, MB_CASE_LOWER_SIMPLE, $this->encoding);
        } elseif (function_exists('mb_strtolower')) {
            $lower = mb_strtolower($this->value, $this->encoding);
        } else {
            $lower = strtolower($this->value);
        }

        return new self($lower, $this->mode, $this->encoding);
    }

    public function toLowerCase(): self
    {
        return $this->toLower();
    }

    public function ucfirst(): self
    {
        if ($this->value === '') {
            return new self('', $this->mode, $this->encoding);
        }

        if (function_exists('mb_substr')) {
            $first = mb_substr($this->value, 0, 1, $this->encoding);
            $rest = mb_substr($this->value, 1, null, $this->encoding);

            if ($first !== false && $rest !== false) {
                $upperFirst = function_exists('mb_strtoupper')
                    ? mb_strtoupper($first, $this->encoding)
                    : strtoupper($first);

                return new self($upperFirst . $rest, $this->mode, $this->encoding);
            }
        }

        $first = substr($this->value, 0, 1);
        $rest = substr($this->value, 1);

        return new self(strtoupper($first) . $rest, $this->mode, $this->encoding);
    }

    public function lcfirst(): self
    {
        if ($this->value === '') {
            return new self('', $this->mode, $this->encoding);
        }

        if (function_exists('mb_substr')) {
            $first = mb_substr($this->value, 0, 1, $this->encoding);
            $rest = mb_substr($this->value, 1, null, $this->encoding);

            if ($first !== false && $rest !== false) {
                $lowerFirst = function_exists('mb_strtolower')
                    ? mb_strtolower($first, $this->encoding)
                    : strtolower($first);

                return new self($lowerFirst . $rest, $this->mode, $this->encoding);
            }
        }

        $first = substr($this->value, 0, 1);
        $rest = substr($this->value, 1);

        return new self(strtolower($first) . $rest, $this->mode, $this->encoding);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private static function normalizeFragment(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if ($value instanceof Stringable) {
            return (string) $value;
        }

        if ($value === null) {
            return '';
        }

        throw new InvalidArgumentException('Only strings and stringable values are supported.');
    }

    /**
     * @param array<int, HtmlTag|Newline|Regex|Stringable|string|null> $data
     */
    private static function concatenateFragments(array $data): string
    {
        $fragments = [];
        foreach ($data as $fragment) {
            $fragments[] = self::normalizeFragment($fragment);
        }

        return implode('', $fragments);
    }

    /**
     * @return array<int, string>
     */
    private static function splitCharacters(string $characters): array
    {
        if ($characters === '') {
            return [];
        }

        if (preg_match('//u', $characters) === 1) {
            $parts = preg_split('//u', $characters, -1, PREG_SPLIT_NO_EMPTY);
            if ($parts !== false) {
                return $parts;
            }
        }

        return str_split($characters);
    }

    private static function normalizeMode(string $mode): string
    {
        $normalized = strtolower($mode);
        if (!in_array($normalized, self::VALID_MODES, true)) {
            throw new InvalidArgumentException('Invalid mode provided.');
        }

        return $normalized;
    }

    private static function normalizeEncoding(string $encoding): string
    {
        $normalized = trim($encoding);
        if ($normalized === '') {
            throw new InvalidArgumentException('Encoding cannot be empty.');
        }

        return $normalized;
    }

    private function graphemeLengthOrFallback(): int
    {
        if (function_exists('grapheme_strlen')) {
            $length = grapheme_strlen($this->value);
            if ($length !== false) {
                return $length;
            }
        }

        if (function_exists('mb_strlen')) {
            return mb_strlen($this->value, $this->encoding);
        }

        return strlen($this->value);
    }

    private static function generateUuid(int $version, ?string $namespace, ?string $name): string
    {
        return match ($version) {
            1 => self::uuidV1(),
            3 => self::uuidFromHash(self::namespaceAndNameHash($namespace, $name, 3), 3),
            4 => self::uuidFromRandomBytes(random_bytes(16), 4),
            5 => self::uuidFromHash(self::namespaceAndNameHash($namespace, $name, 5), 5),
            default => throw new InvalidArgumentException('Unsupported UUID version. Use 1, 3, 4, or 5.'),
        };
    }

    private static function uuidV1(): string
    {
        $timestamp = (int) floor(microtime(true) * 10000000) + 0x01B21DD213814000;

        $timeLow = $timestamp & 0xffffffff;
        $timeMid = ($timestamp >> 32) & 0xffff;
        $timeHi = ($timestamp >> 48) & 0x0fff;
        $timeHi |= 0x1000;

        $clockSeq = random_int(0, 0x3fff);
        $clockSeqHi = (($clockSeq >> 8) & 0x3f) | 0x80;
        $clockSeqLow = $clockSeq & 0xff;

        $node = random_bytes(6);
        $node[0] = chr(ord($node[0]) | 0x01);

        return sprintf(
            '%08x-%04x-%04x-%02x%02x-%s',
            $timeLow,
            $timeMid,
            $timeHi,
            $clockSeqHi,
            $clockSeqLow,
            bin2hex($node)
        );
    }

    private static function namespaceAndNameHash(?string $namespace, ?string $name, int $version): string
    {
        if ($namespace === null || $namespace === '') {
            throw new InvalidArgumentException('Namespace UUID is required for this UUID version.');
        }

        if ($name === null || $name === '') {
            throw new InvalidArgumentException('Name is required for this UUID version.');
        }

        $namespaceBytes = self::uuidToBytes($namespace);

        return match ($version) {
            3 => md5($namespaceBytes . $name, true),
            5 => substr(sha1($namespaceBytes . $name, true), 0, 16),
            default => throw new InvalidArgumentException('Invalid UUID hashing version.'),
        };
    }

    private static function uuidToBytes(string $uuid): string
    {
        $normalized = strtolower(str_replace(['{', '}', '-'], '', $uuid));

        if (!preg_match('/^[0-9a-f]{32}$/', $normalized)) {
            throw new InvalidArgumentException('Namespace must be a valid UUID string.');
        }

        $bytes = hex2bin($normalized);
        if ($bytes === false) {
            throw new InvalidArgumentException('Namespace must be a valid UUID string.');
        }

        return $bytes;
    }

    private static function uuidFromHash(string $hash, int $version): string
    {
        return self::formatUuid(self::applyVersionAndVariant($hash, $version));
    }

    private static function uuidFromRandomBytes(string $bytes, int $version): string
    {
        return self::formatUuid(self::applyVersionAndVariant($bytes, $version));
    }

    private static function applyVersionAndVariant(string $bytes, int $version): string
    {
        if (strlen($bytes) !== 16) {
            throw new InvalidArgumentException('UUID generation requires 16 bytes of data.');
        }

        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | ($version << 4));
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

        return $bytes;
    }

    private static function formatUuid(string $bytes): string
    {
        $hex = bin2hex($bytes);

        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }
}
