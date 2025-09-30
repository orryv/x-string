<?php

declare(strict_types=1);

namespace Orryv;

use InvalidArgumentException;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;
use Orryv\XString\Regex;
use Orryv\XString\Compute\Similarity;
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

    public function asBytes(string $encoding = self::DEFAULT_ENCODING): self
    {
        return $this->withMode('bytes', $encoding);
    }

    public function asCodepoints(string $encoding = self::DEFAULT_ENCODING): self
    {
        return $this->withMode('codepoints', $encoding);
    }

    public function asGraphemes(string $encoding = self::DEFAULT_ENCODING): self
    {
        return $this->withMode('graphemes', $encoding);
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

    public function substr(int $start, ?int $length = null): self
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

    public function repeat(int $times): self
    {
        if ($times < 0) {
            throw new InvalidArgumentException('Repeat count must be greater than or equal to 0.');
        }

        if ($times === 0) {
            return new self('', $this->mode, $this->encoding);
        }

        return new self(str_repeat($this->value, $times), $this->mode, $this->encoding);
    }

    public function reverse(): self
    {
        $units = self::splitByMode($this->value, $this->mode, $this->encoding);
        if ($units === []) {
            return new self('', $this->mode, $this->encoding);
        }

        $units = array_reverse($units);

        return new self(implode('', $units), $this->mode, $this->encoding);
    }

    public function shuffle(): self
    {
        $units = self::splitByMode($this->value, $this->mode, $this->encoding);
        if (count($units) <= 1) {
            return new self($this->value, $this->mode, $this->encoding);
        }

        shuffle($units);

        return new self(implode('', $units), $this->mode, $this->encoding);
    }

    public function slug(Newline|HtmlTag|string $separator = '-'): self
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

    public function fileNameSlug(Newline|HtmlTag|string $separator = '-'): self
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

    public function insertAtInterval(Newline|HtmlTag|Regex|string $insert, int $interval): self
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

    public function trim(bool $newline = true, bool $space = true, bool $tab = true): self
    {
        return $this->trimInternal(true, true, $newline, $space, $tab);
    }

    public function ltrim(bool $newline = true, bool $space = true, bool $tab = true): self
    {
        return $this->trimInternal(true, false, $newline, $space, $tab);
    }

    public function rtrim(bool $newline = true, bool $space = true, bool $tab = true): self
    {
        return $this->trimInternal(false, true, $newline, $space, $tab);
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $search
     */
    public function replace(
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
                $config = $search_value->getStartsWithConfig();
                if ($config !== null) {
                    $result = self::replaceLinesStartingWith(
                        $result,
                        $search_value,
                        $replacement,
                        $remaining,
                        $reversed
                    );

                    continue;
                }
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
    public function replaceFirst(
        HtmlTag|Newline|Regex|Stringable|string|array $search,
        HtmlTag|Newline|Regex|Stringable|string $replace
    ): self {
        return $this->replace($search, $replace, 1);
    }

    public function replaceLast(
        HtmlTag|Newline|Regex|Stringable|string|array $search,
        HtmlTag|Newline|Regex|Stringable|string $replace
    ): self {
        return $this->replace($search, $replace, 1, true);
    }

    public function similarityScore(
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

    private static function replaceLinesStartingWith(
        string $subject,
        Newline $newline,
        string $replacement,
        int &$remaining,
        bool $reversed
    ): string {
        if ($remaining <= 0) {
            return $subject;
        }

        $config = $newline->getStartsWithConfig();
        if ($config === null) {
            return $subject;
        }

        $line_break = (string) $newline;
        if ($line_break === '') {
            return $subject;
        }

        $prefix = $config['prefix'];
        $trim = $config['trim'];

        $segments = explode($line_break, $subject);
        $has_trailing_break = str_ends_with($subject, $line_break);
        if ($has_trailing_break) {
            array_pop($segments);
        }

        $indexes = $reversed
            ? array_reverse(array_keys($segments))
            : array_keys($segments);

        foreach ($indexes as $index) {
            if ($remaining === 0) {
                break;
            }

            $line = $segments[$index];
            $comparison = $trim ? ltrim($line, " \t") : $line;

            if (!str_starts_with($comparison, $prefix)) {
                continue;
            }

            $segments[$index] = $replacement;
            $remaining--;
        }

        $result = implode($line_break, $segments);

        if ($has_trailing_break) {
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

        if (preg_match('/[^\x00-\x7F]/', $value) === 1) {
            $value = self::asciiTransliterationFallback($value, $encoding);
        }

        return $value;
    }

    private static function asciiTransliterationFallback(string $value, string $encoding): string
    {
        if ($value === '') {
            return '';
        }

        $entities = htmlentities($value, ENT_NOQUOTES, $encoding);
        if ($entities === false) {
            return $value;
        }

        if ($entities === '') {
            return '';
        }

        $entities = preg_replace(
            '/&([a-zA-Z]+?)(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);/u',
            '$1',
            $entities,
        );
        if ($entities === null) {
            return $value;
        }

        $entities = preg_replace('/&[^;]+;/', '', $entities);
        if ($entities === null) {
            return $value;
        }

        $decoded = html_entity_decode($entities, ENT_NOQUOTES, 'UTF-8');
        if ($decoded === '') {
            return $value;
        }

        return $decoded;
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
