<?php

declare(strict_types=1);

namespace Orryv;

use BadMethodCallException;
use InvalidArgumentException;
use Normalizer;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;
use Orryv\XString\Regex;
use Orryv\XString\Compute\Similarity;
use Orryv\XString\Exceptions\EmptyCharacterSetException;
use Orryv\XString\Exceptions\InvalidLengthException;
use Orryv\XString\Exceptions\InvalidValueConversionException;
use RuntimeException;
use SodiumException;
use Stringable;
use Throwable;
use ValueError;

final class XString implements Stringable
{
    private const DEFAULT_ENCODING = 'UTF-8';
    private const DEFAULT_MODE = 'graphemes';
    /** @var array<int, string> */
    private const VALID_MODES = ['bytes', 'codepoints', 'graphemes'];
    /** @var array<int, string> */
    private const WINDOWS_RESERVED_NAMES = [
        'CON',
        'PRN',
        'AUX',
        'NUL',
        'CLOCK$',
        'COM1',
        'COM2',
        'COM3',
        'COM4',
        'COM5',
        'COM6',
        'COM7',
        'COM8',
        'COM9',
        'LPT1',
        'LPT2',
        'LPT3',
        'LPT4',
        'LPT5',
        'LPT6',
        'LPT7',
        'LPT8',
        'LPT9',
    ];
    /** @var array<string, string> */
    private const ASCII_FALLBACK_REPLACEMENTS = [
        'ß' => 'ss',
        'ẞ' => 'SS',
        'Æ' => 'AE',
        'æ' => 'ae',
        'Œ' => 'OE',
        'œ' => 'oe',
        'Ø' => 'O',
        'ø' => 'o',
        'Đ' => 'D',
        'đ' => 'd',
        'Ł' => 'L',
        'ł' => 'l',
        'Þ' => 'Th',
        'þ' => 'th',
        'Ŋ' => 'N',
        'ŋ' => 'n',
        'Ð' => 'D',
        'ð' => 'd',
        'Ç' => 'C',
        'ç' => 'c',
        'Ć' => 'C',
        'ć' => 'c',
        'Č' => 'C',
        'č' => 'c',
        'Ś' => 'S',
        'ś' => 's',
        'Ş' => 'S',
        'ş' => 's',
        'Š' => 'S',
        'š' => 's',
        'Ž' => 'Z',
        'ž' => 'z',
        'Ź' => 'Z',
        'ź' => 'z',
        'Ż' => 'Z',
        'ż' => 'z',
        'Ŕ' => 'R',
        'ŕ' => 'r',
        'Ŵ' => 'W',
        'ŵ' => 'w',
        'Ŷ' => 'Y',
        'ŷ' => 'y',
        'Ý' => 'Y',
        'ý' => 'y',
        'Ÿ' => 'Y',
        'ÿ' => 'y',
    ];
    private const ENCRYPTION_VERSION = 1;
    /** @var array<string, int> */
    private const ENCRYPTION_ALGORITHM_IDS = [
        'sodium_xchacha20' => 1,
        'aes-256-gcm' => 2,
    ];
    private const ENCRYPTION_HEADER_LENGTH = 5;
    private const ENCRYPTION_SALT_BYTES = 16;
    private const ENCRYPTION_KEY_BYTES = 32;
    private const PBKDF2_ITERATIONS = 150000;

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

    public static function __callStatic(string $name, array $arguments): mixed
    {
        $instance = $arguments === []
            ? self::new()
            : self::new(array_shift($arguments));

        if (!method_exists($instance, $name)) {
            throw new BadMethodCallException(sprintf('Call to undefined method %s::%s()', self::class, $name));
        }

        return $instance->$name(...$arguments);
    }

    public function __call(string $name, array $arguments): mixed
    {
        if (!method_exists($this, $name)) {
            throw new BadMethodCallException(sprintf('Call to undefined method %s::%s()', self::class, $name));
        }

        return $this->$name(...$arguments);
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

    protected function length(): int
    {
        return match ($this->mode) {
            'bytes' => strlen($this->value),
            'codepoints' => function_exists('mb_strlen')
                ? mb_strlen($this->value, $this->encoding)
                : strlen($this->value),
            default => $this->graphemeLengthOrFallback(),
        };
    }

    protected function byteLength(): int
    {
        return strlen($this->value);
    }

    protected function graphemeLength(): int
    {
        return $this->graphemeLengthOrFallback();
    }

    protected function withMode(string $mode = self::DEFAULT_MODE, string $encoding = self::DEFAULT_ENCODING): self
    {
        return new self($this->value, $mode, $encoding);
    }

    protected function asBytes(string $encoding = self::DEFAULT_ENCODING): self
    {
        return $this->withMode('bytes', $encoding);
    }

    protected function asCodepoints(string $encoding = self::DEFAULT_ENCODING): self
    {
        return $this->withMode('codepoints', $encoding);
    }

    protected function asGraphemes(string $encoding = self::DEFAULT_ENCODING): self
    {
        return $this->withMode('graphemes', $encoding);
    }

    protected function charAt(int $index): string
    {
        $units = self::splitByMode($this->value, $this->mode, $this->encoding);
        if ($units === []) {
            throw new InvalidArgumentException('Index is out of range.');
        }

        $count = count($units);
        $position = $index >= 0 ? $index : $count + $index;

        if ($position < 0 || $position >= $count) {
            throw new InvalidArgumentException('Index is out of range.');
        }

        return $units[$position];
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string>|null $data
     */
    protected function append(HtmlTag|Newline|Regex|Stringable|string|array|null $data): self
    {
        $additional = is_array($data)
            ? self::concatenateFragments($data)
            : self::normalizeFragment($data);

        return new self($this->value . $additional, $this->mode, $this->encoding);
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string>|null $data
     */
    protected function prepend(HtmlTag|Newline|Regex|Stringable|string|array|null $data): self
    {
        $additional = is_array($data)
            ? self::concatenateFragments($data)
            : self::normalizeFragment($data);

        return new self($additional . $this->value, $this->mode, $this->encoding);
    }

    protected function substr(int $start, ?int $length = null): self
    {
        $units = self::splitByMode($this->value, $this->mode, $this->encoding);
        $total = count($units);

        if ($total === 0) {
            return new self('', $this->mode, $this->encoding);
        }

        $start_index = $start >= 0 ? $start : $total + $start;
        if ($start_index < 0) {
            $start_index = 0;
        }

        if ($start_index >= $total) {
            return new self('', $this->mode, $this->encoding);
        }

        if ($length === null) {
            $slice = array_slice($units, $start_index);
        } elseif ($length >= 0) {
            $slice = array_slice($units, $start_index, $length);
        } else {
            $end_index = $total + $length;
            if ($end_index <= $start_index) {
                return new self('', $this->mode, $this->encoding);
            }

            $slice = array_slice($units, $start_index, $end_index - $start_index);
        }

        return new self(implode('', $slice), $this->mode, $this->encoding);
    }

    protected function limit(int $length, HtmlTag|Newline|Stringable|string $append_string = '...'): self
    {
        if ($length < 0) {
            throw new InvalidArgumentException('Length must be greater than or equal to 0.');
        }

        $units = self::splitByMode($this->value, $this->mode, $this->encoding);
        $count = count($units);

        if ($count <= $length) {
            return new self($this->value, $this->mode, $this->encoding);
        }

        $suffix = self::normalizeFragment($append_string);

        if ($length === 0) {
            return new self($suffix, $this->mode, $this->encoding);
        }

        $slice = array_slice($units, 0, $length);
        $result = implode('', $slice);

        if ($suffix !== '') {
            $result .= $suffix;
        }

        return new self($result, $this->mode, $this->encoding);
    }

    protected function repeat(int $times): self
    {
        if ($times < 0) {
            throw new InvalidArgumentException('Repeat count must be greater than or equal to 0.');
        }

        if ($times === 0) {
            return new self('', $this->mode, $this->encoding);
        }

        return new self(str_repeat($this->value, $times), $this->mode, $this->encoding);
    }

    protected function reverse(): self
    {
        $units = self::splitByMode($this->value, $this->mode, $this->encoding);
        if ($units === []) {
            return new self('', $this->mode, $this->encoding);
        }

        $units = array_reverse($units);

        return new self(implode('', $units), $this->mode, $this->encoding);
    }

    protected function shuffle(): self
    {
        $units = self::splitByMode($this->value, $this->mode, $this->encoding);
        if (count($units) <= 1) {
            return new self($this->value, $this->mode, $this->encoding);
        }

        shuffle($units);

        return new self(implode('', $units), $this->mode, $this->encoding);
    }

    protected function slug(Newline|HtmlTag|string $separator = '-'): self
    {
        $normalized_separator = self::normalizeFragment($separator);
        if ($normalized_separator === '') {
            throw new InvalidArgumentException('Separator cannot be empty.');
        }

        $slug_source = self::toAsciiForSlug($this->value, $this->encoding);
        $slug_source = self::lowercaseString($slug_source, $this->encoding);

        $slug = preg_replace('/[^a-z0-9]+/i', $normalized_separator, $slug_source);
        if ($slug === null) {
            $slug = '';
        }

        $quoted_separator = preg_quote($normalized_separator, '/');
        $slug = preg_replace(sprintf('/%s{2,}/', $quoted_separator), $normalized_separator, $slug);
        if ($slug === null) {
            $slug = '';
        }

        $slug = trim($slug, $normalized_separator);

        return new self($slug, $this->mode, $this->encoding);
    }

    protected function fileNameSlug(Newline|HtmlTag|string $separator = '-'): self
    {
        $normalized_separator = self::normalizeFragment($separator);
        if ($normalized_separator === '') {
            throw new InvalidArgumentException('Separator cannot be empty.');
        }

        $slug_source = self::toAsciiForSlug($this->value, $this->encoding);
        $slug_source = self::lowercaseString($slug_source, $this->encoding);

        $slug_source = str_replace(['\\', '/', ':', '*', '?', '"', '<', '>', '|'], ' ', $slug_source);

        $slug = preg_replace('/[^a-z0-9._-]+/i', $normalized_separator, $slug_source);
        if ($slug === null) {
            $slug = '';
        }

        $slug = preg_replace('/\.{2,}/', '.', $slug);
        if ($slug === null) {
            $slug = '';
        }

        $quoted_separator = preg_quote($normalized_separator, '/');
        $slug = preg_replace(sprintf('/(?:%s){2,}/', $quoted_separator), $normalized_separator, $slug);
        if ($slug === null) {
            $slug = '';
        }

        if ($normalized_separator !== '') {
            while (str_contains($slug, $normalized_separator . '.')) {
                $slug = str_replace($normalized_separator . '.', '.', $slug);
            }

            while (str_contains($slug, '.' . $normalized_separator)) {
                $slug = str_replace('.' . $normalized_separator, '.', $slug);
            }
        }

        $slug = trim($slug, $normalized_separator . '.');

        return new self($slug, $this->mode, $this->encoding);
    }

    protected function toWindowsFileName(): self
    {
        $sanitized = self::sanitizeWindowsSegment($this->value);

        return new self($sanitized, $this->mode, $this->encoding);
    }

    protected function toWindowsFolderName(): self
    {
        $sanitized = self::sanitizeWindowsSegment($this->value);

        return new self($sanitized, $this->mode, $this->encoding);
    }

    protected function toWindowsPath(): self
    {
        $value = str_replace('/', '\\', $this->value);
        $has_trailing = preg_match('/\\\\$/', rtrim($value)) === 1;

        $prefix = '';
        $remaining = $value;
        $is_unc = false;

        if (str_starts_with($remaining, '\\\\')) {
            $prefix = '\\\\';
            $remaining = substr($remaining, 2);
            $is_unc = true;
        } elseif (preg_match('/^([A-Za-z]):/', $remaining, $drive_match) === 1) {
            $prefix = strtoupper($drive_match[1]) . ':';
            $remaining = substr($remaining, 2);
            if (str_starts_with($remaining, '\\')) {
                $prefix .= '\\';
                $remaining = substr($remaining, 1);
            }
        }

        $segments = preg_split('/[\\\\]+/', $remaining, -1, PREG_SPLIT_NO_EMPTY);
        if (!is_array($segments)) {
            $segments = [];
        }

        $sanitized_segments = [];
        foreach ($segments as $segment) {
            $sanitized_segments[] = self::sanitizeWindowsSegment($segment);
        }

        $result = $prefix;
        if ($sanitized_segments !== []) {
            if ($result !== '' && !str_ends_with($result, '\\')) {
                $result .= '\\';
            }
            $result .= implode('\\', $sanitized_segments);
        }

        if ($result === '') {
            $result = '_';
        } elseif ($is_unc && $result === '\\\\') {
            $result .= '_';
        }

        if ($has_trailing && !str_ends_with($result, '\\')) {
            $result .= '\\';
        }

        return new self($result, $this->mode, $this->encoding);
    }

    protected function encodeWindowsFileName(bool $double_encode = false): self
    {
        $encoded = self::encodeWindowsSegment($this->value, $double_encode);

        return new self($encoded, $this->mode, $this->encoding);
    }

    protected function decodeWindowsFileName(): self
    {
        $decoded = self::decodeWindowsSegment($this->value);

        return new self($decoded, $this->mode, $this->encoding);
    }

    protected function encodeWindowsFolderName(bool $double_encode = false): self
    {
        $encoded = self::encodeWindowsSegment($this->value, $double_encode);

        return new self($encoded, $this->mode, $this->encoding);
    }

    protected function decodeWindowsFolderName(): self
    {
        $decoded = self::decodeWindowsSegment($this->value);

        return new self($decoded, $this->mode, $this->encoding);
    }

    protected function encodeWindowsPath(bool $double_encode = false): self
    {
        $value = str_replace('/', '\\', $this->value);
        $encoded = self::encodeWindowsPathString($value, $double_encode);

        return new self($encoded, $this->mode, $this->encoding);
    }

    protected function decodeWindowsPath(): self
    {
        $decoded = self::decodeWindowsPathString($this->value);

        return new self($decoded, $this->mode, $this->encoding);
    }

    protected function toUnixFileName(): self
    {
        $sanitized = self::sanitizeUnixSegment($this->value, false);

        return new self($sanitized, $this->mode, $this->encoding);
    }

    protected function toUnixFolderName(): self
    {
        $sanitized = self::sanitizeUnixSegment($this->value, false);

        return new self($sanitized, $this->mode, $this->encoding);
    }

    protected function toUnixPath(): self
    {
        $value = str_replace('\\', '/', $this->value);
        $is_absolute = str_starts_with($value, '/');
        $has_trailing = preg_match('#/(?:\s*)$#', rtrim($value)) === 1;

        $segments = preg_split('~\/+~', $value, -1, PREG_SPLIT_NO_EMPTY);
        if (!is_array($segments)) {
            $segments = [];
        }

        $sanitized_segments = [];
        foreach ($segments as $segment) {
            $sanitized_segments[] = self::sanitizeUnixSegment($segment, false);
        }

        $result = $is_absolute ? '/' : '';
        if ($sanitized_segments !== []) {
            $result .= implode('/', $sanitized_segments);
        }

        if ($result === '') {
            $result = '_';
        }

        if ($has_trailing && !str_ends_with($result, '/')) {
            $result .= '/';
        }

        return new self($result, $this->mode, $this->encoding);
    }

    protected function encodeUnixFileName(bool $double_encode = false): self
    {
        $encoded = self::encodeUnixSegment($this->value, false, $double_encode);

        return new self($encoded, $this->mode, $this->encoding);
    }

    protected function decodeUnixFileName(): self
    {
        $decoded = self::decodeUnixSegment($this->value);

        return new self($decoded, $this->mode, $this->encoding);
    }

    protected function encodeUnixFolderName(bool $double_encode = false): self
    {
        $encoded = self::encodeUnixSegment($this->value, false, $double_encode);

        return new self($encoded, $this->mode, $this->encoding);
    }

    protected function decodeUnixFolderName(): self
    {
        $decoded = self::decodeUnixSegment($this->value);

        return new self($decoded, $this->mode, $this->encoding);
    }

    protected function encodeUnixPath(bool $double_encode = false): self
    {
        $encoded = self::encodeUnixPathString($this->value, false, $double_encode);

        return new self($encoded, $this->mode, $this->encoding);
    }

    protected function decodeUnixPath(): self
    {
        $decoded = self::decodeUnixPathString($this->value);

        return new self($decoded, $this->mode, $this->encoding);
    }

    protected function toMacOSFileName(): self
    {
        $sanitized = self::sanitizeUnixSegment($this->value, true);

        return new self($sanitized, $this->mode, $this->encoding);
    }

    protected function toMacOSFolderName(): self
    {
        $sanitized = self::sanitizeUnixSegment($this->value, true);

        return new self($sanitized, $this->mode, $this->encoding);
    }

    protected function toMacOSPath(): self
    {
        $value = str_replace('\\', '/', $this->value);
        $is_absolute = str_starts_with($value, '/');
        $has_trailing = preg_match('#/(?:\s*)$#', rtrim($value)) === 1;

        $segments = preg_split('~\/+~', $value, -1, PREG_SPLIT_NO_EMPTY);
        if (!is_array($segments)) {
            $segments = [];
        }

        $sanitized_segments = [];
        foreach ($segments as $segment) {
            $sanitized_segments[] = self::sanitizeUnixSegment($segment, true);
        }

        $result = $is_absolute ? '/' : '';
        if ($sanitized_segments !== []) {
            $result .= implode('/', $sanitized_segments);
        }

        if ($result === '') {
            $result = '_';
        }

        if ($has_trailing && !str_ends_with($result, '/')) {
            $result .= '/';
        }

        return new self($result, $this->mode, $this->encoding);
    }

    protected function encodeMacOSFileName(bool $double_encode = false): self
    {
        $encoded = self::encodeUnixSegment($this->value, true, $double_encode);

        return new self($encoded, $this->mode, $this->encoding);
    }

    protected function decodeMacOSFileName(): self
    {
        $decoded = self::decodeUnixSegment($this->value);

        return new self($decoded, $this->mode, $this->encoding);
    }

    protected function encodeMacOSFolderName(bool $double_encode = false): self
    {
        $encoded = self::encodeUnixSegment($this->value, true, $double_encode);

        return new self($encoded, $this->mode, $this->encoding);
    }

    protected function decodeMacOSFolderName(): self
    {
        $decoded = self::decodeUnixSegment($this->value);

        return new self($decoded, $this->mode, $this->encoding);
    }

    protected function encodeMacOSPath(bool $double_encode = false): self
    {
        $value = str_replace('\\', '/', $this->value);
        $encoded = self::encodeUnixPathString($value, true, $double_encode);

        return new self($encoded, $this->mode, $this->encoding);
    }

    protected function decodeMacOSPath(): self
    {
        $value = str_replace('\\', '/', $this->value);
        $decoded = self::decodeUnixPathString($value);

        return new self($decoded, $this->mode, $this->encoding);
    }

    protected function toSafeFileName(): self
    {
        $sanitized = self::sanitizeGenericSegment($this->value);

        return new self($sanitized, $this->mode, $this->encoding);
    }

    protected function toSafeFolderName(): self
    {
        $sanitized = self::sanitizeGenericSegment($this->value);

        return new self($sanitized, $this->mode, $this->encoding);
    }

    protected function toSafePath(): self
    {
        $value = str_replace('\\', '/', $this->value);
        $is_absolute = str_starts_with($value, '/');
        $has_trailing = preg_match('#/(?:\s*)$#', rtrim($value)) === 1;

        $segments = preg_split('~\/+~', $value, -1, PREG_SPLIT_NO_EMPTY);
        if (!is_array($segments)) {
            $segments = [];
        }

        $sanitized_segments = [];
        foreach ($segments as $segment) {
            $sanitized_segments[] = self::sanitizeGenericSegment($segment);
        }

        $result = $is_absolute ? '/' : '';
        if ($sanitized_segments !== []) {
            $result .= implode('/', $sanitized_segments);
        }

        if ($result === '') {
            $result = '_';
        }

        if ($has_trailing && !str_ends_with($result, '/')) {
            $result .= '/';
        }

        return new self($result, $this->mode, $this->encoding);
    }

    protected function encodeSafeFileName(bool $double_encode = false): self
    {
        $encoded = self::encodeGenericSegment($this->value, $double_encode);

        return new self($encoded, $this->mode, $this->encoding);
    }

    protected function decodeSafeFileName(): self
    {
        $decoded = self::decodeGenericSegment($this->value);

        return new self($decoded, $this->mode, $this->encoding);
    }

    protected function encodeSafeFolderName(bool $double_encode = false): self
    {
        $encoded = self::encodeGenericSegment($this->value, $double_encode);

        return new self($encoded, $this->mode, $this->encoding);
    }

    protected function decodeSafeFolderName(): self
    {
        $decoded = self::decodeGenericSegment($this->value);

        return new self($decoded, $this->mode, $this->encoding);
    }

    protected function encodeSafePath(bool $double_encode = false): self
    {
        $value = str_replace('\\', '/', $this->value);
        $encoded = self::encodeGenericPathString($value, $double_encode);

        return new self($encoded, $this->mode, $this->encoding);
    }

    protected function decodeSafePath(): self
    {
        $value = str_replace('\\', '/', $this->value);
        $decoded = self::decodeGenericPathString($value);

        return new self($decoded, $this->mode, $this->encoding);
    }

    protected function insertAtInterval(Newline|HtmlTag|Regex|string $insert, int $interval): self
    {
        if ($interval < 1) {
            throw new InvalidArgumentException('Interval must be at least 1.');
        }

        $fragment = self::normalizeFragment($insert);
        $units = self::splitByMode($this->value, $this->mode, $this->encoding);

        if ($units === []) {
            return new self($this->value, $this->mode, $this->encoding);
        }

        $parts = [];
        foreach ($units as $index => $unit) {
            if ($index > 0 && $index % $interval === 0) {
                $parts[] = $fragment;
            }

            $parts[] = $unit;
        }

        return new self(implode('', $parts), $this->mode, $this->encoding);
    }

    protected function wrap(Newline|HtmlTag|Regex|string $before, Newline|HtmlTag|Regex|string|null $after = null): self
    {
        $prefix = self::normalizeFragment($before);
        $suffix = $after === null
            ? $prefix
            : self::normalizeFragment($after);

        return new self($prefix . $this->value . $suffix, $this->mode, $this->encoding);
    }

    protected function indent(int $spaces = 2, int $tabs = 0, int $lines = 0): self
    {
        if ($spaces < 0 || $tabs < 0) {
            throw new InvalidArgumentException('Indentation parameters must be greater than or equal to 0.');
        }

        if ($this->value === '') {
            return new self('', $this->mode, $this->encoding);
        }

        $indentation = str_repeat(' ', $spaces) . str_repeat("\t", $tabs);
        if ($indentation === '') {
            return new self($this->value, $this->mode, $this->encoding);
        }

        if ($lines < 0) {
            $total_lines = preg_match_all('/^/m', $this->value);
            if ($total_lines === false || $total_lines === 0) {
                return new self($this->value, $this->mode, $this->encoding);
            }

            $target = min(-$lines, $total_lines);
            if ($target === 0) {
                return new self($this->value, $this->mode, $this->encoding);
            }

            $processed = 0;
            $result = preg_replace_callback(
                '/^/m',
                static function (array $matches) use (&$processed, $total_lines, $target, $indentation): string {
                    $should_indent = $processed >= ($total_lines - $target);
                    $processed++;

                    return $should_indent ? $indentation : $matches[0];
                },
                $this->value
            );
        } else {
            $limit = $lines > 0 ? $lines : -1;

            $result = preg_replace('/^/m', $indentation, $this->value, $limit);
        }

        if ($result === null) {
            $result = $this->value;
        }

        return new self($result, $this->mode, $this->encoding);
    }

    protected function outdent(int $spaces = 2, int $tabs = 0, int $lines = 0): self
    {
        if ($spaces < 0 || $tabs < 0) {
            throw new InvalidArgumentException('Indentation parameters must be greater than or equal to 0.');
        }

        if ($this->value === '') {
            return new self('', $this->mode, $this->encoding);
        }

        if ($spaces === 0 && $tabs === 0) {
            return new self($this->value, $this->mode, $this->encoding);
        }

        if ($lines < 0) {
            $total_lines = preg_match_all('/^/m', $this->value);
            if ($total_lines === false || $total_lines === 0) {
                return new self($this->value, $this->mode, $this->encoding);
            }

            $target = min(-$lines, $total_lines);
            if ($target === 0) {
                return new self($this->value, $this->mode, $this->encoding);
            }

            $processed = 0;
            $result = preg_replace_callback(
                '/^([ \t]*)/m',
                static function (array $matches) use (&$processed, $total_lines, $target, $spaces, $tabs): string {
                    $should_outdent = $processed >= ($total_lines - $target);
                    $processed++;

                    if ($should_outdent) {
                        return self::removeIndentationPrefix($matches[1], $spaces, $tabs);
                    }

                    return $matches[1];
                },
                $this->value
            );
        } else {
            $limit = $lines > 0 ? $lines : -1;

            $result = preg_replace_callback(
                '/^([ \t]*)/m',
                static function (array $matches) use ($spaces, $tabs): string {
                    return self::removeIndentationPrefix($matches[1], $spaces, $tabs);
                },
                $this->value,
                $limit
            );
        }

        if ($result === null) {
            $result = $this->value;
        }

        return new self($result, $this->mode, $this->encoding);
    }

    protected function normalize(int $form = Normalizer::FORM_C): self
    {
        if (!class_exists(Normalizer::class)) {
            throw new RuntimeException('The intl extension (Normalizer class) is required for normalization.');
        }

        try {
            $normalized = Normalizer::normalize($this->value, $form);
        } catch (ValueError $exception) {
            throw new InvalidArgumentException('Invalid normalization form provided.', 0, $exception);
        }
        if ($normalized === false) {
            throw new RuntimeException('Failed to normalize the string.');
        }

        return new self($normalized, $this->mode, $this->encoding);
    }

    protected function pad(
        int $length,
        Newline|HtmlTag|Regex|string $pad_string = ' ',
        bool $left = true,
        bool $right = true
    ): self {
        if ($length < 0) {
            throw new InvalidArgumentException('Target length must be greater than or equal to 0.');
        }

        if (!$left && !$right) {
            throw new InvalidArgumentException('At least one side must be selected for padding.');
        }

        $current_length = $this->length();
        if ($length <= $current_length) {
            return new self($this->value, $this->mode, $this->encoding);
        }

        $pad_fragment = self::normalizeFragment($pad_string);
        if ($pad_fragment === '') {
            throw new InvalidArgumentException('Pad string cannot be empty.');
        }

        $units_needed = $length - $current_length;

        if ($left && $right) {
            $left_units = intdiv($units_needed, 2);
            $right_units = $units_needed - $left_units;
        } elseif ($left) {
            $left_units = $units_needed;
            $right_units = 0;
        } else {
            $left_units = 0;
            $right_units = $units_needed;
        }

        $left_padding = $left_units > 0
            ? self::buildPadding($pad_fragment, $left_units, $this->mode, $this->encoding)
            : '';
        $right_padding = $right_units > 0
            ? self::buildPadding($pad_fragment, $right_units, $this->mode, $this->encoding)
            : '';

        return new self($left_padding . $this->value . $right_padding, $this->mode, $this->encoding);
    }

    protected function lpad(int $length, Newline|HtmlTag|Regex|string $pad_string = ' '): self
    {
        return $this->pad($length, $pad_string, true, false);
    }

    protected function rpad(int $length, Newline|HtmlTag|Regex|string $pad_string = ' '): self
    {
        return $this->pad($length, $pad_string, false, true);
    }

    protected function center(int $length, Newline|HtmlTag|Regex|string $pad_string = ' '): self
    {
        return $this->pad($length, $pad_string, true, true);
    }

    protected function mask(
        Newline|HtmlTag|Regex|string $mask,
        Newline|HtmlTag|Regex|string $mask_char = '#',
        bool $reversed = false
    ): self {
        $pattern = self::normalizeFragment($mask);
        $placeholder = self::normalizeFragment($mask_char);

        $placeholder_units = self::splitGraphemes($placeholder, $this->encoding);
        if (count($placeholder_units) !== 1) {
            throw new InvalidArgumentException('Mask placeholder must be a single grapheme.');
        }

        $placeholder_unit = $placeholder_units[0];
        $mask_units = self::splitGraphemes($pattern, $this->encoding);
        $source_units = self::splitByMode($this->value, $this->mode, $this->encoding);

        $source_count = count($source_units);
        $result_units = [];

        if ($reversed) {
            $source_index = $source_count - 1;
            $digits_used = 0;
            $placeholders_remaining = 0;

            foreach ($mask_units as $mask_unit) {
                if ($mask_unit === $placeholder_unit) {
                    $placeholders_remaining++;
                }
            }

            for ($index = count($mask_units) - 1; $index >= 0; $index--) {
                $mask_unit = $mask_units[$index];

                if ($mask_unit === $placeholder_unit) {
                    if ($source_index >= 0) {
                        $result_units[] = $source_units[$source_index];
                        $source_index--;
                        $digits_used++;
                    }

                    if ($placeholders_remaining > 0) {
                        $placeholders_remaining--;
                    }

                    continue;
                }

                if ($source_index >= 0 || ($digits_used > 0 && $placeholders_remaining === 0)) {
                    $result_units[] = $mask_unit;
                }
            }

            $result_units = array_reverse($result_units);
        } else {
            $source_index = 0;
            $digits_used = 0;

            foreach ($mask_units as $mask_unit) {
                if ($mask_unit === $placeholder_unit) {
                    if ($source_index < $source_count) {
                        $result_units[] = $source_units[$source_index];
                        $source_index++;
                        $digits_used++;
                    } else {
                        break;
                    }

                    continue;
                }

                if ($digits_used > 0 || $source_index < $source_count) {
                    $result_units[] = $mask_unit;
                }
            }
        }

        return new self(implode('', $result_units), $this->mode, $this->encoding);
    }

    protected function collapseWhitespace(bool $space = true, bool $tab = true, bool $newline = false): self
    {
        if (!$space && !$tab && !$newline) {
            return new self($this->value, $this->mode, $this->encoding);
        }

        $result = $this->value;

        if ($space) {
            $collapsed = preg_replace('/ {2,}/', ' ', $result);
            if ($collapsed !== null) {
                $result = $collapsed;
            }
        }

        if ($tab) {
            $collapsed = preg_replace("/\t{2,}/", "\t", $result);
            if ($collapsed !== null) {
                $result = $collapsed;
            }
        }

        if ($newline) {
            $collapsed = preg_replace_callback(
                '/(?:\r\n|\r|\n){2,}/',
                static function (array $matches): string {
                    $sequence = $matches[0];

                    if (str_contains($sequence, "\r\n")) {
                        return "\r\n";
                    }

                    if (str_contains($sequence, "\r")) {
                        return "\r";
                    }

                    return "\n";
                },
                $result
            );

            if ($collapsed !== null) {
                $result = $collapsed;
            }
        }

        return new self($result, $this->mode, $this->encoding);
    }

    protected function collapseWhitespaceToSpace(): self
    {
        return $this->collapseWhitespaceToReplacement(' ');
    }

    protected function collapseWhitespaceToTab(): self
    {
        return $this->collapseWhitespaceToReplacement("\t");
    }

    protected function collapseWhitespaceToNewline(): self
    {
        return $this->collapseWhitespaceToReplacement("\n");
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string|array> $start
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string|array> $end
     */
    protected function between(
        HtmlTag|Newline|Regex|Stringable|string|array $start,
        HtmlTag|Newline|Regex|Stringable|string|array $end,
        bool $last_occurence = false,
        int $skip_start = 0,
        int $skip_end = 0,
        string $start_behavior = 'sequential',
        string $end_behavior = 'sequential'
    ): self {
        if ($skip_start < 0 || $skip_end < 0) {
            throw new InvalidArgumentException('Skip values must be greater than or equal to 0.');
        }

        $start_sequence = self::normalizeSearchOptions($start, $start_behavior);
        $end_sequence = self::normalizeSearchOptions($end, $end_behavior);

        if ($last_occurence) {
            $start_occurrences = self::findAllSequences($this->value, $start_sequence);
            if ($start_occurrences === []) {
                return new self('', $this->mode, $this->encoding);
            }

            $start_index = count($start_occurrences) - 1 - $skip_start;
            if ($start_index < 0 || $start_index >= count($start_occurrences)) {
                return new self('', $this->mode, $this->encoding);
            }

            $start_match = $start_occurrences[$start_index];
        } else {
            $start_match = self::findSequenceWithSkip($this->value, $start_sequence, 0, $skip_start);
            if ($start_match === null) {
                return new self('', $this->mode, $this->encoding);
            }
        }

        $end_match = self::findSequenceWithSkip($this->value, $end_sequence, $start_match['end'], $skip_end);
        if ($end_match === null || $end_match['start'] < $start_match['end']) {
            return new self('', $this->mode, $this->encoding);
        }

        return new self(
            substr($this->value, $start_match['end'], $end_match['start'] - $start_match['end']),
            $this->mode,
            $this->encoding
        );
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string|array> $start
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string|array> $end
     * @return list<string>
     */
    protected function betweenAll(
        HtmlTag|Newline|Regex|Stringable|string|array $start,
        HtmlTag|Newline|Regex|Stringable|string|array $end,
        bool $reversed = false,
        string $start_behavior = 'sequential',
        string $end_behavior = 'sequential'
    ): array {
        $start_sequence = self::normalizeSearchOptions($start, $start_behavior);
        $end_sequence = self::normalizeSearchOptions($end, $end_behavior);

        if ($this->value === '') {
            return [];
        }

        $results = [];
        $offset = 0;

        while (true) {
            $start_match = self::findSequence($this->value, $start_sequence, $offset);
            if ($start_match === null) {
                break;
            }

            $end_match = self::findSequence($this->value, $end_sequence, $start_match['end']);
            if ($end_match === null) {
                break;
            }

            if ($end_match['start'] < $start_match['end']) {
                $offset = $start_match['end'];
                continue;
            }

            $results[] = substr(
                $this->value,
                $start_match['end'],
                $end_match['start'] - $start_match['end']
            );

            $offset = $end_match['end'];
        }

        if ($reversed) {
            $results = array_reverse($results);
        }

        /** @var list<string> $results */
        $results = array_values($results);

        return $results;
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string|array> $search
     */
    protected function before(
        HtmlTag|Newline|Regex|Stringable|string|array $search,
        bool $last_occurence = false,
        int $skip = 0,
        string $start_behavior = 'sequential'
    ): self {
        if ($skip < 0) {
            throw new InvalidArgumentException('Skip must be greater than or equal to 0.');
        }

        $sequence = self::normalizeSearchOptions($search, $start_behavior);
        $occurrences = self::findAllSequences($this->value, $sequence);

        if ($occurrences === []) {
            return new self($this->value, $this->mode, $this->encoding);
        }

        $index = $last_occurence
            ? count($occurrences) - 1 - $skip
            : $skip;

        if ($index < 0 || $index >= count($occurrences)) {
            return new self($this->value, $this->mode, $this->encoding);
        }

        $match = $occurrences[$index];

        return new self(substr($this->value, 0, $match['start']), $this->mode, $this->encoding);
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string|array> $search
     */
    protected function after(
        HtmlTag|Newline|Regex|Stringable|string|array $search,
        bool $last_occurence = false,
        int $skip = 0,
        string $start_behavior = 'sequential'
    ): self {
        if ($skip < 0) {
            throw new InvalidArgumentException('Skip must be greater than or equal to 0.');
        }

        $sequence = self::normalizeSearchOptions($search, $start_behavior);
        $occurrences = self::findAllSequences($this->value, $sequence);

        if ($occurrences === []) {
            return new self($this->value, $this->mode, $this->encoding);
        }

        $index = $last_occurence
            ? count($occurrences) - 1 - $skip
            : $skip;

        if ($index < 0 || $index >= count($occurrences)) {
            return new self($this->value, $this->mode, $this->encoding);
        }

        $match = $occurrences[$index];

        return new self(substr($this->value, $match['end']), $this->mode, $this->encoding);
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $delimiter
     * @return list<string>
     */
    protected function split(HtmlTag|Newline|Regex|Stringable|string|array $delimiter, ?int $limit = null): array
    {
        if ($limit !== null && $limit < 1) {
            throw new InvalidArgumentException('Limit must be greater than or equal to 1 when provided.');
        }

        if ($this->value === '') {
            return [];
        }

        $delimiters = is_array($delimiter) ? $delimiter : [$delimiter];
        if ($delimiters === []) {
            throw new InvalidArgumentException('Delimiter list cannot be empty.');
        }

        $max_parts = $limit ?? PHP_INT_MAX;
        $parts = [];
        $offset = 0;

        while (count($parts) + 1 < $max_parts) {
            $match = self::findNextDelimiterMatch($this->value, $delimiters, $offset);
            if ($match === null) {
                break;
            }

            $parts[] = substr($this->value, $offset, $match['start'] - $offset);
            $offset = $match['end'];
        }

        $parts[] = substr($this->value, $offset);

        /** @var list<string> $parts */
        $parts = array_values($parts);

        return $parts;
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $delimiter
     * @return list<string>
     */
    protected function explode(HtmlTag|Newline|Regex|Stringable|string|array $delimiter, ?int $limit = null): array
    {
        return $this->split($delimiter, $limit);
    }

    /**
     * @return list<string>
     */
    protected function lines(bool $trim = false, ?int $limit = null): array
    {
        if ($limit !== null && $limit < 1) {
            throw new InvalidArgumentException('Limit must be greater than or equal to 1 when provided.');
        }

        if ($this->value === '') {
            return [];
        }

        $segments = self::withRegexErrorHandling(
            fn () => preg_split('/\r\n|\r|\n/', $this->value, $limit ?? -1)
        );

        if (!is_array($segments) || $segments === false) {
            $segments = [$this->value];
        }

        if ($segments === ['']) {
            return [];
        }

        if ($trim) {
            $segments = array_map(static fn (string $line): string => trim($line), $segments);
        }

        /** @var list<string> $segments */
        $segments = array_values($segments);

        return $segments;
    }

    protected function lineCount(): int
    {
        return count($this->lines());
    }

    /**
     * @return list<string>
     */
    protected function words(bool $trim = false, ?int $limit = null): array
    {
        if ($limit !== null && $limit < 1) {
            throw new InvalidArgumentException('Limit must be greater than or equal to 1 when provided.');
        }

        if ($this->value === '') {
            return [];
        }

        $segments = self::withRegexErrorHandling(
            fn () => preg_split('/[\s\p{Z}]+/u', $this->value, $limit ?? -1, PREG_SPLIT_NO_EMPTY)
        );

        if (!is_array($segments) || $segments === false) {
            $segments = preg_split('/\s+/', $this->value, $limit ?? -1, PREG_SPLIT_NO_EMPTY);
        }

        if (!is_array($segments) || $segments === false) {
            $segments = [];
        }

        if ($segments === []) {
            return [];
        }

        if ($trim) {
            $segments = array_map(
                static fn (string $word): string => trim($word, " \t\r\n\f\v\"'.,;:!?()[]{}<>-_")
                ,
                $segments
            );
            $segments = array_values(array_filter(
                $segments,
                static fn (string $word): bool => $word !== ''
            ));

            /** @var list<string> $segments */
            return $segments;
        }

        /** @var list<string> $segments */
        return array_values($segments);
    }

    protected function wordCount(): int
    {
        return count($this->words());
    }

    protected function sentenceCount(): int
    {
        $normalized = trim($this->value);
        if ($normalized === '') {
            return 0;
        }

        $boundaries = [];
        $matchCount = self::withRegexErrorHandling(
            static function () use ($normalized, &$boundaries) {
                return preg_match_all(
                    '/[.!?…]+(?:["\'\)\]\}»”’]+)?(?=\s+|\R|$)/u',
                    $normalized,
                    $boundaries,
                    PREG_OFFSET_CAPTURE
                );
            }
        );

        $entries = [];
        if (is_int($matchCount) && $matchCount > 0 && isset($boundaries[0]) && is_array($boundaries[0])) {
            /** @var list<array{0: string, 1: int}> $entries */
            $entries = $boundaries[0];
        }

        $sentenceCount = 0;
        $start = 0;
        $length = strlen($normalized);

        foreach ($entries as $entry) {
            [$punctuation, $offset] = $entry;
            if (!is_string($punctuation) || !is_int($offset)) {
                continue;
            }

            $end = $offset + strlen($punctuation);
            $fragment = substr($normalized, $start, $end - $start);
            if ($fragment === false || trim($fragment) === '') {
                continue;
            }

            if (self::fragmentEndsWithAbbreviation($fragment)) {
                continue;
            }

            $sentenceCount++;
            $start = $end;
        }

        if ($entries !== [] && $start < $length) {
            $tail = trim(substr($normalized, $start));
            if ($tail !== '') {
                $sentenceCount++;
            }
        }

        if ($sentenceCount === 0) {
            $lines = preg_split('/\R+/u', $normalized, -1, PREG_SPLIT_NO_EMPTY);
            if (is_array($lines) && $lines !== []) {
                return count($lines);
            }

            return 1;
        }

        return $sentenceCount;
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $input_delimiter
     */
    protected function toSnake(Newline|HtmlTag|Regex|string|array $input_delimiter = ' '): self
    {
        if ($this->value === '') {
            return new self('', $this->mode, $this->encoding);
        }

        $words = $this->extractCaseWords($input_delimiter);

        return new self(implode('_', $words), $this->mode, $this->encoding);
    }

    protected function toKebab(): self
    {
        if ($this->value === '') {
            return new self('', $this->mode, $this->encoding);
        }

        $snake = $this->toSnake([' ', '-', '_']);

        return new self(str_replace('_', '-', (string) $snake), $this->mode, $this->encoding);
    }

    protected function toCamel(bool $capitalize_first = false): self
    {
        if ($this->value === '') {
            return new self('', $this->mode, $this->encoding);
        }

        $snake = (string) $this->toSnake([' ', '-', '_']);
        if ($snake === '') {
            return new self('', $this->mode, $this->encoding);
        }

        $parts = array_values(array_filter(
            explode('_', $snake),
            static fn (string $part): bool => $part !== ''
        ));

        if ($parts === []) {
            return new self('', $this->mode, $this->encoding);
        }

        $first = array_shift($parts);
        $result = $capitalize_first
            ? self::uppercaseFirst($first, $this->encoding)
            : $first;

        foreach ($parts as $part) {
            $result .= self::uppercaseFirst($part, $this->encoding);
        }

        return new self($result, $this->mode, $this->encoding);
    }

    protected function toTitle(): self
    {
        if ($this->value === '') {
            return new self('', $this->mode, $this->encoding);
        }

        if (function_exists('mb_convert_case')) {
            $converted = mb_convert_case($this->value, MB_CASE_TITLE_SIMPLE, $this->encoding);
            $converted = preg_replace_callback(
                "/(?<=['’])\p{L}/u",
                function (array $match): string {
                    if (function_exists('mb_strtoupper')) {
                        return mb_strtoupper($match[0], $this->encoding);
                    }

                    return strtoupper($match[0]);
                },
                $converted
            ) ?? $converted;
        } else {
            $converted = ucwords(
                self::lowercaseString($this->value, $this->encoding),
                " \t\r\n\f\v-'_"
            );
        }

        return new self($converted, $this->mode, $this->encoding);
    }

    protected function toPascal(): self
    {
        return $this->toCamel(true);
    }

    /**
     * @param Regex|array<int, Regex> $pattern
     * @return self|null
     */
    protected function match(Regex|array $pattern, int $offset = 0): ?self
    {
        $patterns = is_array($pattern) ? $pattern : [$pattern];

        if ($patterns === []) {
            throw new InvalidArgumentException('Pattern array cannot be empty.');
        }

        if ($offset < 0) {
            throw new InvalidArgumentException('Offset must be greater than or equal to 0.');
        }

        $bestMatch = null;

        foreach ($patterns as $candidate) {
            if (!$candidate instanceof Regex) {
                throw new InvalidArgumentException('All patterns must be instances of Regex.');
            }

            $matches = [];
            $result = self::withRegexErrorHandling(
                function () use (&$matches, $candidate, $offset) {
                    return preg_match(
                        (string) $candidate,
                        $this->value,
                        $matches,
                        PREG_OFFSET_CAPTURE,
                        $offset
                    );
                }
            );

            if (is_int($result) && $result > 0 && isset($matches[0]) && is_array($matches[0])) {
                $matchOffset = $matches[0][1];

                if (!is_int($matchOffset)) {
                    continue;
                }

                if ($bestMatch === null || $matchOffset < $bestMatch['offset']) {
                    $bestMatch = [
                        'value' => $matches[0][0],
                        'offset' => $matchOffset,
                    ];
                }
            }
        }

        if ($bestMatch === null) {
            return null;
        }

        return new self($bestMatch['value'], $this->mode, $this->encoding);
    }

    protected function matchAll(
        Regex|array $pattern,
        false|int $limit = false,
        array|int|null $flags = PREG_PATTERN_ORDER
    ): array {
        $patterns = is_array($pattern) ? $pattern : [$pattern];
        if ($patterns === []) {
            throw new InvalidArgumentException('Pattern array cannot be empty.');
        }

        if ($limit !== false && $limit < 0) {
            throw new InvalidArgumentException('Limit must be greater than or equal to 0 or false.');
        }

        $normalized_flags = self::normalizeMatchAllFlags($flags);
        $use_set_order = ($normalized_flags & PREG_SET_ORDER) === PREG_SET_ORDER;

        if ($limit === 0) {
            return [];
        }

        $remaining = $limit === false ? null : $limit;

        $aggregate = $use_set_order ? [] : [];

        foreach ($patterns as $candidate) {
            if (!$candidate instanceof Regex) {
                throw new InvalidArgumentException('All patterns must be instances of Regex.');
            }

            if ($remaining !== null && $remaining === 0) {
                break;
            }

            $matches = [];
            $match_count = self::withRegexErrorHandling(
                function () use (&$matches, $candidate, $normalized_flags) {
                    return preg_match_all((string) $candidate, $this->value, $matches, $normalized_flags);
                }
            );

            if (!is_int($match_count) || $match_count === 0) {
                continue;
            }

            if ($remaining !== null && $match_count > $remaining) {
                if ($use_set_order) {
                    $matches = array_slice($matches, 0, $remaining);
                    $match_count = count($matches);
                } else {
                    foreach ($matches as &$group) {
                        if (is_array($group)) {
                            $group = array_slice($group, 0, $remaining);
                        }
                    }
                    unset($group);
                    $match_count = $remaining;
                }
            }

            if ($use_set_order) {
                $aggregate = array_merge($aggregate, $matches);
            } else {
                foreach ($matches as $key => $group) {
                    if (!array_key_exists($key, $aggregate)) {
                        $aggregate[$key] = [];
                    }

                    if (is_array($group)) {
                        $aggregate[$key] = array_merge($aggregate[$key], $group);
                    }
                }
            }

            if ($remaining !== null) {
                $remaining -= $match_count;
            }
        }

        if ($use_set_order) {
            return array_values($aggregate);
        }

        return $aggregate;
    }

    protected function trim(bool $newline = true, bool $space = true, bool $tab = true): self
    {
        return $this->trimInternal(true, true, $newline, $space, $tab);
    }

    protected function ltrim(bool $newline = true, bool $space = true, bool $tab = true): self
    {
        return $this->trimInternal(true, false, $newline, $space, $tab);
    }

    protected function rtrim(bool $newline = true, bool $space = true, bool $tab = true): self
    {
        return $this->trimInternal(false, true, $newline, $space, $tab);
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $search
     */
    protected function replace(
        HtmlTag|Newline|Regex|Stringable|string|array $search,
        HtmlTag|Newline|Regex|Stringable|string $replace,
        ?int $limit = null,
        bool $reversed = false
    ): self {
        if ($limit !== null && $limit < 0) {
            throw new InvalidArgumentException('Limit must be greater than or equal to 0.');
        }

        if ($limit === 0) {
            return new self($this->value, $this->mode, $this->encoding);
        }

        $replacement = self::normalizeFragment($replace);
        $remaining = $limit ?? PHP_INT_MAX;
        $result = $this->value;

        $search_values = is_array($search) ? $search : [$search];

        foreach ($search_values as $search_value) {
            if ($remaining === 0) {
                break;
            }

            if ($search_value instanceof Newline) {
                $constraint = $search_value->getLineConstraint();
                if ($constraint !== null) {
                    $result = self::replaceLinesMatchingConstraint(
                        $result,
                        $search_value,
                        $constraint,
                        $replacement,
                        $remaining,
                        $reversed
                    );

                    continue;
                }
            }

            if ($search_value instanceof Regex) {
                $result = self::replaceWithRegex(
                    $result,
                    (string) $search_value,
                    $replacement,
                    $remaining,
                    $reversed
                );

                continue;
            }

            if ($search_value instanceof HtmlTag) {
                $result = self::replaceHtmlTag(
                    $result,
                    $search_value,
                    $replacement,
                    $remaining,
                    $reversed
                );

                continue;
            }

            $normalized_search = self::normalizeFragment($search_value);
            if ($normalized_search === '') {
                throw new InvalidArgumentException('Search value cannot be empty.');
            }

            if ($reversed) {
                $result = self::replaceFromEnd($result, $normalized_search, $replacement, $remaining);
            } else {
                $result = self::replaceFromStart($result, $normalized_search, $replacement, $remaining);
            }
        }

        return new self($result, $this->mode, $this->encoding);
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $search
     */
    protected function replaceFirst(
        HtmlTag|Newline|Regex|Stringable|string|array $search,
        HtmlTag|Newline|Regex|Stringable|string $replace
    ): self {
        return $this->replace($search, $replace, 1);
    }

    protected function replaceLast(
        HtmlTag|Newline|Regex|Stringable|string|array $search,
        HtmlTag|Newline|Regex|Stringable|string $replace
    ): self {
        return $this->replace($search, $replace, 1, true);
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $search
     */
    protected function strip(
        HtmlTag|Newline|Regex|Stringable|string|array $search,
        ?int $limit = null,
        bool $reversed = false
    ): self {
        return $this->replace($search, '', $limit, $reversed);
    }

    protected function stripEmojis(): self
    {
        if ($this->value === '') {
            return new self('', $this->mode, $this->encoding);
        }

        $clusters = self::splitByMode($this->value, 'graphemes', $this->encoding);
        if ($clusters === []) {
            return new self('', $this->mode, $this->encoding);
        }

        $filtered = [];
        foreach ($clusters as $cluster) {
            if (!self::isEmojiCluster($cluster)) {
                $filtered[] = $cluster;
            }
        }

        return new self(implode('', $filtered), $this->mode, $this->encoding);
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $allowed_tags
     */
    protected function stripTags(HtmlTag|Newline|Regex|Stringable|string|array $allowed_tags = ''): self
    {
        $allowed = self::normalizeAllowedTags($allowed_tags);

        return new self(strip_tags($this->value, $allowed), $this->mode, $this->encoding);
    }

    protected function stripAccents(): self
    {
        if ($this->value === '') {
            return new self('', $this->mode, $this->encoding);
        }

        $value = $this->value;

        if (class_exists('Normalizer')) {
            $normalized = Normalizer::normalize($value, Normalizer::FORM_D);
            if (is_string($normalized)) {
                $value = $normalized;
            }
        }

        $value = preg_replace('/\p{Mn}+/u', '', $value);
        if (!is_string($value)) {
            $value = $this->value;
        }

        $value = self::stripDetachedAccentMarkers($value);
        $value = strtr($value, self::ASCII_FALLBACK_REPLACEMENTS);

        if (class_exists('Normalizer')) {
            $recomposed = Normalizer::normalize($value, Normalizer::FORM_C);
            if (is_string($recomposed)) {
                $value = $recomposed;
            }
        }

        return new self($value, $this->mode, $this->encoding);
    }

    protected function ensurePrefix(Newline|HtmlTag|string $prefix): self
    {
        $fragment = self::normalizeFragment($prefix);
        if ($fragment === '') {
            throw new InvalidArgumentException('Prefix cannot be empty.');
        }

        if ($this->startsWith($prefix)) {
            return new self($this->value, $this->mode, $this->encoding);
        }

        return new self($fragment . $this->value, $this->mode, $this->encoding);
    }

    protected function ensureSuffix(Newline|HtmlTag|string $suffix): self
    {
        $fragment = self::normalizeFragment($suffix);
        if ($fragment === '') {
            throw new InvalidArgumentException('Suffix cannot be empty.');
        }

        if ($this->endsWith($suffix)) {
            return new self($this->value, $this->mode, $this->encoding);
        }

        return new self($this->value . $fragment, $this->mode, $this->encoding);
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $prefix
     */
    protected function removePrefix(HtmlTag|Newline|Regex|Stringable|string|array $prefix): self
    {
        $candidates = is_array($prefix) ? $prefix : [$prefix];
        if ($candidates === []) {
            throw new InvalidArgumentException('Prefix candidates cannot be empty.');
        }

        foreach ($candidates as $candidate) {
            if (is_array($candidate)) {
                throw new InvalidArgumentException('Nested prefix arrays are not supported.');
            }

            $result = $this->removePrefixCandidate($candidate);
            if ($result !== null) {
                return new self($result, $this->mode, $this->encoding);
            }
        }

        return new self($this->value, $this->mode, $this->encoding);
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $suffix
     */
    protected function removeSuffix(HtmlTag|Newline|Regex|Stringable|string|array $suffix): self
    {
        $candidates = is_array($suffix) ? $suffix : [$suffix];
        if ($candidates === []) {
            throw new InvalidArgumentException('Suffix candidates cannot be empty.');
        }

        foreach ($candidates as $candidate) {
            if (is_array($candidate)) {
                throw new InvalidArgumentException('Nested suffix arrays are not supported.');
            }

            $result = $this->removeSuffixCandidate($candidate);
            if ($result !== null) {
                return new self($result, $this->mode, $this->encoding);
            }
        }

        return new self($this->value, $this->mode, $this->encoding);
    }

    protected function togglePrefix(Newline|HtmlTag|string $prefix): self
    {
        $fragment = self::normalizeFragment($prefix);
        if ($fragment === '') {
            throw new InvalidArgumentException('Prefix cannot be empty.');
        }

        if ($this->startsWith($prefix)) {
            return $this->removePrefix($prefix);
        }

        return $this->ensurePrefix($prefix);
    }

    protected function toggleSuffix(Newline|HtmlTag|string $suffix): self
    {
        $fragment = self::normalizeFragment($suffix);
        if ($fragment === '') {
            throw new InvalidArgumentException('Suffix cannot be empty.');
        }

        if ($this->endsWith($suffix)) {
            return $this->removeSuffix($suffix);
        }

        return $this->ensureSuffix($suffix);
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $prefix
     */
    protected function hasPrefix(HtmlTag|Newline|Regex|Stringable|string|array $prefix): bool
    {
        return $this->startsWith($prefix);
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $suffix
     */
    protected function hasSuffix(HtmlTag|Newline|Regex|Stringable|string|array $suffix): bool
    {
        return $this->endsWith($suffix);
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $search
     */
    protected function contains(HtmlTag|Newline|Regex|Stringable|string|array $search): bool
    {
        $candidates = is_array($search) ? $search : [$search];
        if ($candidates === []) {
            throw new InvalidArgumentException('Search candidates cannot be empty.');
        }

        foreach ($candidates as $candidate) {
            if (is_array($candidate)) {
                throw new InvalidArgumentException('Nested search arrays are not supported.');
            }

            if ($this->containsCandidate($candidate)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $search
     * @return false|int|array<int, int>
     */
    protected function indexOf(
        HtmlTag|Newline|Regex|Stringable|string|array $search,
        bool $reversed = false,
        int|bool $limit = 1,
        string $behavior = 'or'
    ): false|int|array {
        $normalized_behavior = self::normalizeSearchBehavior($behavior);
        $sequences = self::normalizeIndexOfOptions($search, $normalized_behavior);

        $normalized_limit = $limit === false ? 0 : (int) $limit;
        if ($normalized_limit < 0) {
            throw new InvalidArgumentException('Search limit must be greater than or equal to 0 or false.');
        }

        $matches = $this->collectSequenceMatches(
            $sequences,
            $reversed,
            $normalized_behavior === 'sequential'
        );
        if ($matches === []) {
            return false;
        }

        if ($normalized_limit === 1) {
            return $matches[0];
        }

        if ($limit === false || $normalized_limit === 0) {
            return $matches;
        }

        return array_slice($matches, 0, $normalized_limit);
    }

    /**
     * @return list<list<HtmlTag|Newline|Regex|string>>
     */
    private static function normalizeIndexOfOptions(
        HtmlTag|Newline|Regex|Stringable|string|array $search,
        string $behavior
    ): array {
        if ($behavior === 'or') {
            return self::normalizeIndexOfOrOptions($search);
        }

        return self::normalizeIndexOfSequentialOptions($search);
    }

    /**
     * @return list<list<HtmlTag|Newline|Regex|string>>
     */
    private static function normalizeIndexOfOrOptions(
        HtmlTag|Newline|Regex|Stringable|string|array $value
    ): array {
        $items = is_array($value) ? $value : [$value];
        if ($items === []) {
            throw new InvalidArgumentException('Search candidates cannot be empty.');
        }

        $options = [];

        foreach ($items as $item) {
            $sequence_items = is_array($item) ? $item : [$item];
            if ($sequence_items === []) {
                throw new InvalidArgumentException('Search sequence cannot be empty.');
            }

            $sequence = [];

            foreach ($sequence_items as $fragment) {
                if (is_array($fragment)) {
                    throw new InvalidArgumentException('Nested search arrays are not supported.');
                }

                $sequence[] = self::normalizeIndexOfFragment($fragment);
            }

            $options[] = $sequence;
        }

        /** @var list<list<HtmlTag|Newline|Regex|string>> $options */
        return $options;
    }

    /**
     * @return list<list<HtmlTag|Newline|Regex|string>>
     */
    private static function normalizeIndexOfSequentialOptions(
        HtmlTag|Newline|Regex|Stringable|string|array $value
    ): array {
        if (!is_array($value)) {
            return [[self::normalizeIndexOfFragment($value)]];
        }

        if ($value === []) {
            throw new InvalidArgumentException('Search sequence cannot be empty.');
        }

        $has_nested_arrays = false;
        foreach ($value as $item) {
            if (is_array($item)) {
                $has_nested_arrays = true;
                break;
            }
        }

        if (!$has_nested_arrays) {
            $sequence = [];
            foreach ($value as $fragment) {
                $sequence[] = self::normalizeIndexOfFragment($fragment);
            }

            return [$sequence];
        }

        return self::normalizeIndexOfOrOptions($value);
    }

    private static function normalizeIndexOfFragment(
        HtmlTag|Newline|Regex|Stringable|string $fragment
    ): HtmlTag|Newline|Regex|string {
        if ($fragment instanceof HtmlTag || $fragment instanceof Regex || $fragment instanceof Newline) {
            return $fragment;
        }

        $normalized = self::normalizeFragment($fragment);
        if ($normalized === '') {
            throw new InvalidArgumentException('Search value cannot be empty.');
        }

        return $normalized;
    }

    /**
     * @param list<list<HtmlTag|Newline|Regex|string>> $sequences
     * @return list<int>
     */
    private function collectSequenceMatches(array $sequences, bool $reversed, bool $use_sequence_terminal_offset): array
    {
        $offsets = [];
        $subject_length = strlen($this->value);

        foreach ($sequences as $sequence) {
            $offset = 0;

            while ($offset <= $subject_length) {
                $match = $this->findNextSequenceOccurrence($sequence, $offset);
                if ($match === null) {
                    break;
                }

                $offsets[] = $use_sequence_terminal_offset ? $match['last'] : $match['start'];

                $next_offset = $match['start'] + 1;
                if ($next_offset <= $offset) {
                    $next_offset = $offset + 1;
                }

                $offset = $next_offset;
            }
        }

        if ($offsets === []) {
            return [];
        }

        sort($offsets);
        $offsets = array_values(array_unique($offsets));

        if ($reversed) {
            $offsets = array_reverse($offsets);
        }

        return array_map(fn (int $offset): int => $this->convertOffsetToIndex($offset), $offsets);
    }

    /**
     * @param list<HtmlTag|Newline|Regex|string> $sequence
     * @return array{start: int, end: int, last: int}|null
     */
    private function findNextSequenceOccurrence(array $sequence, int $offset): ?array
    {
        $current_offset = max(0, $offset);
        $start = null;
        $last_fragment_start = null;

        foreach ($sequence as $fragment) {
            $match = $this->findNextFragmentMatch($fragment, $current_offset);
            if ($match === null) {
                return null;
            }

            if ($start === null) {
                $start = $match['start'];
            }

            $last_fragment_start = $match['start'];
            $current_offset = $match['end'];
        }

        if ($start === null || $last_fragment_start === null) {
            return null;
        }

        return [
            'start' => $start,
            'end' => $current_offset,
            'last' => $last_fragment_start,
        ];
    }

    /**
     * @return array{start: int, end: int}|null
     */
    private function findNextFragmentMatch(HtmlTag|Newline|Regex|string $fragment, int $offset): ?array
    {
        $subject = $this->value;
        $cursor = max(0, $offset);

        if ($fragment instanceof HtmlTag) {
            $match = self::findNextHtmlTagMatch($subject, $fragment, $cursor);
            if ($match === null || !isset($match['offset'], $match['length'])) {
                return null;
            }

            $start = (int) $match['offset'];
            $length = (int) $match['length'];

            return [
                'start' => $start,
                'end' => $start + $length,
            ];
        }

        if ($fragment instanceof Regex) {
            $pattern = (string) $fragment;
            $matches = [];

            $result = self::withRegexErrorHandling(
                static function () use ($pattern, $subject, $cursor, &$matches) {
                    return preg_match($pattern, $subject, $matches, PREG_OFFSET_CAPTURE, $cursor);
                }
            );

            if (!is_int($result) || $result === 0) {
                return null;
            }

            if (!isset($matches[0]) || !is_array($matches[0]) || !isset($matches[0][0], $matches[0][1]) || !is_int($matches[0][1])) {
                return null;
            }

            $match_string = (string) $matches[0][0];
            $start = (int) $matches[0][1];

            return [
                'start' => $start,
                'end' => $start + strlen($match_string),
            ];
        }

        if ($fragment instanceof Newline) {
            $best_match = null;

            foreach ($this->newlineFragments($fragment) as $line_break) {
                $position = strpos($subject, $line_break, $cursor);
                if ($position === false) {
                    continue;
                }

                $candidate = [
                    'start' => (int) $position,
                    'end' => (int) $position + strlen($line_break),
                ];

                if (
                    $best_match === null
                    || $candidate['start'] < $best_match['start']
                    || ($candidate['start'] === $best_match['start'] && $candidate['end'] > $best_match['end'])
                ) {
                    $best_match = $candidate;
                }
            }

            return $best_match;
        }

        $fragment_value = (string) $fragment;
        $position = strpos($subject, $fragment_value, $cursor);
        if ($position === false) {
            return null;
        }

        $start = (int) $position;

        return [
            'start' => $start,
            'end' => $start + strlen($fragment_value),
        ];
    }

    protected function isEmpty(bool $newline = true, bool $space = true, bool $tab = true): bool
    {
        if ($this->value === '') {
            return true;
        }

        if (!$newline && !$space && !$tab) {
            return $this->value === '';
        }

        $mask = self::buildTrimMask($newline, $space, $tab);
        if ($mask === '') {
            return $this->value === '';
        }

        return trim($this->value, $mask) === '';
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $search
     */
    protected function startsWith(HtmlTag|Newline|Regex|Stringable|string|array $search): bool
    {
        $candidates = is_array($search) ? $search : [$search];
        if ($candidates === []) {
            throw new InvalidArgumentException('Search candidates cannot be empty.');
        }

        foreach ($candidates as $candidate) {
            if (is_array($candidate)) {
                throw new InvalidArgumentException('Nested search arrays are not supported.');
            }

            if ($this->startsWithCandidate($candidate)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $search
     */
    protected function endsWith(HtmlTag|Newline|Regex|Stringable|string|array $search): bool
    {
        $candidates = is_array($search) ? $search : [$search];
        if ($candidates === []) {
            throw new InvalidArgumentException('Search candidates cannot be empty.');
        }

        foreach ($candidates as $candidate) {
            if (is_array($candidate)) {
                throw new InvalidArgumentException('Nested search arrays are not supported.');
            }

            if ($this->endsWithCandidate($candidate)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $string
     */
    protected function equals(HtmlTag|Newline|Regex|Stringable|string|array $string, bool $case_sensitive = true): bool
    {
        $candidates = is_array($string) ? $string : [$string];
        if ($candidates === []) {
            throw new InvalidArgumentException('Comparison candidates cannot be empty.');
        }

        foreach ($candidates as $candidate) {
            if (is_array($candidate)) {
                throw new InvalidArgumentException('Nested comparison arrays are not supported.');
            }

            if ($this->equalsCandidate($candidate, $case_sensitive)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $search
     */
    protected function countOccurrences(HtmlTag|Newline|Regex|Stringable|string|array $search): int
    {
        $candidates = is_array($search) ? $search : [$search];
        if ($candidates === []) {
            throw new InvalidArgumentException('Search candidates cannot be empty.');
        }

        $count = 0;

        foreach ($candidates as $candidate) {
            if (is_array($candidate)) {
                throw new InvalidArgumentException('Nested search arrays are not supported.');
            }

            $count += $this->countCandidateOccurrences($candidate);
        }

        return $count;
    }

    protected function similarityScore(
        HtmlTag|Newline|Regex|Stringable|string|array $comparison,
        string $algorithm = 'github-style',
        array $options = []
    ): float {
        $normalized_algorithm = strtolower(trim($algorithm));
        if ($normalized_algorithm === '') {
            $normalized_algorithm = 'github-style';
        }

        $comparison_value = is_array($comparison)
            ? self::concatenateFragments($comparison)
            : self::normalizeFragment($comparison);

        return Similarity::compute(
            $this->value,
            $comparison_value,
            $normalized_algorithm,
            $options,
            $this->mode,
            $this->encoding
        );
    }

    protected function transliterate(string $to = 'ASCII//TRANSLIT'): self
    {
        $target = trim($to);
        if ($target === '') {
            throw new InvalidArgumentException('Target transliteration cannot be empty.');
        }

        if (class_exists('\\Transliterator')) {
            $transliterator = \Transliterator::create($target);
            if ($transliterator instanceof \Transliterator) {
                $transliterated = $transliterator->transliterate($this->value);
                if ($transliterated === null) {
                    throw new RuntimeException(sprintf('Unable to transliterate string using "%s".', $target));
                }

                if (stripos($target, 'TRANSLIT') !== false) {
                    $transliterated = self::stripDetachedAccentMarkers($transliterated);
                }

                if (self::baseEncoding($target) === 'ASCII') {
                    $transliterated = self::applyAsciiTransliterationFallback(
                        $transliterated,
                        $this->value,
                        $this->encoding,
                    );
                }

                return new self($transliterated, $this->mode, $this->encoding);
            }

            if (strpos($target, '//') === false) {
                throw new InvalidArgumentException(sprintf('Unknown transliterator identifier "%s".', $target));
            }
        } elseif (strpos($target, '//') === false) {
            throw new RuntimeException('The intl extension is required to use transliterator identifiers.');
        }

        if (!function_exists('iconv')) {
            throw new RuntimeException('The iconv extension is required for transliteration.');
        }

        $converted = @iconv($this->encoding, $target, $this->value);
        if ($converted === false) {
            throw new RuntimeException(sprintf('Unable to transliterate string from %s to %s.', $this->encoding, $target));
        }

        if (stripos($target, 'TRANSLIT') !== false) {
            $converted = self::stripDetachedAccentMarkers($converted);
        }

        if (self::baseEncoding($target) === 'ASCII') {
            $converted = self::applyAsciiTransliterationFallback($converted, $this->value, $this->encoding);
        }

        return new self($converted, $this->mode, self::baseEncoding($target));
    }

    protected function toEncoding(string $to_encoding, ?string $from_encoding = null): self
    {
        $normalized_target = self::normalizeEncoding($to_encoding);
        $target_base = self::baseEncoding($normalized_target);

        $normalized_source = $from_encoding !== null
            ? self::normalizeEncoding($from_encoding)
            : ($this->detectEncoding([$this->encoding, $target_base, 'UTF-8', 'ISO-8859-1', 'ASCII'])
                ?: $this->encoding);

        $converted = self::convertEncoding($this->value, $normalized_target, $normalized_source);

        if (stripos($normalized_target, 'TRANSLIT') !== false) {
            $converted = self::stripDetachedAccentMarkers($converted);
        }

        if ($target_base === 'ASCII') {
            $converted = self::applyAsciiTransliterationFallback($converted, $this->value, $normalized_source);
        }

        return new self($converted, $this->mode, $target_base);
    }

    protected function detectEncoding(array $encodings = ['UTF-8', 'ISO-8859-1', 'ASCII']): string|false
    {
        if ($encodings === []) {
            throw new InvalidArgumentException('Encoding list cannot be empty.');
        }

        $normalized = [];
        foreach ($encodings as $encoding) {
            $normalized[self::normalizeEncoding($encoding)] = true;
        }

        $candidates = array_keys($normalized);

        if (function_exists('mb_detect_encoding')) {
            try {
                $detected = mb_detect_encoding($this->value, $candidates, true);
            } catch (ValueError) {
                $detected = false;
            }

            if (is_string($detected)) {
                return $detected;
            }
        }

        if (function_exists('iconv')) {
            foreach ($candidates as $candidate) {
                $result = @iconv($candidate, $candidate, $this->value);
                if ($result !== false) {
                    return $candidate;
                }
            }
        }

        return false;
    }

    protected function isValidEncoding(?string $encoding = null): bool
    {
        $target = $encoding !== null
            ? self::normalizeEncoding($encoding)
            : $this->encoding;

        if (function_exists('mb_check_encoding')) {
            return @mb_check_encoding($this->value, $target);
        }

        if (function_exists('iconv')) {
            return @iconv($target, $target, $this->value) !== false;
        }

        if (strcasecmp($target, 'UTF-8') === 0) {
            return preg_match('//u', $this->value) === 1;
        }

        return true;
    }

    protected function isAscii(): bool
    {
        if ($this->value === '') {
            return true;
        }

        if (function_exists('mb_check_encoding')) {
            return @mb_check_encoding($this->value, 'ASCII');
        }

        return preg_match('/^[\x00-\x7F]*$/', $this->value) === 1;
    }

    protected function isUtf8(): bool
    {
        if ($this->value === '') {
            return true;
        }

        if (function_exists('mb_check_encoding')) {
            return @mb_check_encoding($this->value, 'UTF-8');
        }

        return preg_match('//u', $this->value) === 1;
    }

    protected function toUtf8(?string $from_encoding = null): self
    {
        $source = $from_encoding !== null
            ? self::normalizeEncoding($from_encoding)
            : ($this->detectEncoding([$this->encoding, 'UTF-8', 'ISO-8859-1', 'ASCII']) ?: $this->encoding);

        $converted = self::convertEncoding($this->value, 'UTF-8', $source);

        return new self($converted, $this->mode, 'UTF-8');
    }

    protected function toAscii(?string $from_encoding = null): self
    {
        $source = $from_encoding !== null
            ? self::normalizeEncoding($from_encoding)
            : ($this->detectEncoding([$this->encoding, 'UTF-8', 'ISO-8859-1', 'ASCII']) ?: $this->encoding);

        $converted = self::convertEncoding($this->value, 'ASCII//TRANSLIT', $source);
        $converted = self::stripDetachedAccentMarkers($converted);
        $converted = self::applyAsciiTransliterationFallback($converted, $this->value, $source);

        return new self($converted, $this->mode, 'ASCII');
    }

    protected function encodeHtmlEntities(
        int $flags = ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401,
        ?string $encoding = null,
        bool $double_encode = false
    ): self {
        $target_encoding = $encoding !== null
            ? self::normalizeEncoding($encoding)
            : $this->encoding;

        $value = $target_encoding === $this->encoding
            ? $this->value
            : self::convertEncoding($this->value, $target_encoding, $this->encoding);

        $encoded = htmlentities($value, $flags, $target_encoding, $double_encode);

        if ($target_encoding !== $this->encoding) {
            $encoded = self::convertEncoding($encoded, $this->encoding, $target_encoding);
        }

        return new self($encoded, $this->mode, $this->encoding);
    }

    protected function decodeHtmlEntities(int $flags = ENT_QUOTES | ENT_HTML401, ?string $encoding = null): self
    {
        $target_encoding = $encoding !== null
            ? self::normalizeEncoding($encoding)
            : $this->encoding;

        if ($target_encoding === $this->encoding) {
            $decoded = html_entity_decode($this->value, $flags, $target_encoding);

            return new self($decoded, $this->mode, $this->encoding);
        }

        $converted = self::convertEncoding($this->value, $target_encoding, $this->encoding);
        $decoded = html_entity_decode($converted, $flags, $target_encoding);
        $decoded = self::convertEncoding($decoded, $this->encoding, $target_encoding);

        return new self($decoded, $this->mode, $this->encoding);
    }

    protected function toInt(): int
    {
        $normalized = str_replace('_', '', trim($this->value));

        if ($normalized === '') {
            throw new InvalidValueConversionException('Cannot convert an empty string to an integer.');
        }

        if (preg_match('/^[+-]?\d+$/', $normalized) === 1) {
            $sign = 1;
            if ($normalized[0] === '+') {
                $digits = substr($normalized, 1);
            } elseif ($normalized[0] === '-') {
                $sign = -1;
                $digits = substr($normalized, 1);
            } else {
                $digits = $normalized;
            }

            $digits = ltrim($digits, '0');
            if ($digits === '') {
                $digits = '0';
            }

            if ($sign === 1) {
                $max_digits = (string) PHP_INT_MAX;
                if (strlen($digits) > strlen($max_digits) || (strlen($digits) === strlen($max_digits) && strcmp($digits, $max_digits) > 0)) {
                    throw new InvalidValueConversionException('Integer value exceeds the maximum supported range.');
                }
            } else {
                $min_digits = ltrim((string) PHP_INT_MIN, '-');
                if (strlen($digits) > strlen($min_digits) || (strlen($digits) === strlen($min_digits) && strcmp($digits, $min_digits) > 0)) {
                    throw new InvalidValueConversionException('Integer value exceeds the minimum supported range.');
                }
            }

            return (int) $normalized;
        }

        if (!is_numeric($normalized)) {
            throw new InvalidValueConversionException('Value is not a valid integer representation.');
        }

        $float_value = (float) $normalized;
        if (!is_finite($float_value)) {
            throw new InvalidValueConversionException('Float conversion resulted in a non-finite value.');
        }

        if ($float_value > PHP_INT_MAX || $float_value < PHP_INT_MIN) {
            throw new InvalidValueConversionException('Integer value exceeds the supported range.');
        }

        return (int) $float_value;
    }

    protected function toFloat(): float
    {
        $normalized = str_replace('_', '', trim($this->value));

        if ($normalized === '') {
            throw new InvalidValueConversionException('Cannot convert an empty string to a float.');
        }

        if (!is_numeric($normalized)) {
            throw new InvalidValueConversionException('Value is not a valid floating point representation.');
        }

        $float_value = (float) $normalized;
        if (!is_finite($float_value)) {
            throw new InvalidValueConversionException('Float conversion resulted in a non-finite value.');
        }

        return $float_value;
    }

    protected function toBool(): bool
    {
        $normalized = trim($this->value);

        if ($normalized === '') {
            return false;
        }

        $lower = strtolower($normalized);

        $true_values = [
            '1',
            'true',
            't',
            'yes',
            'y',
            'yeah',
            'yup',
            'ok',
            'okay',
            'on',
            'enable',
            'enabled',
            'active',
            'pass',
            'passed',
            'success',
        ];

        $false_values = [
            '0',
            'false',
            'f',
            'no',
            'n',
            'off',
            'disable',
            'disabled',
            'inactive',
            'fail',
            'failed',
            'ko',
            'null',
            'none',
            '-1',
        ];

        if (in_array($lower, $true_values, true)) {
            return true;
        }

        if (in_array($lower, $false_values, true)) {
            return false;
        }

        $numeric_candidate = str_replace('_', '', $normalized);
        if (is_numeric($numeric_candidate)) {
            return (float) $numeric_candidate > 0.0;
        }

        throw new InvalidValueConversionException('Value cannot be interpreted as a boolean.');
    }

    protected function base64Encode(): self
    {
        return new self(base64_encode($this->value), $this->mode, $this->encoding);
    }

    protected function base64Decode(): self
    {
        if ($this->value === '') {
            return new self('', $this->mode, $this->encoding);
        }

        $normalized = preg_replace('/\s+/', '', $this->value);
        if ($normalized === null) {
            $normalized = $this->value;
        }

        $decoded = base64_decode($normalized, true);
        if ($decoded === false) {
            throw new InvalidArgumentException('The string is not valid Base64 encoded data.');
        }

        return new self($decoded, $this->mode, $this->encoding);
    }

    protected function md5(bool $raw_output = false): self
    {
        return new self(md5($this->value, $raw_output), $this->mode, $this->encoding);
    }

    protected function crc32(bool $raw_output = false): self
    {
        return new self(hash('crc32b', $this->value, $raw_output), $this->mode, $this->encoding);
    }

    protected function sha1(bool $raw_output = false): self
    {
        return new self(sha1($this->value, $raw_output), $this->mode, $this->encoding);
    }

    protected function sha256(bool $raw_output = false): self
    {
        return new self(hash('sha256', $this->value, $raw_output), $this->mode, $this->encoding);
    }

    protected function crypt(string $salt): self
    {
        if ($salt === '') {
            throw new InvalidArgumentException('Salt must be provided for crypt().');
        }

        $hash = @crypt($this->value, $salt);
        if ($hash === false || $hash === '' || $hash === '*0' || $hash === '*1') {
            throw new RuntimeException('Unable to generate hash using crypt().');
        }

        return new self($hash, $this->mode, $this->encoding);
    }

    protected function passwordHash(int|string $algo = PASSWORD_BCRYPT, array $options = []): self
    {
        $hash = password_hash($this->value, $algo, $options);
        if ($hash === false) {
            throw new RuntimeException('password_hash() failed to generate a hash.');
        }

        return new self($hash, $this->mode, $this->encoding);
    }

    protected function passwordVerify(string $hash): bool
    {
        return password_verify($this->value, $hash);
    }

    protected function encrypt(string $password, string $cipher = 'sodium_xchacha20'): self
    {
        $algorithm = $this->resolveEncryptionAlgorithm($cipher);
        $salt = random_bytes(self::ENCRYPTION_SALT_BYTES);

        if ($algorithm === 'sodium_xchacha20') {
            if (!self::sodiumAeadAvailable()) {
                throw new RuntimeException('libsodium support is required to encrypt using sodium_xchacha20.');
            }

            $nonceLength = self::sodiumNonceLength();
            $tagLength = self::sodiumTagLength();
            $nonce = random_bytes($nonceLength);
            $aad = pack('CC', self::ENCRYPTION_VERSION, self::ENCRYPTION_ALGORITHM_IDS[$algorithm]);
            $key = $this->deriveKey($password, $salt, true);

            $ciphertextWithTag = sodium_crypto_aead_xchacha20poly1305_ietf_encrypt(
                $this->value,
                $aad,
                $nonce,
                $key
            );

            $tag = substr($ciphertextWithTag, -$tagLength);
            $ciphertext = substr($ciphertextWithTag, 0, -$tagLength);
        } else {
            $nonceLength = self::opensslNonceLength($algorithm);
            $nonce = random_bytes($nonceLength);
            $aad = pack('CC', self::ENCRYPTION_VERSION, self::ENCRYPTION_ALGORITHM_IDS[$algorithm]);
            $key = $this->deriveKey($password, $salt, self::sodiumPwhashAvailable());

            $tag = '';
            $ciphertext = openssl_encrypt(
                $this->value,
                $algorithm,
                $key,
                OPENSSL_RAW_DATA,
                $nonce,
                $tag,
                $aad
            );

            if ($ciphertext === false) {
                $this->wipeSensitiveValue($key);

                throw new RuntimeException('Encryption failed using AES-256-GCM.');
            }

            $tagLength = strlen($tag);
        }

        $header = pack(
            'CCCCC',
            self::ENCRYPTION_VERSION,
            self::ENCRYPTION_ALGORITHM_IDS[$algorithm],
            strlen($salt),
            $nonceLength,
            $tagLength
        );

        $envelope = $header . $salt . $nonce . $tag . $ciphertext;

        $this->wipeSensitiveValue($key);

        return new self(base64_encode($envelope), $this->mode, $this->encoding);
    }

    protected function decrypt(string $password, string $cipher = 'sodium_xchacha20'): self
    {
        $this->validateCipherName($cipher);

        $binary = base64_decode($this->value, true);
        if ($binary === false) {
            throw new InvalidArgumentException('Encrypted payload is not valid Base64 data.');
        }

        if (strlen($binary) < self::ENCRYPTION_HEADER_LENGTH) {
            throw new InvalidArgumentException('Encrypted payload is malformed.');
        }

        $header = unpack('Cversion/Calgo/CsaltLen/CnonceLen/CtagLen', substr($binary, 0, self::ENCRYPTION_HEADER_LENGTH));
        if (!is_array($header)) {
            throw new InvalidArgumentException('Unable to parse encrypted payload header.');
        }

        if ($header['version'] !== self::ENCRYPTION_VERSION) {
            throw new RuntimeException('Unsupported encryption envelope version.');
        }

        $algorithm = $this->algorithmFromId($header['algo']);

        $offset = self::ENCRYPTION_HEADER_LENGTH;
        $salt = substr($binary, $offset, $header['saltLen']);
        $offset += $header['saltLen'];
        $nonce = substr($binary, $offset, $header['nonceLen']);
        $offset += $header['nonceLen'];
        $tag = substr($binary, $offset, $header['tagLen']);
        $offset += $header['tagLen'];
        $ciphertext = substr($binary, $offset);

        if (strlen($salt) !== $header['saltLen'] || strlen($nonce) !== $header['nonceLen'] || strlen($tag) !== $header['tagLen']) {
            throw new InvalidArgumentException('Encrypted payload lengths are inconsistent.');
        }

        $aad = pack('CC', self::ENCRYPTION_VERSION, self::ENCRYPTION_ALGORITHM_IDS[$algorithm]);
        $deriveWithSodium = $algorithm === 'sodium_xchacha20'
            || self::sodiumPwhashAvailable();
        $key = $this->deriveKey($password, $salt, $deriveWithSodium);

        if ($algorithm === 'sodium_xchacha20') {
            if (!self::sodiumAeadAvailable()) {
                $this->wipeSensitiveValue($key);

            throw new RuntimeException('libsodium support is required to decrypt sodium_xchacha20 payloads.');
            }

            $plaintext = sodium_crypto_aead_xchacha20poly1305_ietf_decrypt(
                $ciphertext . $tag,
                $aad,
                $nonce,
                $key
            );

            if ($plaintext === false) {
                $this->wipeSensitiveValue($key);

                throw new RuntimeException('Decryption failed: authentication tag mismatch or corrupted data.');
            }
        } else {
            if (!self::opensslAvailable()) {
                $this->wipeSensitiveValue($key);

                throw new RuntimeException('AES-256-GCM support via OpenSSL was not detected.');
            }

            $plaintext = openssl_decrypt(
                $ciphertext,
                'aes-256-gcm',
                $key,
                OPENSSL_RAW_DATA,
                $nonce,
                $tag,
                $aad
            );

            if ($plaintext === false) {
                $this->wipeSensitiveValue($key);

                throw new RuntimeException('Decryption failed: authentication tag mismatch or corrupted data.');
            }
        }

        $this->wipeSensitiveValue($key);

        return new self($plaintext, $this->mode, $this->encoding);
    }

    protected function htmlEscape(int $flags = ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, string $encoding = 'UTF-8'): self
    {
        $error = null;
        set_error_handler(static function (int $severity, string $message) use (&$error): bool {
            if (($severity & (E_WARNING | E_NOTICE | E_USER_WARNING | E_USER_NOTICE)) !== 0) {
                $error = new ValueError($message);

                return true;
            }

            return false;
        });

        try {
            $escaped = htmlspecialchars($this->value, $flags, $encoding, false);
        } finally {
            restore_error_handler();
        }

        if ($error instanceof ValueError) {
            throw $error;
        }

        return new self($escaped, $this->mode, $encoding);
    }

    protected function htmlUnescape(): self
    {
        $unescaped = html_entity_decode($this->value, ENT_QUOTES | ENT_HTML5, $this->encoding);

        return new self($unescaped, $this->mode, $this->encoding);
    }

    protected function urlEncode(bool $raw = false): self
    {
        $encoded = $raw ? rawurlencode($this->value) : urlencode($this->value);

        return new self($encoded, $this->mode, $this->encoding);
    }

    protected function urlDecode(bool $raw = false): self
    {
        $decoded = $raw ? rawurldecode($this->value) : urldecode($this->value);

        return new self($decoded, $this->mode, $this->encoding);
    }

    protected function nl2br(bool $is_xhtml = true): self
    {
        if ($this->value === '') {
            return new self('', $this->mode, $this->encoding);
        }

        $converted = nl2br($this->value, $is_xhtml);

        return new self($converted, $this->mode, $this->encoding);
    }

    protected function br2nl(): self
    {
        if ($this->value === '') {
            return new self('', $this->mode, $this->encoding);
        }

        $converted = preg_replace('/<br\s*\/?>/i', PHP_EOL, $this->value);
        if ($converted === null) {
            $converted = $this->value;
        }

        return new self($converted, $this->mode, $this->encoding);
    }

    private function resolveEncryptionAlgorithm(string $cipher): string
    {
        $this->validateCipherName($cipher);
        $normalized = strtolower($cipher);

        if ($normalized === 'sodium_xchacha20') {
            if (!self::sodiumAeadAvailable()) {
                throw new RuntimeException('libsodium support is required for sodium_xchacha20 encryption.');
            }

            return 'sodium_xchacha20';
        }

        if (!self::opensslAvailable()) {
            throw new RuntimeException('AES-256-GCM support via OpenSSL was not detected.');
        }

        return 'aes-256-gcm';
    }

    private function validateCipherName(string $cipher): void
    {
        $normalized = strtolower($cipher);
        if (!isset(self::ENCRYPTION_ALGORITHM_IDS[$normalized])) {
            throw new InvalidArgumentException(sprintf('Unsupported cipher "%s".', $cipher));
        }
    }

    private function algorithmFromId(int $id): string
    {
        $algorithm = array_search($id, self::ENCRYPTION_ALGORITHM_IDS, true);
        if ($algorithm === false) {
            throw new RuntimeException('Unsupported encryption algorithm identifier in payload.');
        }

        return $algorithm;
    }

    private static function sodiumAeadAvailable(): bool
    {
        return function_exists('sodium_crypto_aead_xchacha20poly1305_ietf_encrypt')
            && function_exists('sodium_crypto_aead_xchacha20poly1305_ietf_decrypt');
    }

    private static function sodiumPwhashAvailable(): bool
    {
        return function_exists('sodium_crypto_pwhash');
    }

    private static function sodiumNonceLength(): int
    {
        return (int) (defined('SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES')
            ? constant('SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES')
            : 24);
    }

    private static function sodiumTagLength(): int
    {
        return (int) (defined('SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_ABYTES')
            ? constant('SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_ABYTES')
            : 16);
    }

    private static function opensslAvailable(): bool
    {
        if (!function_exists('openssl_encrypt') || !function_exists('openssl_decrypt')) {
            return false;
        }

        $methods = openssl_get_cipher_methods();
        if (!is_array($methods)) {
            return false;
        }

        $methods = array_map('strtolower', $methods);

        return in_array('aes-256-gcm', $methods, true);
    }

    private static function opensslNonceLength(string $cipher): int
    {
        if (!self::opensslAvailable()) {
            throw new RuntimeException('AES-256-GCM support via OpenSSL was not detected.');
        }

        $length = openssl_cipher_iv_length($cipher);
        if (!is_int($length) || $length <= 0) {
            throw new RuntimeException(sprintf('Unable to determine IV length for cipher "%s".', $cipher));
        }

        return $length;
    }

    private function deriveKey(string $password, string $salt, bool $preferSodium): string
    {
        if ($preferSodium && self::sodiumPwhashAvailable()) {
            $opslimit = defined('SODIUM_CRYPTO_PWHASH_OPSLIMIT_MODERATE')
                ? (int) constant('SODIUM_CRYPTO_PWHASH_OPSLIMIT_MODERATE')
                : 4;
            $memlimit = defined('SODIUM_CRYPTO_PWHASH_MEMLIMIT_MODERATE')
                ? (int) constant('SODIUM_CRYPTO_PWHASH_MEMLIMIT_MODERATE')
                : 1 << 25;
            $algorithm = defined('SODIUM_CRYPTO_PWHASH_ALG_ARGON2ID13')
                ? (int) constant('SODIUM_CRYPTO_PWHASH_ALG_ARGON2ID13')
                : (defined('SODIUM_CRYPTO_PWHASH_ALG_DEFAULT')
                    ? (int) constant('SODIUM_CRYPTO_PWHASH_ALG_DEFAULT')
                    : 2);

            try {
                return sodium_crypto_pwhash(
                    self::ENCRYPTION_KEY_BYTES,
                    $password,
                    $salt,
                    $opslimit,
                    $memlimit,
                    $algorithm
                );
            } catch (Throwable $exception) {
                if (!$this->isSodiumNotImplemented($exception)) {
                    throw $exception;
                }
            }
        }

        return $this->deriveKeyWithPbkdf2($password, $salt);
    }

    private function deriveKeyWithPbkdf2(string $password, string $salt): string
    {
        return hash_pbkdf2('sha256', $password, $salt, self::PBKDF2_ITERATIONS, self::ENCRYPTION_KEY_BYTES, true);
    }

    private function isSodiumNotImplemented(Throwable $exception): bool
    {
        if ($exception instanceof SodiumException) {
            return stripos($exception->getMessage(), 'not implemented') !== false;
        }

        return false;
    }

    private function wipeSensitiveValue(string &$value): void
    {
        if ($value === '') {
            return;
        }

        if (function_exists('sodium_memzero')) {
            try {
                sodium_memzero($value);

                return;
            } catch (Throwable $exception) {
                if ($this->isSodiumNotImplemented($exception)) {
                    $value = str_repeat("\0", strlen($value));

                    return;
                }

                throw $exception;
            }
        }

        $value = str_repeat("\0", strlen($value));
    }

    protected function toUpper(): self
    {
        $upper = function_exists('mb_strtoupper')
            ? mb_strtoupper($this->value, $this->encoding)
            : strtoupper($this->value);

        return new self($upper, $this->mode, $this->encoding);
    }

    protected function toUpperCase(): self
    {
        return $this->toUpper();
    }

    protected function toLower(): self
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

    protected function toLowerCase(): self
    {
        return $this->toLower();
    }

    protected function ucfirst(): self
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

    protected function lcfirst(): self
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

    public function toString(): string
    {
        return $this->value;
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $input_delimiter
     * @return array<int, string>
     */
    private function extractCaseWords(HtmlTag|Newline|Regex|Stringable|string|array $input_delimiter): array
    {
        $delimiters = is_array($input_delimiter) ? $input_delimiter : [$input_delimiter];
        if ($delimiters === []) {
            throw new InvalidArgumentException('Input delimiter cannot be empty.');
        }

        $normalized_delimiters = [];
        foreach ($delimiters as $delimiter) {
            $fragment = self::normalizeFragment($delimiter);
            if ($fragment === '') {
                throw new InvalidArgumentException('Input delimiters cannot be empty.');
            }

            $normalized_delimiters[$fragment] = true;
        }

        $normalized_delimiters = array_keys($normalized_delimiters);

        $working = $this->value;
        $patterns = [
            '/(?<=\p{Ll})(?=\p{Lu})/u',
            '/(?<=\p{L})(?=\p{Nd})/u',
            '/(?<=\p{Nd})(?=\p{L})/u',
            '/(?<=\p{Lu})(?=\p{Lu}\p{Ll})/u',
        ];

        foreach ($patterns as $pattern) {
            $next = preg_replace($pattern, ' ', $working);
            if ($next !== null) {
                $working = $next;
            }
        }

        if ($normalized_delimiters !== []) {
            $escaped_parts = array_map(
                static fn (string $value): string => preg_quote($value, '/'),
                $normalized_delimiters
            );
            $delimiter_pattern = '/(?:' . implode('|', $escaped_parts) . ')+/u';
            $next = preg_replace($delimiter_pattern, ' ', $working);
            if ($next !== null) {
                $working = $next;
            }
        }

        $segments = preg_split('/[\s_]+/u', $working);
        if ($segments === false) {
            $segments = preg_split('/_+/', $working);
        }

        $words = [];
        foreach ($segments as $segment) {
            if ($segment === '' || $segment === null) {
                continue;
            }

            $segment = trim($segment);
            if ($segment === '') {
                continue;
            }

            $words[] = self::lowercaseString($segment, $this->encoding);
        }

        return $words;
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $value
     * @return list<string>
     */
    /**
     * @return list<list<string>>
     */
    private static function normalizeSearchOptions(
        HtmlTag|Newline|Regex|Stringable|string|array $value,
        string $behavior
    ): array
    {
        $normalized_behavior = self::normalizeSearchBehavior($behavior);

        if ($normalized_behavior === 'or') {
            return self::normalizeSearchOptionsOrBehavior($value);
        }

        return self::normalizeSearchOptionsSequentialBehavior($value);
    }

    private static function normalizeSearchBehavior(string $behavior): string
    {
        $normalized = strtolower($behavior);
        if (!in_array($normalized, ['sequential', 'or'], true)) {
            throw new InvalidArgumentException('Search behavior must be either "sequential" or "or".');
        }

        return $normalized;
    }

    /**
     * @return list<list<string>>
     */
    private static function normalizeSearchOptionsOrBehavior(HtmlTag|Newline|Regex|Stringable|string|array $value): array
    {
        $items = is_array($value) ? $value : [$value];
        if ($items === []) {
            throw new InvalidArgumentException('Search sequence cannot be empty.');
        }

        $options = [];
        foreach ($items as $item) {
            $sequence_items = is_array($item) ? $item : [$item];
            if ($sequence_items === []) {
                throw new InvalidArgumentException('Search sequence cannot be empty.');
            }

            $sequence = [];
            foreach ($sequence_items as $fragment_value) {
                $fragment = self::normalizeSearchFragment($fragment_value);
                if ($fragment === '') {
                    throw new InvalidArgumentException('Search sequence values cannot be empty.');
                }

                $sequence[] = $fragment;
            }

            $options[] = $sequence;
        }

        return $options;
    }

    /**
     * @return list<list<string>>
     */
    private static function normalizeSearchOptionsSequentialBehavior(
        HtmlTag|Newline|Regex|Stringable|string|array $value
    ): array {
        if (!is_array($value)) {
            $fragment = self::normalizeSearchFragment($value);
            if ($fragment === '') {
                throw new InvalidArgumentException('Search sequence values cannot be empty.');
            }

            return [[$fragment]];
        }

        if ($value === []) {
            throw new InvalidArgumentException('Search sequence cannot be empty.');
        }

        $has_nested_arrays = false;
        foreach ($value as $item) {
            if (is_array($item)) {
                $has_nested_arrays = true;
                break;
            }
        }

        if (!$has_nested_arrays) {
            $sequence = [];
            foreach ($value as $fragment_value) {
                $fragment = self::normalizeSearchFragment($fragment_value);
                if ($fragment === '') {
                    throw new InvalidArgumentException('Search sequence values cannot be empty.');
                }

                $sequence[] = $fragment;
            }

            return [$sequence];
        }

        return self::normalizeSearchOptionsOrBehavior($value);
    }

    private static function normalizeSearchFragment(mixed $value): string
    {
        if ($value instanceof Newline) {
            return self::canonicalizeLineBreak((string) $value);
        }

        return self::normalizeFragment($value);
    }

    private static function canonicalizeLineBreak(string $line_break): string
    {
        if ($line_break === '') {
            return '';
        }

        return str_contains($line_break, "\n") ? "\n" : $line_break;
    }

    /**
     * @param list<list<string>> $options
     * @return array{start: int, end: int}|null
     */
    private static function findSequence(string $subject, array $options, int $offset): ?array
    {
        $best_match = null;

        foreach ($options as $sequence) {
            $current_offset = max(0, $offset);
            $start = null;

            foreach ($sequence as $fragment) {
                $position = strpos($subject, $fragment, $current_offset);
                if ($position === false) {
                    continue 2;
                }

                if ($start === null) {
                    $start = $position;
                }

                $current_offset = $position + strlen($fragment);
            }

            if ($start === null) {
                continue;
            }

            $match = [
                'start' => $start,
                'end' => $current_offset,
            ];

            if (
                $best_match === null
                || $match['start'] < $best_match['start']
                || ($match['start'] === $best_match['start'] && $match['end'] > $best_match['end'])
            ) {
                $best_match = $match;
            }
        }

        return $best_match;
    }

    /**
     * @param list<list<string>> $options
     * @return list<array{start: int, end: int}>
     */
    private static function findAllSequences(string $subject, array $options): array
    {
        $occurrences = [];
        $offset = 0;

        while (true) {
            $match = self::findSequence($subject, $options, $offset);
            if ($match === null) {
                break;
            }

            $occurrences[] = $match;
            $next_offset = $match['start'] + 1;
            if ($next_offset <= $offset) {
                $next_offset = $offset + 1;
            }

            $offset = $next_offset;
        }

        return $occurrences;
    }

    /**
     * @param list<list<string>> $options
     * @return array{start: int, end: int}|null
     */
    private static function findSequenceWithSkip(string $subject, array $options, int $offset, int $skip): ?array
    {
        $current_offset = max(0, $offset);
        $remaining = $skip;

        while (true) {
            $match = self::findSequence($subject, $options, $current_offset);
            if ($match === null) {
                return null;
            }

            if ($remaining === 0) {
                return $match;
            }

            $remaining--;
            $advance = max(1, $match['end'] - $match['start']);
            $current_offset = $match['start'] + $advance;
        }
    }

    private function collapseWhitespaceToReplacement(string $replacement): self
    {
        $normalized = $this->collapseWhitespace(space: true, tab: true, newline: true);
        $string = (string) $normalized;

        $search = ["\r\n", "\r", "\n", "\t", ' '];
        $converted = str_replace($search, $replacement, $string);

        $repeat_pattern = '/' . preg_quote($replacement, '/') . '+/u';
        $collapsed = preg_replace($repeat_pattern, $replacement, $converted);
        if ($collapsed !== null) {
            $converted = $collapsed;
        }

        return new self($converted, $this->mode, $this->encoding);
    }

    private static function fragmentEndsWithAbbreviation(string $fragment): bool
    {
        $trimmed = trim($fragment);
        if ($trimmed === '') {
            return false;
        }

        $trimmed = preg_replace('/["\'\)\]\}»”’]+$/u', '', $trimmed) ?? $trimmed;
        $parts = preg_split('/\s+/u', $trimmed, -1, PREG_SPLIT_NO_EMPTY);
        if ($parts === false || $parts === []) {
            $lastWord = $trimmed;
        } else {
            $last = end($parts);
            $lastWord = is_string($last) ? $last : $trimmed;
        }

        $normalized = strtolower($lastWord);
        $abbreviations = [
            'mr.', 'mrs.', 'ms.', 'dr.', 'prof.', 'sr.', 'jr.', 'vs.', 'etc.', 'e.g.', 'i.e.', 'no.', 'fig.', 'st.', 'rd.',
            'th.', 'jan.', 'feb.', 'mar.', 'apr.', 'jun.', 'jul.', 'aug.', 'sep.', 'sept.', 'oct.', 'nov.', 'dec.',
        ];

        if (in_array($normalized, $abbreviations, true)) {
            return true;
        }

        return preg_match('/^\p{Lu}\.$/u', $lastWord) === 1;
    }

    /**
     * @return array{offset: int, index: int}|null
     */
    private function locateCandidate(HtmlTag|Newline|Regex|Stringable|string $candidate, bool $reversed): ?array
    {
        if ($candidate instanceof HtmlTag) {
            if ($reversed) {
                $matches = self::findAllHtmlTagMatches($this->value, $candidate);
                if ($matches === []) {
                    return null;
                }

                $match = end($matches);
                if (!is_array($match) || !isset($match['offset'])) {
                    return null;
                }

                $offset = (int) $match['offset'];
            } else {
                $match = self::findNextHtmlTagMatch($this->value, $candidate, 0);
                if ($match === null || !isset($match['offset'])) {
                    return null;
                }

                $offset = (int) $match['offset'];
            }

            return [
                'offset' => $offset,
                'index' => $this->convertOffsetToIndex($offset),
            ];
        }

        if ($candidate instanceof Regex) {
            $pattern = (string) $candidate;
            $subject = $this->value;

            if ($reversed) {
                $matches = [];
                $result = self::withRegexErrorHandling(
                    static function () use ($pattern, $subject, &$matches) {
                        return preg_match_all($pattern, $subject, $matches, PREG_OFFSET_CAPTURE);
                    }
                );

                if (!is_int($result) || $result === 0) {
                    return null;
                }

                $match_set = $matches[0] ?? [];
                if ($match_set === []) {
                    return null;
                }

                $last = end($match_set);
                if (!is_array($last) || !isset($last[1]) || !is_int($last[1])) {
                    return null;
                }

                $offset = $last[1];
            } else {
                $matches = [];
                $result = self::withRegexErrorHandling(
                    static function () use ($pattern, $subject, &$matches) {
                        return preg_match($pattern, $subject, $matches, PREG_OFFSET_CAPTURE);
                    }
                );

                if (!is_int($result) || $result === 0) {
                    return null;
                }

                if (!isset($matches[0]) || !is_array($matches[0]) || !isset($matches[0][1]) || !is_int($matches[0][1])) {
                    return null;
                }

                $offset = $matches[0][1];
            }

            return [
                'offset' => $offset,
                'index' => $this->convertOffsetToIndex($offset),
            ];
        }

        if ($candidate instanceof Newline) {
            $offset = $this->locateNewlineOffset($candidate, $reversed);
            if ($offset === null) {
                return null;
            }

            return [
                'offset' => $offset,
                'index' => $this->convertOffsetToIndex($offset),
            ];
        }

        $fragment = self::normalizeFragment($candidate);
        if ($fragment === '') {
            throw new InvalidArgumentException('Search value cannot be empty.');
        }

        $position = $reversed ? strrpos($this->value, $fragment) : strpos($this->value, $fragment);
        if ($position === false) {
            return null;
        }

        $offset = (int) $position;

        return [
            'offset' => $offset,
            'index' => $this->convertOffsetToIndex($offset),
        ];
    }

    private function startsWithCandidate(HtmlTag|Newline|Regex|Stringable|string $candidate): bool
    {
        if ($candidate instanceof HtmlTag) {
            $match = self::findNextHtmlTagMatch($this->value, $candidate, 0);
            return $match !== null && isset($match['offset']) && (int) $match['offset'] === 0;
        }

        if ($candidate instanceof Regex) {
            $pattern = (string) $candidate;
            $subject = $this->value;
            $matches = [];

            $result = self::withRegexErrorHandling(
                static function () use ($pattern, $subject, &$matches) {
                    return preg_match($pattern, $subject, $matches, PREG_OFFSET_CAPTURE);
                }
            );

            if (!is_int($result) || $result === 0) {
                return false;
            }

            return isset($matches[0][1]) && (int) $matches[0][1] === 0;
        }

        if ($candidate instanceof Newline) {
            $constraint = $candidate->getLineConstraint();
            if ($constraint !== null) {
                if ($this->value === '') {
                    return false;
                }

                $line = $this->firstLineForNewline($candidate);

                return self::lineMatchesConstraint($line, $constraint);
            }

            foreach ($this->newlineFragments($candidate) as $fragment) {
                if (str_starts_with($this->value, $fragment)) {
                    return true;
                }
            }

            return false;
        }

        $fragment = self::normalizeFragment($candidate);
        if ($fragment === '') {
            throw new InvalidArgumentException('Search value cannot be empty.');
        }

        return str_starts_with($this->value, $fragment);
    }

    private function endsWithCandidate(HtmlTag|Newline|Regex|Stringable|string $candidate): bool
    {
        if ($candidate instanceof HtmlTag) {
            $matches = self::findAllHtmlTagMatches($this->value, $candidate);
            if ($matches === []) {
                return false;
            }

            $match = end($matches);
            if (!is_array($match) || !isset($match['offset'], $match['length'])) {
                return false;
            }

            $end = (int) $match['offset'] + (int) $match['length'];

            return $end === strlen($this->value);
        }

        if ($candidate instanceof Regex) {
            $pattern = (string) $candidate;
            $subject = $this->value;
            $matches = [];

            $result = self::withRegexErrorHandling(
                static function () use ($pattern, $subject, &$matches) {
                    return preg_match_all($pattern, $subject, $matches, PREG_OFFSET_CAPTURE);
                }
            );

            if (!is_int($result) || $result === 0) {
                return false;
            }

            $length = strlen($subject);
            $match_set = $matches[0] ?? [];

            for ($index = count($match_set) - 1; $index >= 0; $index--) {
                $match = $match_set[$index];
                if (!is_array($match) || !isset($match[0], $match[1]) || !is_int($match[1])) {
                    continue;
                }

                $match_string = (string) $match[0];
                $offset = $match[1];
                $end = $offset + strlen($match_string);

                if ($match_string === '') {
                    if ($offset === $length) {
                        return true;
                    }

                    continue;
                }

                if ($end === $length) {
                    return true;
                }
            }

            return false;
        }

        if ($candidate instanceof Newline) {
            $constraint = $candidate->getLineConstraint();
            if ($constraint !== null) {
                $line = $this->lastLineForNewline($candidate);

                return self::lineMatchesConstraint($line, $constraint);
            }

            foreach ($this->newlineFragments($candidate) as $fragment) {
                if ($fragment !== '' && str_ends_with($this->value, $fragment)) {
                    return true;
                }
            }

            return false;
        }

        $fragment = self::normalizeFragment($candidate);
        if ($fragment === '') {
            throw new InvalidArgumentException('Search value cannot be empty.');
        }

        return str_ends_with($this->value, $fragment);
    }

    private function equalsCandidate(HtmlTag|Newline|Regex|Stringable|string $candidate, bool $case_sensitive): bool
    {
        if ($candidate instanceof Regex) {
            $pattern = (string) $candidate;
            $subject = $this->value;
            $matches = [];

            $result = self::withRegexErrorHandling(
                static function () use ($pattern, $subject, &$matches) {
                    return preg_match($pattern, $subject, $matches, PREG_OFFSET_CAPTURE);
                }
            );

            if (!is_int($result) || $result === 0) {
                return false;
            }

            if (!isset($matches[0]) || !is_array($matches[0]) || !isset($matches[0][0], $matches[0][1])) {
                return false;
            }

            return $matches[0][1] === 0 && $matches[0][0] === $subject;
        }

        if ($candidate instanceof Newline) {
            $constraint = $candidate->getLineConstraint();
            if ($constraint !== null && $constraint['type'] === 'equals') {
                $segments = self::splitLines($this->value, $candidate, true);
                $lines = $segments['lines'];

                if ($segments['has_trailing_break'] && $lines !== [] && $lines[array_key_last($lines)] === '') {
                    array_pop($lines);
                }

                if (count($lines) !== 1) {
                    return false;
                }

                return self::lineMatchesConstraint($lines[0], $constraint);
            }

            $fragments = $this->newlineFragments($candidate);
            $subject = $this->value;

            if ($case_sensitive) {
                foreach ($fragments as $fragment) {
                    if ($subject === $fragment) {
                        return true;
                    }
                }

                return false;
            }

            $normalized_subject = self::lowercaseString($subject, $this->encoding);

            foreach ($fragments as $fragment) {
                if ($normalized_subject === self::lowercaseString($fragment, $this->encoding)) {
                    return true;
                }
            }

            return false;
        }

        $comparison = self::normalizeFragment($candidate);

        if ($case_sensitive) {
            return $this->value === $comparison;
        }

        return self::lowercaseString($this->value, $this->encoding)
            === self::lowercaseString($comparison, $this->encoding);
    }

    private function countCandidateOccurrences(HtmlTag|Newline|Regex|Stringable|string $candidate): int
    {
        if ($candidate instanceof HtmlTag) {
            return count(self::findAllHtmlTagMatches($this->value, $candidate));
        }

        if ($candidate instanceof Regex) {
            $pattern = (string) $candidate;
            $subject = $this->value;
            $matches = [];

            $result = self::withRegexErrorHandling(
                static function () use ($pattern, $subject, &$matches) {
                    return preg_match_all($pattern, $subject, $matches, PREG_OFFSET_CAPTURE);
                }
            );

            if (!is_int($result) || $result === 0) {
                return 0;
            }

            $occurrences = 0;

            foreach ($matches[0] ?? [] as $match) {
                if (!is_array($match) || !isset($match[0])) {
                    continue;
                }

                $match_string = (string) $match[0];
                if ($match_string === '') {
                    throw new InvalidArgumentException('Regex pattern cannot match an empty string when counting occurrences.');
                }

                $occurrences++;
            }

            return $occurrences;
        }

        if ($candidate instanceof Newline) {
            $fragment = (string) $candidate;
            $canonical = self::canonicalizeLineBreak($fragment);

            if ($fragment === '' && $canonical === '') {
                throw new InvalidArgumentException('Search value cannot be empty.');
            }

            if ($fragment !== '') {
                $count = substr_count($this->value, $fragment);
                if ($count > 0 || $canonical === '' || $canonical === $fragment) {
                    return $count;
                }
            }

            if ($canonical !== '' && $canonical !== $fragment) {
                return substr_count($this->value, $canonical);
            }

            return 0;
        }

        $fragment = self::normalizeFragment($candidate);
        if ($fragment === '') {
            throw new InvalidArgumentException('Search value cannot be empty.');
        }

        return substr_count($this->value, $fragment);
    }

    private function convertOffsetToIndex(int $offset): int
    {
        if ($offset <= 0) {
            return 0;
        }

        $prefix = substr($this->value, 0, $offset);
        if ($prefix === false || $prefix === '') {
            return 0;
        }

        return match ($this->mode) {
            'bytes' => strlen($prefix),
            'codepoints' => $this->calculateCodepointLength($prefix),
            default => $this->calculateGraphemeLength($prefix),
        };
    }

    private function calculateCodepointLength(string $value): int
    {
        if ($value === '') {
            return 0;
        }

        if (function_exists('mb_strlen')) {
            $length = mb_strlen($value, $this->encoding);
            if (is_int($length)) {
                return $length;
            }
        }

        return strlen($value);
    }

    private function calculateGraphemeLength(string $value): int
    {
        if ($value === '') {
            return 0;
        }

        if (function_exists('grapheme_strlen')) {
            $length = grapheme_strlen($value);
            if (is_int($length)) {
                return $length;
            }
        }

        $count = preg_match_all('/\\X/u', $value);
        if ($count !== false) {
            return $count;
        }

        return strlen($value);
    }

    private function newlineFragments(Newline $newline): array
    {
        $fragment = (string) $newline;
        $canonical = self::canonicalizeLineBreak($fragment);

        if ($fragment === '' && $canonical === '') {
            throw new InvalidArgumentException('Search value cannot be empty.');
        }

        $fragments = [];

        if ($fragment !== '') {
            $fragments[] = $fragment;
        }

        if ($canonical !== '' && $canonical !== $fragment) {
            $fragments[] = $canonical;
        }

        if ($fragments === []) {
            $fragments[] = '';
        }

        return array_values(array_unique($fragments));
    }

    private function firstLineForNewline(Newline $newline): string
    {
        if ($this->value === '') {
            return '';
        }

        $line_break = (string) $newline;
        $subject = $this->value;

        if ($line_break === '') {
            return $subject;
        }

        $position = strpos($subject, $line_break);
        if ($position === false) {
            $canonical = self::canonicalizeLineBreak($line_break);
            if ($canonical !== '' && $canonical !== $line_break) {
                $position = strpos($subject, $canonical);
            }
        }

        if ($position === false) {
            return $subject;
        }

        $line = substr($subject, 0, $position);

        return $line === false ? '' : $line;
    }

    private function lastLineForNewline(Newline $newline): string
    {
        if ($this->value === '') {
            return '';
        }

        $line_break = (string) $newline;
        $subject = $this->value;

        if ($line_break === '') {
            return $subject;
        }

        $split_break = $line_break;
        $position = strrpos($subject, $split_break);

        if ($position === false) {
            $canonical = self::canonicalizeLineBreak($line_break);
            if ($canonical !== '' && $canonical !== $line_break) {
                $position = strrpos($subject, $canonical);
                if ($position !== false) {
                    $split_break = $canonical;
                }
            }
        }

        if ($position === false) {
            return $subject;
        }

        $line = substr($subject, $position + strlen($split_break));

        return $line === false ? '' : $line;
    }

    /**
     * @return array{lines: array<int, string>, has_trailing_break: bool, line_break: string, split_break: string}
     */
    private static function splitLines(string $subject, Newline $newline, bool $preserve_trailing_blank): array
    {
        $line_break = (string) $newline;
        $split_break = $line_break;

        if ($line_break === '') {
            return [
                'lines' => [$subject],
                'has_trailing_break' => false,
                'line_break' => $line_break,
                'split_break' => $split_break,
            ];
        }

        if (!str_contains($subject, $split_break)) {
            $canonical = self::canonicalizeLineBreak($line_break);
            if ($canonical !== '' && $canonical !== $line_break && str_contains($subject, $canonical)) {
                $split_break = $canonical;
            }
        }

        $segments = $split_break === '' ? [$subject] : explode($split_break, $subject);

        $has_trailing_break = ($split_break !== '' && str_ends_with($subject, $split_break))
            || ($line_break !== $split_break && $line_break !== '' && str_ends_with($subject, $line_break));

        if (!$preserve_trailing_blank && $has_trailing_break && $segments !== []) {
            array_pop($segments);
        }

        return [
            'lines' => $segments,
            'has_trailing_break' => $has_trailing_break,
            'line_break' => $line_break,
            'split_break' => $split_break,
        ];
    }

    /**
     * @param array{type: 'starts_with'|'ends_with'|'contains'|'equals', needle: string, trim?: bool} $constraint
     */
    private static function lineMatchesConstraint(string $line, array $constraint): bool
    {
        return match ($constraint['type']) {
            'starts_with' => str_starts_with(
                ($constraint['trim'] ?? false) ? ltrim($line, " \t") : $line,
                $constraint['needle']
            ),
            'ends_with' => str_ends_with(
                ($constraint['trim'] ?? false) ? rtrim($line, " \t") : $line,
                $constraint['needle']
            ),
            'contains' => str_contains($line, $constraint['needle']),
            'equals' => $line === $constraint['needle'],
            default => false,
        };
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string $candidate
     */
    private function containsCandidate(HtmlTag|Newline|Regex|Stringable|string $candidate): bool
    {
        if ($candidate instanceof HtmlTag) {
            return self::findNextHtmlTagMatch($this->value, $candidate, 0) !== null;
        }

        if ($candidate instanceof Regex) {
            $pattern = (string) $candidate;
            $subject = $this->value;

            $result = self::withRegexErrorHandling(
                static function () use ($pattern, $subject) {
                    return preg_match($pattern, $subject);
                }
            );

            return is_int($result) && $result > 0;
        }

        if ($candidate instanceof Newline) {
            $constraint = $candidate->getLineConstraint();
            if ($constraint !== null) {
                $segments = self::splitLines($this->value, $candidate, true);

                foreach ($segments['lines'] as $line) {
                    if (self::lineMatchesConstraint($line, $constraint)) {
                        return true;
                    }
                }

                return false;
            }

            $fragment = (string) $candidate;
            $canonical = self::canonicalizeLineBreak($fragment);

            if ($fragment === '' && $canonical === '') {
                throw new InvalidArgumentException('Search value cannot be empty.');
            }

            if ($fragment !== '' && str_contains($this->value, $fragment)) {
                return true;
            }

            return $canonical !== '' && str_contains($this->value, $canonical);
        }

        $fragment = self::normalizeFragment($candidate);
        if ($fragment === '') {
            throw new InvalidArgumentException('Search value cannot be empty.');
        }

        return str_contains($this->value, $fragment);
    }

    private function removePrefixCandidate(HtmlTag|Newline|Regex|Stringable|string $candidate): ?string
    {
        if ($this->value === '') {
            return null;
        }

        if ($candidate instanceof HtmlTag) {
            $match = self::findNextHtmlTagMatch($this->value, $candidate, 0);
            if ($match !== null && $match['offset'] === 0 && $match['length'] > 0) {
                return substr($this->value, $match['length']);
            }

            return null;
        }

        if ($candidate instanceof Regex) {
            $pattern = (string) $candidate;
            $subject = $this->value;
            $matches = [];

            $result = self::withRegexErrorHandling(
                static function () use ($pattern, $subject, &$matches) {
                    return preg_match($pattern, $subject, $matches, PREG_OFFSET_CAPTURE);
                }
            );

            if (!is_int($result) || $result === 0) {
                return null;
            }

            if (!isset($matches[0][0], $matches[0][1])) {
                return null;
            }

            $matched = (string) $matches[0][0];
            $offset = (int) $matches[0][1];

            if ($offset !== 0 || $matched === '') {
                return null;
            }

            return substr($this->value, strlen($matched));
        }

        if ($candidate instanceof Newline) {
            foreach ($this->newlineFragments($candidate) as $fragment) {
                if ($fragment === '') {
                    continue;
                }

                if (str_starts_with($this->value, $fragment)) {
                    return substr($this->value, strlen($fragment));
                }
            }

            return null;
        }

        $fragment = self::normalizeFragment($candidate);
        if ($fragment === '') {
            throw new InvalidArgumentException('Prefix cannot be empty.');
        }

        if (!str_starts_with($this->value, $fragment)) {
            return null;
        }

        return substr($this->value, strlen($fragment));
    }

    private function removeSuffixCandidate(HtmlTag|Newline|Regex|Stringable|string $candidate): ?string
    {
        if ($this->value === '') {
            return null;
        }

        if ($candidate instanceof HtmlTag) {
            $matches = self::findAllHtmlTagMatches($this->value, $candidate);
            if ($matches === []) {
                return null;
            }

            $match = end($matches);
            if (!is_array($match) || !isset($match['offset'], $match['length'])) {
                return null;
            }

            $offset = (int) $match['offset'];
            $length = (int) $match['length'];
            if ($length <= 0 || $offset + $length !== strlen($this->value)) {
                return null;
            }

            return substr($this->value, 0, $offset);
        }

        if ($candidate instanceof Regex) {
            $pattern = (string) $candidate;
            $subject = $this->value;
            $matches = [];

            $result = self::withRegexErrorHandling(
                static function () use ($pattern, $subject, &$matches) {
                    return preg_match_all($pattern, $subject, $matches, PREG_OFFSET_CAPTURE);
                }
            );

            if (!is_int($result) || $result === 0) {
                return null;
            }

            $match_set = $matches[0] ?? [];
            $length = strlen($subject);

            for ($index = count($match_set) - 1; $index >= 0; $index--) {
                $match = $match_set[$index] ?? null;
                if (!is_array($match) || !isset($match[0], $match[1]) || !is_int($match[1])) {
                    continue;
                }

                $matched_string = (string) $match[0];
                if ($matched_string === '') {
                    continue;
                }

                $offset = $match[1];
                $end = $offset + strlen($matched_string);

                if ($end === $length) {
                    return substr($subject, 0, $offset);
                }
            }

            return null;
        }

        if ($candidate instanceof Newline) {
            foreach ($this->newlineFragments($candidate) as $fragment) {
                if ($fragment === '') {
                    continue;
                }

                if (str_ends_with($this->value, $fragment)) {
                    return substr($this->value, 0, strlen($this->value) - strlen($fragment));
                }
            }

            return null;
        }

        $fragment = self::normalizeFragment($candidate);
        if ($fragment === '') {
            throw new InvalidArgumentException('Suffix cannot be empty.');
        }

        if (!str_ends_with($this->value, $fragment)) {
            return null;
        }

        return substr($this->value, 0, strlen($this->value) - strlen($fragment));
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
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $allowed_tags
     */
    private static function normalizeAllowedTags(HtmlTag|Newline|Regex|Stringable|string|array $allowed_tags): string
    {
        $candidates = is_array($allowed_tags) ? $allowed_tags : [$allowed_tags];
        if ($candidates === []) {
            return '';
        }

        $fragments = [];

        foreach ($candidates as $candidate) {
            if (is_array($candidate)) {
                throw new InvalidArgumentException('Nested allowed tag arrays are not supported.');
            }

            if ($candidate instanceof HtmlTag) {
                $tag_name = trim($candidate->getRawTagName());
                if ($tag_name !== '') {
                    $fragments[] = sprintf('<%s>', $tag_name);
                }

                continue;
            }

            $fragment = trim(self::normalizeFragment($candidate));
            if ($fragment === '') {
                continue;
            }

            if (str_contains($fragment, '<')) {
                $fragments[] = $fragment;

                continue;
            }

            $parts = preg_split('/\s+/', $fragment, -1, PREG_SPLIT_NO_EMPTY);
            if ($parts === false) {
                continue;
            }

            foreach ($parts as $part) {
                $clean = trim((string) $part, "<> ");
                if ($clean !== '') {
                    $fragments[] = sprintf('<%s>', $clean);
                }
            }
        }

        if ($fragments === []) {
            return '';
        }

        return implode('', array_values(array_unique($fragments)));
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

    private static function removeIndentationPrefix(string $prefix, int $spaces, int $tabs): string
    {
        if ($prefix === '') {
            return '';
        }

        $remaining_spaces = $spaces;
        $remaining_tabs = $tabs;
        $length = strlen($prefix);
        $index = 0;

        while ($index < $length) {
            $char = $prefix[$index];

            if ($char === ' ' && $remaining_spaces > 0) {
                $remaining_spaces--;
                $index++;
                continue;
            }

            if ($char === "\t" && $remaining_tabs > 0) {
                $remaining_tabs--;
                $index++;
                continue;
            }

            break;
        }

        if ($index >= $length) {
            return '';
        }

        return substr($prefix, $index);
    }

    private static function buildPadding(string $pad_fragment, int $units, string $mode, string $encoding): string
    {
        if ($units <= 0) {
            return '';
        }

        $units_pool = self::splitByMode($pad_fragment, $mode, $encoding);
        if ($units_pool === []) {
            return '';
        }

        $pool_count = count($units_pool);
        $result = '';

        $full_repeats = intdiv($units, $pool_count);
        if ($full_repeats > 0) {
            $result .= str_repeat($pad_fragment, $full_repeats);
        }

        $remainder = $units % $pool_count;
        if ($remainder > 0) {
            $result .= implode('', array_slice($units_pool, 0, $remainder));
        }

        return $result;
    }

    private function trimInternal(bool $trim_left, bool $trim_right, bool $newline, bool $space, bool $tab): self
    {
        $mask = self::buildTrimMask($newline, $space, $tab);
        if ($mask === '') {
            return new self($this->value, $this->mode, $this->encoding);
        }

        $result = $this->value;
        if ($trim_left && $trim_right) {
            $result = trim($result, $mask);
        } elseif ($trim_left) {
            $result = ltrim($result, $mask);
        } elseif ($trim_right) {
            $result = rtrim($result, $mask);
        }

        return new self($result, $this->mode, $this->encoding);
    }

    private static function buildTrimMask(bool $newline, bool $space, bool $tab): string
    {
        $mask = '';

        if ($space) {
            $mask .= ' ';
        }

        if ($tab) {
            $mask .= "\t";
        }

        if ($newline) {
            $mask .= "\r\n";
        }

        return $mask;
    }

    private static function replaceFromStart(string $subject, string $search, string $replace, int &$remaining): string
    {
        $search_length = strlen($search);
        $offset = 0;

        while ($remaining > 0) {
            $position = strpos($subject, $search, $offset);
            if ($position === false) {
                break;
            }

            $subject = substr($subject, 0, $position)
                . $replace
                . substr($subject, $position + $search_length);

            $offset = $position + strlen($replace);
            $remaining--;
        }

        return $subject;
    }

    private static function replaceFromEnd(string $subject, string $search, string $replace, int &$remaining): string
    {
        $search_length = strlen($search);

        while ($remaining > 0) {
            $position = strrpos($subject, $search);
            if ($position === false) {
                break;
            }

            $subject = substr($subject, 0, $position)
                . $replace
                . substr($subject, $position + $search_length);

            $remaining--;
        }

        return $subject;
    }

    private static function replaceWithRegex(
        string $subject,
        string $pattern,
        string $replacement,
        int &$remaining,
        bool $reversed
    ): string {
        if ($remaining <= 0) {
            return $subject;
        }

        if ($reversed) {
            return self::replaceRegexFromEnd($subject, $pattern, $replacement, $remaining);
        }

        return self::replaceRegexFromStart($subject, $pattern, $replacement, $remaining);
    }

    private static function replaceRegexFromStart(string $subject, string $pattern, string $replace, int &$remaining): string
    {
        if ($remaining <= 0) {
            return $subject;
        }

        $unlimited = $remaining === PHP_INT_MAX;
        $limit = $unlimited ? -1 : $remaining;
        $count = 0;

        $result = self::withRegexErrorHandling(
            static function () use ($pattern, $replace, $subject, $limit, &$count) {
                return preg_replace($pattern, $replace, $subject, $limit, $count);
            }
        );

        if (!is_string($result)) {
            return $subject;
        }

        if (!$unlimited) {
            $remaining = max(0, $remaining - $count);
        }

        return $result;
    }

    private static function replaceRegexFromEnd(string $subject, string $pattern, string $replace, int &$remaining): string
    {
        if ($remaining <= 0) {
            return $subject;
        }

        $unlimited = $remaining === PHP_INT_MAX;
        $matches = [];

        $matchCount = self::withRegexErrorHandling(
            static function () use ($pattern, $subject, &$matches) {
                return preg_match_all($pattern, $subject, $matches, PREG_OFFSET_CAPTURE);
            }
        );

        if (!is_int($matchCount) || $matchCount === 0) {
            return $subject;
        }

        $fullMatches = $matches[0] ?? [];
        if (!is_array($fullMatches) || $fullMatches === []) {
            return $subject;
        }

        for ($index = count($fullMatches) - 1; $index >= 0; $index--) {
            if (!$unlimited && $remaining === 0) {
                break;
            }

            $entry = $fullMatches[$index];
            if (!is_array($entry) || count($entry) < 2) {
                continue;
            }

            [$matchedText, $offset] = $entry;
            if (!is_string($matchedText) || !is_int($offset)) {
                continue;
            }

            $replacementText = self::withRegexErrorHandling(
                static function () use ($pattern, $replace, $matchedText) {
                    return preg_replace($pattern, $replace, $matchedText, 1);
                }
            );

            if (!is_string($replacementText)) {
                continue;
            }

            $subject = substr($subject, 0, $offset)
                . $replacementText
                . substr($subject, $offset + strlen($matchedText));

            if (!$unlimited) {
                $remaining--;
            }
        }

        return $subject;
    }

    /**
     * @return mixed
     */
    private static function withRegexErrorHandling(callable $operation)
    {
        set_error_handler(
            static function (int $errno, string $errstr): bool {
                throw new ValueError($errstr);
            }
        );

        try {
            return $operation();
        } finally {
            restore_error_handler();
        }
    }

    /**
     * @param array<int, int>|int|null $flags
     */
    private static function normalizeMatchAllFlags(array|int|null $flags): int
    {
        if (is_int($flags)) {
            return $flags === 0 ? PREG_PATTERN_ORDER : $flags;
        }

        if ($flags === null) {
            return PREG_PATTERN_ORDER;
        }

        $normalized = 0;
        foreach ($flags as $flag) {
            if (!is_int($flag)) {
                throw new InvalidArgumentException('Flags array must contain integers.');
            }

            $normalized |= $flag;
        }

        return $normalized === 0 ? PREG_PATTERN_ORDER : $normalized;
    }

    /**
     * @param array{type: 'starts_with'|'ends_with'|'contains'|'equals', needle: string, trim?: bool} $constraint
     */
    private static function replaceLinesMatchingConstraint(
        string $subject,
        Newline $newline,
        array $constraint,
        string $replacement,
        int &$remaining,
        bool $reversed
    ): string {
        if ($remaining <= 0) {
            return $subject;
        }

        $line_break = (string) $newline;
        if ($line_break === '') {
            return $subject;
        }

        $segments = self::splitLines($subject, $newline, false);
        $lines = $segments['lines'];

        if ($lines === []) {
            return $subject;
        }

        $indexes = $reversed
            ? array_reverse(array_keys($lines))
            : array_keys($lines);

        foreach ($indexes as $index) {
            if ($remaining === 0) {
                break;
            }

            $line = $lines[$index];

            if (!self::lineMatchesConstraint($line, $constraint)) {
                continue;
            }

            $lines[$index] = $replacement;
            $remaining--;
        }

        $result = implode($line_break, $lines);

        if ($segments['has_trailing_break']) {
            $result .= $line_break;
        }

        return $result;
    }

    private static function replaceHtmlTag(
        string $subject,
        HtmlTag $tag,
        string $replacement,
        int &$remaining,
        bool $reversed
    ): string {
        if ($remaining <= 0) {
            return $subject;
        }

        if ($reversed) {
            $matches = self::findAllHtmlTagMatches($subject, $tag);
            for ($index = count($matches) - 1; $index >= 0 && $remaining > 0; $index--) {
                $match = $matches[$index];
                $subject = substr($subject, 0, $match['offset'])
                    . $replacement
                    . substr($subject, $match['offset'] + $match['length']);
                $remaining--;
            }

            return $subject;
        }

        $offset = 0;
        while ($remaining > 0) {
            $match = self::findNextHtmlTagMatch($subject, $tag, $offset);
            if ($match === null) {
                break;
            }

            $subject = substr($subject, 0, $match['offset'])
                . $replacement
                . substr($subject, $match['offset'] + $match['length']);

            $offset = $match['offset'] + strlen($replacement);
            $remaining--;
        }

        return $subject;
    }

    /**
     * @return array<int, array{offset: int, length: int}>
     */
    private static function findAllHtmlTagMatches(string $subject, HtmlTag $tag): array
    {
        $matches = [];
        $offset = 0;

        while (true) {
            $match = self::findNextHtmlTagMatch($subject, $tag, $offset);
            if ($match === null) {
                break;
            }

            $matches[] = $match;
            $offset = $match['offset'] + $match['length'];
        }

        return $matches;
    }

    /**
     * @return array{offset: int, length: int}|null
     */
    private static function findNextHtmlTagMatch(string $subject, HtmlTag $tag, int $offset): ?array
    {
        $pattern = self::buildHtmlTagPattern($tag);
        $flags = $tag->isCaseSensitive() ? 'u' : 'iu';
        $regex = sprintf('/%s/%s', $pattern, $flags);

        $cursor = max(0, $offset);
        $length = strlen($subject);

        while ($cursor <= $length) {
            if (preg_match($regex, $subject, $matches, PREG_OFFSET_CAPTURE, $cursor) !== 1) {
                return null;
            }

            $match_string = $matches[0][0];
            $match_offset = $matches[0][1];

            if (!$tag->matchesFragment($match_string)) {
                $cursor = $match_offset + max(1, strlen($match_string));
                continue;
            }

            return [
                'offset' => $match_offset,
                'length' => strlen($match_string),
            ];
        }

        return null;
    }

    /**
     * @param array<int, HtmlTag|Newline|Regex|Stringable|string> $delimiters
     * @return array{start: int, end: int}|null
     */
    private static function findNextDelimiterMatch(string $subject, array $delimiters, int $offset): ?array
    {
        $best_match = null;

        foreach ($delimiters as $delimiter) {
            if ($delimiter instanceof HtmlTag) {
                $match = self::findNextHtmlTagMatch($subject, $delimiter, $offset);
                if ($match === null) {
                    continue;
                }

                $candidate = [
                    'start' => $match['offset'],
                    'end' => $match['offset'] + $match['length'],
                ];
            } elseif ($delimiter instanceof Regex) {
                $candidate = self::findNextRegexDelimiter($subject, (string) $delimiter, $offset);
                if ($candidate === null) {
                    continue;
                }
            } else {
                $fragment = $delimiter instanceof Newline
                    ? (string) $delimiter
                    : self::normalizeFragment($delimiter);

                if ($fragment === '') {
                    throw new InvalidArgumentException('Delimiter cannot be empty.');
                }

                $position = strpos($subject, $fragment, max(0, $offset));
                if ($position === false && $delimiter instanceof Newline) {
                    $canonical = self::canonicalizeLineBreak($fragment);
                    if ($canonical !== '' && $canonical !== $fragment) {
                        $position = strpos($subject, $canonical, max(0, $offset));
                        if ($position !== false) {
                            $fragment = $canonical;
                        }
                    }
                }

                if ($position === false) {
                    continue;
                }

                $candidate = [
                    'start' => $position,
                    'end' => $position + strlen($fragment),
                ];
            }

            if ($best_match === null
                || $candidate['start'] < $best_match['start']
                || ($candidate['start'] === $best_match['start'] && $candidate['end'] > $best_match['end'])
            ) {
                $best_match = $candidate;
            }
        }

        return $best_match;
    }

    /**
     * @return array{start: int, end: int}|null
     */
    private static function findNextRegexDelimiter(string $subject, string $pattern, int $offset): ?array
    {
        $matches = [];
        $result = self::withRegexErrorHandling(
            static function () use ($pattern, $subject, $offset, &$matches) {
                return preg_match($pattern, $subject, $matches, PREG_OFFSET_CAPTURE, max(0, $offset));
            }
        );

        if ($result !== 1) {
            return null;
        }

        $match_string = $matches[0][0];
        $match_offset = $matches[0][1];

        if ($match_string === '') {
            throw new InvalidArgumentException('Regex delimiter cannot match an empty string.');
        }

        return [
            'start' => $match_offset,
            'end' => $match_offset + strlen($match_string),
        ];
    }

    private static function buildHtmlTagPattern(HtmlTag $tag): string
    {
        $tag_name = preg_quote($tag->getRawTagName(), '/');

        if ($tag->isClosingTag()) {
            return sprintf('<\/%s\s*>', $tag_name);
        }

        return sprintf('<\s*%s\b[^>]*\/?\s*>', $tag_name);
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

    /**
     * @return list<string>
     */
    private static function splitByMode(string $value, string $mode, string $encoding): array
    {
        if ($value === '') {
            return [];
        }

        return match ($mode) {
            'bytes' => str_split($value),
            'codepoints' => self::splitCodepoints($value),
            default => self::splitGraphemes($value, $encoding),
        };
    }

    /**
     * @return list<string>
     */
    private static function splitCodepoints(string $value): array
    {
        if ($value === '') {
            return [];
        }

        if (preg_match('//u', $value) === 1) {
            $parts = preg_split('//u', $value, -1, PREG_SPLIT_NO_EMPTY);
            if ($parts !== false) {
                return $parts;
            }
        }

        return str_split($value);
    }

    /**
     * @return list<string>
     */
    private static function splitGraphemes(string $value, string $encoding): array
    {
        if ($value === '') {
            return [];
        }

        if (function_exists('grapheme_extract')) {
            $units = [];
            $offset = 0;
            $length = strlen($value);

            while ($offset < $length) {
                $cluster = grapheme_extract($value, 1, GRAPHEME_EXTR_COUNT, $offset, $next);
                if ($cluster === false || $cluster === '') {
                    $next = $offset + 1;
                    $cluster = substr($value, $offset, $next - $offset);
                }

                $units[] = $cluster;
                $offset = $next;
            }

            return $units;
        }

        $clusters = preg_split('/\\X/u', $value, -1, PREG_SPLIT_NO_EMPTY);
        if ($clusters !== false && $clusters !== []) {
            return $clusters;
        }

        $manualClusters = self::splitGraphemesManually($value);
        if ($manualClusters !== []) {
            return $manualClusters;
        }

        if (function_exists('mb_strlen')) {
            $units = [];
            $length = mb_strlen($value, $encoding);
            if ($length !== false) {
                for ($i = 0; $i < $length; $i++) {
                    $units[] = (string) mb_substr($value, $i, 1, $encoding);
                }

                return $units;
            }
        }

        return str_split($value);
    }

    /**
     * @return list<string>
     */
    private static function splitGraphemesManually(string $value): array
    {
        if ($value === '') {
            return [];
        }

        $codepoints = self::splitCodepoints($value);
        if ($codepoints === []) {
            return [];
        }

        $clusters = [];
        $joinNext = false;
        $regionalIndicatorRun = 0;

        foreach ($codepoints as $codepoint) {
            if (self::isRegionalIndicator($codepoint)) {
                if ($regionalIndicatorRun % 2 === 0) {
                    $clusters[] = $codepoint;
                } else {
                    $clusters[count($clusters) - 1] .= $codepoint;
                }

                $regionalIndicatorRun++;
                $joinNext = false;

                continue;
            }

            $regionalIndicatorRun = 0;

            if ($joinNext) {
                $clusters[count($clusters) - 1] .= $codepoint;
                $joinNext = self::isZeroWidthJoiner($codepoint);

                continue;
            }

            if ($clusters !== [] && self::shouldExtendCluster($codepoint)) {
                $clusters[count($clusters) - 1] .= $codepoint;

                continue;
            }

            if ($clusters !== [] && self::isZeroWidthJoiner($codepoint)) {
                $clusters[count($clusters) - 1] .= $codepoint;
                $joinNext = true;

                continue;
            }

            $clusters[] = $codepoint;
            $joinNext = self::isZeroWidthJoiner($codepoint);
        }

        return $clusters;
    }

    private static function isEmojiCluster(string $cluster): bool
    {
        if ($cluster === '') {
            return false;
        }

        if (preg_match('/\p{Extended_Pictographic}/u', $cluster) === 1) {
            return true;
        }

        if (preg_match('/\p{Regional_Indicator}/u', $cluster) === 1) {
            return true;
        }

        return preg_match('/^[#*0-9]\x{FE0F}?\x{20E3}$/u', $cluster) === 1;
    }

    private static function shouldExtendCluster(string $codepoint): bool
    {
        return self::isCombiningMark($codepoint)
            || self::isEmojiModifier($codepoint)
            || self::isVariationSelector($codepoint);
    }

    private static function isCombiningMark(string $codepoint): bool
    {
        return preg_match('/^\p{M}$/u', $codepoint) === 1;
    }

    private static function isEmojiModifier(string $codepoint): bool
    {
        return preg_match('/^[\x{1F3FB}-\x{1F3FF}]$/u', $codepoint) === 1;
    }

    private static function isVariationSelector(string $codepoint): bool
    {
        return preg_match('/^[\x{FE00}-\x{FE0F}\x{E0100}-\x{E01EF}]$/u', $codepoint) === 1;
    }

    private static function isZeroWidthJoiner(string $codepoint): bool
    {
        return $codepoint === "\u{200D}";
    }

    private static function isRegionalIndicator(string $codepoint): bool
    {
        return preg_match('/^[\x{1F1E6}-\x{1F1FF}]$/u', $codepoint) === 1;
    }

    private static function toAsciiForSlug(string $value, string $encoding): string
    {
        if ($value === '') {
            return '';
        }

        $originalValue = $value;

        if (class_exists('\\Transliterator')) {
            $transliterator = \Transliterator::create('Any-Latin; Latin-ASCII; [:Nonspacing Mark:] Remove; NFC');
            if ($transliterator !== null) {
                $transliterated = $transliterator->transliterate($value);
                if ($transliterated !== null) {
                    $value = $transliterated;
                }
            }
        }

        if (function_exists('iconv')) {
            $converted = @iconv($encoding, 'ASCII//TRANSLIT//IGNORE', $value);
            if ($converted !== false) {
                $value = $converted;
            }
        }

        if (self::shouldApplyAsciiFallback($value, $originalValue)) {
            $fallback = self::asciiTransliterationFallback($originalValue, $encoding);
            if ($fallback !== '') {
                $value = $fallback;
            }
        }

        return $value;
    }

    private static function applyAsciiTransliterationFallback(string $value, string $originalValue, string $encoding): string
    {
        $fallback = self::asciiTransliterationFallback($originalValue, $encoding);

        if (!self::shouldApplyAsciiFallback($value, $originalValue)) {
            if ($fallback !== '' && $fallback !== $value) {
                return $fallback;
            }

            return $value;
        }

        $candidate = $fallback !== '' ? $fallback : $value;

        if (preg_match('/[^\x00-\x7F]/u', $candidate) === 1) {
            $normalized = preg_replace('/[^\x00-\x7F]+/u', '?', $candidate);
            if (is_string($normalized) && $normalized !== '') {
                return $normalized;
            }
        }

        if ($fallback !== '') {
            return $fallback;
        }

        return $value;
    }

    private static function shouldApplyAsciiFallback(string $value, string $original): bool
    {
        if ($value === '') {
            return false;
        }

        if (preg_match('/[^\x00-\x7F]/', $value) === 1) {
            return true;
        }

        foreach (["'", '"', '`', '^', '~', '?'] as $marker) {
            if (substr_count($value, $marker) > substr_count($original, $marker)) {
                return true;
            }
        }

        return false;
    }

    private static function asciiTransliterationFallback(string $value, string $encoding): string
    {
        if ($value === '') {
            return '';
        }

        $utf8 = $encoding === 'UTF-8'
            ? $value
            : (function_exists('iconv') ? @iconv($encoding, 'UTF-8//IGNORE', $value) : false);
        if (!is_string($utf8) || $utf8 === '') {
            $utf8 = $value;
        }

        if (class_exists('\Transliterator')) {
            $transliterator = \Transliterator::create('Any-Latin; Latin-ASCII');
            if ($transliterator instanceof \Transliterator) {
                $transliterated = $transliterator->transliterate($utf8);
                if (is_string($transliterated) && $transliterated !== '') {
                    $utf8 = $transliterated;
                }
            }
        }

        if (class_exists('Normalizer')) {
            $normalized = Normalizer::normalize($utf8, Normalizer::FORM_KD);
            if (is_string($normalized) && $normalized !== '') {
                $utf8 = $normalized;
            }
        }

        $stripped = preg_replace('/\p{Mn}+/u', '', $utf8);
        if (is_string($stripped) && $stripped !== '') {
            $utf8 = $stripped;
        }

        $utf8 = self::stripDetachedAccentMarkers($utf8);
        $utf8 = strtr($utf8, self::ASCII_FALLBACK_REPLACEMENTS);

        $entities = htmlentities($utf8, ENT_NOQUOTES, 'UTF-8');
        if ($entities !== false && $entities !== '') {
            $entities = preg_replace('/&([a-zA-Z]+?)(?:acute|breve|caron|cedil|circ|grave|lig|macr|orn|ring|slash|th|tilde|uml);/u', '$1', $entities);
            if (is_string($entities) && $entities !== '') {
                $entities = preg_replace('/&[^;]+;/', '', $entities);
                if (is_string($entities) && $entities !== '') {
                    $decoded = html_entity_decode($entities, ENT_NOQUOTES, 'UTF-8');
                    if ($decoded !== '') {
                        $utf8 = $decoded;
                    }
                }
            }
        }

        $ascii = preg_replace('/[^\x00-\x7F]+/u', '?', $utf8);
        if (is_string($ascii) && $ascii !== '') {
            return $ascii;
        }

        return $utf8;
    }

    private static function stripDetachedAccentMarkers(string $value): string
    {
        if ($value === '') {
            return '';
        }

        $patterns = [
            "/([A-Za-z])\xC2\xB4/",
            "/([A-Za-z])\xB4/",
            "/([A-Za-z])\xC2\xA8/",
            "/([A-Za-z])\xA8/",
            "/([A-Za-z])\xCB\x86/",
            "/([A-Za-z])\xCB\x98/",
            "/([A-Za-z])\xCB\x9A/",
            "/([A-Za-z])\xCB\x9B/",
            "/([A-Za-z])\xCB\x9C/",
            "/([A-Za-z])\xCB\x9D/",
        ];

        $cleaned = $value;
        foreach ($patterns as $pattern) {
            $replaced = preg_replace($pattern, '$1', $cleaned);
            if (is_string($replaced) && $replaced !== '') {
                $cleaned = $replaced;
            }
        }

        return $cleaned;
    }

    private static function uppercaseFirst(string $value, string $encoding): string
    {
        if ($value === '') {
            return '';
        }

        if (function_exists('mb_substr')) {
            $first = mb_substr($value, 0, 1, $encoding);
            $rest = mb_substr($value, 1, null, $encoding);

            if ($first !== false && $rest !== false) {
                $upper = function_exists('mb_strtoupper')
                    ? mb_strtoupper($first, $encoding)
                    : strtoupper($first);

                return $upper . $rest;
            }
        }

        $first = substr($value, 0, 1);
        $rest = substr($value, 1);

        return strtoupper($first) . $rest;
    }

    private static function lowercaseString(string $value, string $encoding): string
    {
        if ($value === '') {
            return '';
        }

        if (function_exists('mb_strtolower')) {
            return mb_strtolower($value, $encoding);
        }

        return strtolower($value);
    }

    private static function normalizeMode(string $mode): string
    {
        $normalized = strtolower($mode);
        if (!in_array($normalized, self::VALID_MODES, true)) {
            throw new InvalidArgumentException('Invalid mode provided.');
        }

        return $normalized;
    }

    private static function convertEncoding(string $value, string $toEncoding, string $fromEncoding): string
    {
        if (function_exists('mb_convert_encoding')) {
            try {
                $converted = mb_convert_encoding($value, $toEncoding, $fromEncoding);
            } catch (ValueError) {
                $converted = false;
            }

            if (is_string($converted)) {
                return $converted;
            }
        }

        if (function_exists('iconv')) {
            $converted = @iconv($fromEncoding, $toEncoding, $value);
            if ($converted !== false) {
                return $converted;
            }
        }

        throw new RuntimeException(sprintf('Unable to convert string encoding from %s to %s.', $fromEncoding, $toEncoding));
    }

    private static function baseEncoding(string $encoding): string
    {
        $normalized = trim($encoding);
        if ($normalized === '') {
            return $encoding;
        }

        $delimiter = strpos($normalized, '//');
        if ($delimiter === false) {
            return $normalized;
        }

        $base = substr($normalized, 0, $delimiter);
        $base = trim((string) $base);

        return $base === '' ? $normalized : $base;
    }

    private static function normalizeEncoding(string $encoding): string
    {
        $normalized = trim($encoding);
        if ($normalized === '') {
            throw new InvalidArgumentException('Encoding cannot be empty.');
        }

        return $normalized;
    }

    private static function encodeWindowsSegment(string $segment, bool $double_encode): string
    {
        if ($segment === '') {
            return '';
        }

        $characters = self::splitCharacters($segment);
        if ($characters === []) {
            return '';
        }

        $count = count($characters);
        $trailing = 0;
        for ($index = $count - 1; $index >= 0; $index--) {
            $char = $characters[$index];
            if ($char === ' ' || $char === '.') {
                $trailing++;
                continue;
            }

            break;
        }

        $trimmed = rtrim($segment, " .");
        $is_reserved = $trimmed !== ''
            && in_array(strtoupper($trimmed), self::WINDOWS_RESERVED_NAMES, true);

        $encoded = '';
        for ($index = 0; $index < $count; $index++) {
            $char = $characters[$index];

            if ($char === '%' && self::isPercentEncodedSequence($characters, $index)) {
                $first = strtoupper($characters[$index + 1]);
                $second = strtoupper($characters[$index + 2]);

                $encoded .= $double_encode
                    ? '%25' . $first . $second
                    : '%' . $first . $second;

                $index += 2;

                if ($double_encode
                    && isset($characters[$index + 1], $characters[$index + 2])
                    && strtoupper($characters[$index + 1]) === $first
                    && strtoupper($characters[$index + 2]) === $second
                ) {
                    $index += 2;
                }

                continue;
            }

            $should_encode = false;

            if (in_array($char, ['%', '<', '>', ':', '"', '/', '\\', '|', '?', '*'], true)) {
                $should_encode = true;
            } elseif (strlen($char) === 1) {
                $code = ord($char);
                if ($code < 0x20 || $code === 0x7F) {
                    $should_encode = true;
                }
            }

            if ($trailing > 0 && $index >= $count - $trailing && ($char === ' ' || $char === '.')) {
                $should_encode = true;
            }

            if ($is_reserved && $index === 0) {
                $should_encode = true;
            }

            $encoded .= $should_encode
                ? self::percentEncodeBytes($char, $double_encode)
                : $char;
        }

        return $encoded;
    }

    private static function decodeWindowsSegment(string $segment): string
    {
        if ($segment === '') {
            return '';
        }

        return self::decodePercentEncoded($segment);
    }

    private static function encodeWindowsPathString(string $path, bool $double_encode): string
    {
        if ($path === '') {
            return '';
        }

        $prefix = '';
        $remaining = $path;

        if (str_starts_with($remaining, '\\\\')) {
            $prefix = '\\\\';
            $remaining = substr($remaining, 2);
        } elseif (preg_match('/^([A-Za-z]):/', $remaining, $drive_match) === 1) {
            $prefix = $drive_match[1] . ':';
            $remaining = substr($remaining, 2);
            if (str_starts_with($remaining, '\\')) {
                $prefix .= '\\';
                $remaining = substr($remaining, 1);
            }
        }

        $result = $prefix;
        $buffer = '';
        $length = strlen($remaining);
        for ($i = 0; $i < $length; $i++) {
            $char = $remaining[$i];
            if ($char === '\\') {
                if ($buffer !== '') {
                    $result .= self::encodeWindowsSegment($buffer, $double_encode);
                    $buffer = '';
                }

                $result .= '\\';
            } else {
                $buffer .= $char;
            }
        }

        if ($buffer !== '') {
            $result .= self::encodeWindowsSegment($buffer, $double_encode);
        }

        return $result;
    }

    private static function decodeWindowsPathString(string $path): string
    {
        if ($path === '') {
            return '';
        }

        $prefix = '';
        $remaining = $path;

        if (str_starts_with($remaining, '\\\\')) {
            $prefix = '\\\\';
            $remaining = substr($remaining, 2);
        } elseif (preg_match('/^([A-Za-z]):/', $remaining, $drive_match) === 1) {
            $prefix = $drive_match[1] . ':';
            $remaining = substr($remaining, 2);
            if (str_starts_with($remaining, '\\')) {
                $prefix .= '\\';
                $remaining = substr($remaining, 1);
            }
        }

        $result = $prefix;
        $buffer = '';
        $length = strlen($remaining);
        for ($i = 0; $i < $length; $i++) {
            $char = $remaining[$i];
            if ($char === '\\') {
                if ($buffer !== '') {
                    $result .= self::decodeWindowsSegment($buffer);
                    $buffer = '';
                }

                $result .= '\\';
            } else {
                $buffer .= $char;
            }
        }

        if ($buffer !== '') {
            $result .= self::decodeWindowsSegment($buffer);
        }

        return $result;
    }

    private static function encodeUnixSegment(string $segment, bool $macos, bool $double_encode): string
    {
        if ($segment === '') {
            return '';
        }

        $characters = self::splitCharacters($segment);
        if ($characters === []) {
            return '';
        }

        $count = count($characters);
        $encoded = '';

        for ($index = 0; $index < $count; $index++) {
            $char = $characters[$index];

            if ($char === '%' && self::isPercentEncodedSequence($characters, $index)) {
                $first = strtoupper($characters[$index + 1]);
                $second = strtoupper($characters[$index + 2]);

                $encoded .= $double_encode
                    ? '%25' . $first . $second
                    : '%' . $first . $second;

                $index += 2;

                if ($double_encode
                    && isset($characters[$index + 1], $characters[$index + 2])
                    && strtoupper($characters[$index + 1]) === $first
                    && strtoupper($characters[$index + 2]) === $second
                ) {
                    $index += 2;
                }

                continue;
            }

            $should_encode = $char === '/' || $char === "\0" || $char === '%';

            if ($macos && $char === ':') {
                $should_encode = true;
            }

            $encoded .= $should_encode
                ? self::percentEncodeBytes($char, $double_encode)
                : $char;
        }

        return $encoded;
    }

    private static function decodeUnixSegment(string $segment): string
    {
        if ($segment === '') {
            return '';
        }

        return self::decodePercentEncoded($segment);
    }

    private static function encodeUnixPathString(string $path, bool $macos, bool $double_encode): string
    {
        if ($path === '') {
            return '';
        }

        $result = '';
        $buffer = '';
        $length = strlen($path);
        for ($i = 0; $i < $length; $i++) {
            $char = $path[$i];
            if ($char === '/') {
                if ($buffer !== '') {
                    $result .= self::encodeUnixSegment($buffer, $macos, $double_encode);
                    $buffer = '';
                }

                $result .= '/';
            } else {
                $buffer .= $char;
            }
        }

        if ($buffer !== '') {
            $result .= self::encodeUnixSegment($buffer, $macos, $double_encode);
        }

        return $result;
    }

    private static function decodeUnixPathString(string $path): string
    {
        if ($path === '') {
            return '';
        }

        $result = '';
        $buffer = '';
        $length = strlen($path);
        for ($i = 0; $i < $length; $i++) {
            $char = $path[$i];
            if ($char === '/') {
                if ($buffer !== '') {
                    $result .= self::decodeUnixSegment($buffer);
                    $buffer = '';
                }

                $result .= '/';
            } else {
                $buffer .= $char;
            }
        }

        if ($buffer !== '') {
            $result .= self::decodeUnixSegment($buffer);
        }

        return $result;
    }

    private static function encodeGenericSegment(string $segment, bool $double_encode): string
    {
        if ($segment === '') {
            return '';
        }

        return self::encodeWindowsSegment($segment, $double_encode);
    }

    private static function decodeGenericSegment(string $segment): string
    {
        if ($segment === '') {
            return '';
        }

        return self::decodePercentEncoded($segment);
    }

    private static function encodeGenericPathString(string $path, bool $double_encode): string
    {
        if ($path === '') {
            return '';
        }

        $result = '';
        $buffer = '';
        $length = strlen($path);
        for ($i = 0; $i < $length; $i++) {
            $char = $path[$i];
            if ($char === '/') {
                if ($buffer !== '') {
                    $result .= self::encodeGenericSegment($buffer, $double_encode);
                    $buffer = '';
                }

                $result .= '/';
            } else {
                $buffer .= $char;
            }
        }

        if ($buffer !== '') {
            $result .= self::encodeGenericSegment($buffer, $double_encode);
        }

        return $result;
    }

    /**
     * @param array<int, string> $characters
     */
    private static function isPercentEncodedSequence(array $characters, int $index): bool
    {
        if (!isset($characters[$index + 1], $characters[$index + 2])) {
            return false;
        }

        $first = $characters[$index + 1];
        $second = $characters[$index + 2];

        if (strlen($first) !== 1 || strlen($second) !== 1) {
            return false;
        }

        return self::isUppercaseHexDigit($first) && self::isUppercaseHexDigit($second);
    }

    private static function isUppercaseHexDigit(string $char): bool
    {
        if ($char === '') {
            return false;
        }

        $code = ord($char);

        return ($code >= 0x30 && $code <= 0x39)
            || ($code >= 0x41 && $code <= 0x46);
    }

    private static function decodeGenericPathString(string $path): string
    {
        if ($path === '') {
            return '';
        }

        $result = '';
        $buffer = '';
        $length = strlen($path);
        for ($i = 0; $i < $length; $i++) {
            $char = $path[$i];
            if ($char === '/') {
                if ($buffer !== '') {
                    $result .= self::decodeGenericSegment($buffer);
                    $buffer = '';
                }

                $result .= '/';
            } else {
                $buffer .= $char;
            }
        }

        if ($buffer !== '') {
            $result .= self::decodeGenericSegment($buffer);
        }

        return $result;
    }

    private static function percentEncodeBytes(string $char, bool $double_encode): string
    {
        if ($char === '') {
            return '';
        }

        $bytes = str_split($char);
        $encoded = '';
        foreach ($bytes as $byte) {
            $encoded .= sprintf('%%%02X', ord($byte));
        }

        if ($double_encode && $char !== '/') {
            $encoded = str_replace('%', '%25', $encoded);
        }

        return $encoded;
    }

    private static function decodePercentEncoded(string $value): string
    {
        if ($value === '') {
            return '';
        }

        $decoded = preg_replace_callback(
            '/%([0-9A-Fa-f]{2})/',
            static function (array $matches): string {
                return chr(hexdec($matches[1]));
            },
            $value
        );

        return is_string($decoded) ? $decoded : $value;
    }

    private static function sanitizeWindowsSegment(string $segment): string
    {
        $segment = str_replace("\0", '', $segment);

        $replaced = preg_replace('~[<>:"/\\\\|?*]~', '_', $segment);
        if (is_string($replaced)) {
            $segment = $replaced;
        }

        $removed_controls = preg_replace('~[\x00-\x1F\x7F]~', '', $segment);
        if (is_string($removed_controls)) {
            $segment = $removed_controls;
        }

        $segment = trim($segment, " .");

        if ($segment === '' || $segment === '.' || $segment === '..') {
            $segment = '_';
        }

        $upper = strtoupper($segment);
        if (in_array($upper, self::WINDOWS_RESERVED_NAMES, true)) {
            $segment = '_' . $segment;
        }

        if (strlen($segment) > 255) {
            $segment = substr($segment, 0, 255);
        }

        return $segment;
    }

    private static function sanitizeUnixSegment(string $segment, bool $macos): string
    {
        $segment = str_replace("\0", '', $segment);
        $segment = str_replace('\\', '_', $segment);
        $segment = str_replace('/', '_', $segment);
        if ($macos) {
            $segment = str_replace(':', '_', $segment);
        }

        $removed_controls = preg_replace('~[\x00-\x1F\x7F]~', '', $segment);
        if (is_string($removed_controls)) {
            $segment = $removed_controls;
        }

        if (trim($segment) === '' || $segment === '.' || $segment === '..') {
            $segment = '_';
        }

        if (strlen($segment) > 255) {
            $segment = substr($segment, 0, 255);
        }

        return $segment;
    }

    private static function sanitizeGenericSegment(string $segment): string
    {
        $segment = self::sanitizeWindowsSegment($segment);
        $segment = str_replace(':', '_', $segment);
        $segment = str_replace('/', '_', $segment);
        $segment = str_replace('\\', '_', $segment);

        if (trim($segment) === '') {
            $segment = '_';
        }

        if (strlen($segment) > 255) {
            $segment = substr($segment, 0, 255);
        }

        return $segment;
    }

    private function graphemeLengthOrFallback(): int
    {
        if (function_exists('grapheme_strlen')) {
            $length = grapheme_strlen($this->value);
            if ($length !== false) {
                return $length;
            }
        }

        $grapheme_count = preg_match_all('/\\X/u', $this->value);
        if ($grapheme_count !== false) {
            return $grapheme_count;
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
