<?php

declare(strict_types=1);

namespace Orryv\XString\Compute;

use InvalidArgumentException;

final class Similarity
{
    /**
     * @var array<int, string>
     */
    private const SUPPORTED_ALGORITHMS = [
        'levenshtein',
        'damerau-levenshtein',
        'jaro-winkler',
        'lcs-myers',
        'ratcliff-obershelp',
        'jaccard',
        'sorensen-dice',
        'cosine-ngrams',
        'monge-elkan',
        'soft-tfidf',
        'github-style',
    ];

    /**
     * @param array<string, mixed> $options
     */
    public static function compute(
        string $left,
        string $right,
        string $algorithm,
        array $options,
        string $mode,
        string $encoding
    ): float {
        if (!in_array($algorithm, self::SUPPORTED_ALGORITHMS, true)) {
            throw new InvalidArgumentException('Unsupported similarity algorithm.');
        }

        $normalizedOptions = self::normalizeOptions($options, $algorithm, $mode);

        $leftPrepared = self::prepareInput($left, $normalizedOptions, $encoding);
        $rightPrepared = self::prepareInput($right, $normalizedOptions, $encoding);

        switch ($algorithm) {
            case 'levenshtein':
                $score = self::scoreLevenshtein($leftPrepared['string'], $rightPrepared['string'], $normalizedOptions, $encoding);
                break;

            case 'damerau-levenshtein':
                $score = self::scoreDamerauLevenshtein($leftPrepared['characters'], $rightPrepared['characters'], $normalizedOptions);
                break;

            case 'jaro-winkler':
                $score = self::scoreJaroWinkler($leftPrepared['characters'], $rightPrepared['characters'], $normalizedOptions);
                break;

            case 'lcs-myers':
                $score = self::scoreLcsMyers($leftPrepared['tokens'], $rightPrepared['tokens'], $normalizedOptions);
                break;

            case 'ratcliff-obershelp':
                $score = self::scoreRatcliffObershelp($leftPrepared['tokens'], $rightPrepared['tokens']);
                break;

            case 'jaccard':
                $score = self::scoreJaccard($leftPrepared['tokens'], $rightPrepared['tokens'], $normalizedOptions);
                break;

            case 'sorensen-dice':
                $score = self::scoreSorensenDice($leftPrepared['tokens'], $rightPrepared['tokens'], $normalizedOptions);
                break;

            case 'cosine-ngrams':
                $score = self::scoreCosineNgrams($leftPrepared, $rightPrepared, $normalizedOptions);
                break;

            case 'monge-elkan':
                $score = self::scoreMongeElkan($leftPrepared['tokens'], $rightPrepared['tokens'], $normalizedOptions, $encoding);
                break;

            case 'soft-tfidf':
                $score = self::scoreSoftTfidf($leftPrepared['tokens'], $rightPrepared['tokens'], $normalizedOptions, $encoding);
                break;

            case 'github-style':
                $score = self::scoreGithubStyle($leftPrepared['tokens'], $rightPrepared['tokens'], $normalizedOptions);
                break;

            default:
                throw new InvalidArgumentException('Unsupported similarity algorithm.');
        }

        $score = max(0.0, min(1.0, $score));

        if (abs($score - 1.0) < 1e-12) {
            $score = 1.0;
        } elseif (abs($score) < 1e-12) {
            $score = 0.0;
        }

        $threshold = (float) $normalizedOptions['threshold'];
        if ($threshold > 0.0 && $score < $threshold) {
            return 0.0;
        }

        return $score;
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    private static function normalizeOptions(array $options, string $algorithm, string $mode): array
    {
        $defaults = [
            'granularity' => 'token',
            'case_sensitive' => false,
            'normalize_whitespace' => true,
            'strip_punctuation' => null,
            'threshold' => 0.0,
            'tokenizer' => null,
            'mode' => $mode,
        ];

        $algorithmDefaults = [];
        switch ($algorithm) {
            case 'jaro-winkler':
                $algorithmDefaults = [
                    'prefix_scale' => 0.1,
                    'prefix_limit' => 4,
                ];
                break;

            case 'lcs-myers':
                $algorithmDefaults = [
                    'weight_common_prefix' => 0.0,
                ];
                break;

            case 'ratcliff-obershelp':
                $algorithmDefaults = [
                    'symmetric' => true,
                ];
                break;

            case 'jaccard':
            case 'sorensen-dice':
                $algorithmDefaults = [
                    'token_set' => true,
                ];
                break;

            case 'cosine-ngrams':
                $algorithmDefaults = [
                    'n' => 3,
                    'weighting' => 'binary',
                ];
                break;

            case 'monge-elkan':
                $algorithmDefaults = [
                    'secondary_metric' => 'jaro-winkler',
                    'tau' => 0.9,
                ];
                break;

            case 'soft-tfidf':
                $algorithmDefaults = [
                    'secondary_metric' => 'jaro-winkler',
                    'tau' => 0.9,
                    'weighting' => 'tfidf',
                ];
                break;

            case 'github-style':
                $algorithmDefaults = [
                    'prefix_scale' => 0.05,
                    'prefix_limit' => 3,
                ];
                break;

            case 'levenshtein':
                $algorithmDefaults = [
                    'transposition_cost' => 2,
                ];
                break;

            case 'damerau-levenshtein':
                $algorithmDefaults = [
                    'transposition_cost' => 1,
                ];
                break;
        }

        $normalized = array_merge($defaults, $algorithmDefaults, $options);

        $granularity = strtolower((string) $normalized['granularity']);
        if (!in_array($granularity, ['token', 'word', 'character'], true)) {
            throw new InvalidArgumentException('Invalid granularity option.');
        }
        $normalized['granularity'] = $granularity;

        if ($normalized['strip_punctuation'] === null) {
            $normalized['strip_punctuation'] = $granularity !== 'character';
        } else {
            $normalized['strip_punctuation'] = (bool) $normalized['strip_punctuation'];
        }

        $normalized['case_sensitive'] = (bool) $normalized['case_sensitive'];
        $normalized['normalize_whitespace'] = (bool) $normalized['normalize_whitespace'];
        $normalized['threshold'] = (float) $normalized['threshold'];
        $normalized['mode'] = (string) $normalized['mode'];

        if (isset($normalized['tokenizer']) && $normalized['tokenizer'] !== null && !is_callable($normalized['tokenizer'])) {
            throw new InvalidArgumentException('Tokenizer must be callable or null.');
        }

        if (isset($normalized['prefix_limit'])) {
            $normalized['prefix_limit'] = max(0, (int) $normalized['prefix_limit']);
        }

        if (isset($normalized['prefix_scale'])) {
            $normalized['prefix_scale'] = (float) $normalized['prefix_scale'];
        }

        if (isset($normalized['tau'])) {
            $normalized['tau'] = max(0.0, min(1.0, (float) $normalized['tau']));
        }

        if (isset($normalized['weight_common_prefix'])) {
            $normalized['weight_common_prefix'] = max(0.0, (float) $normalized['weight_common_prefix']);
        }

        if (isset($normalized['token_set'])) {
            $normalized['token_set'] = (bool) $normalized['token_set'];
        }

        if (isset($normalized['n'])) {
            $normalized['n'] = max(1, (int) $normalized['n']);
        }

        if (isset($normalized['weighting'])) {
            $weighting = strtolower((string) $normalized['weighting']);
            $allowedWeightings = ['binary', 'tf', 'log', 'augmented', 'double-normalization-0.5', 'tfidf'];
            if (!in_array($weighting, $allowedWeightings, true)) {
                throw new InvalidArgumentException('Unsupported weighting strategy.');
            }

            $normalized['weighting'] = $weighting;
        }

        if (isset($normalized['transposition_cost'])) {
            $normalized['transposition_cost'] = max(0, (int) $normalized['transposition_cost']);
        }

        if (isset($normalized['secondary_metric'])) {
            $secondary = strtolower((string) $normalized['secondary_metric']);
            if (!in_array($secondary, self::SUPPORTED_ALGORITHMS, true)) {
                throw new InvalidArgumentException('Unsupported secondary similarity algorithm.');
            }

            if (in_array($secondary, ['monge-elkan', 'soft-tfidf'], true)) {
                throw new InvalidArgumentException('Composite algorithms cannot be used as secondary metrics.');
            }

            $normalized['secondary_metric'] = $secondary;
        }

        return $normalized;
    }

    /**
     * @param array<string, mixed> $options
     * @return array{string: string, tokens: array<int, string>, characters: array<int, string>}
     */
    private static function prepareInput(string $value, array $options, string $encoding): array
    {
        $normalized = self::normalizeString($value, $options, $encoding);

        /** @var array<int, string> $tokens */
        $tokens = self::tokenize($normalized, $options, $encoding);

        /** @var array<int, string> $characters */
        $characters = self::splitCharacters($normalized, (string) $options['mode'], $encoding);

        return [
            'string' => $normalized,
            'tokens' => $tokens,
            'characters' => $characters,
        ];
    }

    /**
     * @param array<string, mixed> $options
     */
    private static function normalizeString(string $value, array $options, string $encoding): string
    {
        $result = $options['case_sensitive'] ? $value : self::toLower($value, $encoding);

        if ($options['strip_punctuation']) {
            $result = (string) preg_replace('/[\p{P}\p{S}]+/u', ' ', $result);
        }

        if ($options['normalize_whitespace']) {
            $normalizedWhitespace = preg_replace('/\s+/u', ' ', $result);
            $result = $normalizedWhitespace === null ? $result : trim($normalizedWhitespace);
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $options
     * @return array<int, string>
     */
    private static function tokenize(string $value, array $options, string $encoding): array
    {
        if ($value === '') {
            return [];
        }

        if ($options['tokenizer'] !== null) {
            /** @var callable $tokenizer */
            $tokenizer = $options['tokenizer'];
            $tokens = $tokenizer($value, $options);
            if (!is_array($tokens)) {
                throw new InvalidArgumentException('Custom tokenizer must return an array.');
            }

            $normalizedTokens = [];
            foreach ($tokens as $token) {
                if (!is_string($token)) {
                    $normalizedTokens[] = (string) $token;
                } else {
                    $normalizedTokens[] = $options['case_sensitive'] ? $token : self::toLower($token, $encoding);
                }
            }

            return $normalizedTokens;
        }

        switch ($options['granularity']) {
            case 'character':
                return self::splitCharacters($value, (string) $options['mode'], $encoding);

            case 'word':
                $tokens = preg_split('/[^\p{L}\p{N}]+/u', $value, -1, PREG_SPLIT_NO_EMPTY);
                break;

            case 'token':
            default:
                $tokens = preg_split('/\s+/u', $value, -1, PREG_SPLIT_NO_EMPTY);
                break;
        }

        if ($tokens === false) {
            return [];
        }

        $normalized = [];
        foreach ($tokens as $token) {
            $normalized[] = $options['case_sensitive'] ? $token : self::toLower($token, $encoding);
        }

        return $normalized;
    }

    /**
     * @return array<int, string>
     */
    private static function splitCharacters(string $value, string $mode, string $encoding): array
    {
        if ($value === '') {
            return [];
        }

        if ($mode === 'bytes') {
            return str_split($value);
        }

        if ($mode === 'graphemes' && function_exists('grapheme_split')) {
            $graphemes = grapheme_split($value);
            if (is_array($graphemes)) {
                return $graphemes;
            }
        }

        $chars = preg_split('//u', $value, -1, PREG_SPLIT_NO_EMPTY);
        if ($chars === false) {
            return [];
        }

        return $chars;
    }

    /**
     * @param array<string, mixed> $options
     */
    private static function scoreLevenshtein(string $left, string $right, array $options, string $encoding): float
    {
        if ($left === '' && $right === '') {
            return 1.0;
        }

        $distance = levenshtein($left, $right);

        $length = max(self::stringLength($left, (string) $options['mode'], $encoding), self::stringLength($right, (string) $options['mode'], $encoding));
        if ($length === 0) {
            return 1.0;
        }

        return 1.0 - ($distance / $length);
    }

    /**
     * @param array<int, string> $left
     * @param array<int, string> $right
     * @param array<string, mixed> $options
     */
    private static function scoreDamerauLevenshtein(array $left, array $right, array $options): float
    {
        if ($left === $right) {
            return 1.0;
        }

        $lenLeft = count($left);
        $lenRight = count($right);

        if ($lenLeft === 0 && $lenRight === 0) {
            return 1.0;
        }

        if ($lenLeft === 0 || $lenRight === 0) {
            return 0.0;
        }

        $distance = self::damerauLevenshteinDistance($left, $right, (int) $options['transposition_cost']);
        $length = max($lenLeft, $lenRight);

        return 1.0 - ($distance / $length);
    }

    /**
     * @param array<int, string> $left
     * @param array<int, string> $right
     * @param array<string, mixed> $options
     */
    private static function scoreJaroWinkler(array $left, array $right, array $options): float
    {
        $lenLeft = count($left);
        $lenRight = count($right);

        if ($lenLeft === 0 && $lenRight === 0) {
            return 1.0;
        }

        if ($lenLeft === 0 || $lenRight === 0) {
            return 0.0;
        }

        if ($left === $right) {
            return 1.0;
        }

        $matchWindow = max(0, intdiv(max($lenLeft, $lenRight), 2) - 1);

        $matchesLeft = array_fill(0, $lenLeft, false);
        $matchesRight = array_fill(0, $lenRight, false);

        $matches = 0;

        for ($i = 0; $i < $lenLeft; $i++) {
            $start = max(0, $i - $matchWindow);
            $end = min($lenRight - 1, $i + $matchWindow);

            for ($j = $start; $j <= $end; $j++) {
                if ($matchesRight[$j]) {
                    continue;
                }

                if ($left[$i] !== $right[$j]) {
                    continue;
                }

                $matchesLeft[$i] = true;
                $matchesRight[$j] = true;
                $matches++;
                break;
            }
        }

        if ($matches === 0) {
            return 0.0;
        }

        $transpositions = 0;
        $k = 0;
        for ($i = 0; $i < $lenLeft; $i++) {
            if (!$matchesLeft[$i]) {
                continue;
            }

            while ($k < $lenRight && !$matchesRight[$k]) {
                $k++;
            }

            if ($k < $lenRight && $left[$i] !== $right[$k]) {
                $transpositions++;
            }

            $k++;
        }

        $transpositions /= 2.0;

        $m = $matches;

        $jaro = (
            ($m / $lenLeft) +
            ($m / $lenRight) +
            (($m - $transpositions) / $m)
        ) / 3.0;

        $prefixLimit = (int) $options['prefix_limit'];
        $prefixScale = (float) $options['prefix_scale'];

        $prefix = 0;
        $maxPrefix = min($prefixLimit, min($lenLeft, $lenRight));
        for ($i = 0; $i < $maxPrefix; $i++) {
            if ($left[$i] !== $right[$i]) {
                break;
            }
            $prefix++;
        }

        return $jaro + ($prefix * $prefixScale * (1.0 - $jaro));
    }

    /**
     * @param array<int, string> $left
     * @param array<int, string> $right
     * @param array<string, mixed> $options
     */
    private static function scoreLcsMyers(array $left, array $right, array $options): float
    {
        $lenLeft = count($left);
        $lenRight = count($right);

        if ($lenLeft === 0 && $lenRight === 0) {
            return 1.0;
        }

        if ($lenLeft === 0 || $lenRight === 0) {
            return 0.0;
        }

        $lcs = self::lcsLength($left, $right);
        $base = (2.0 * $lcs) / ($lenLeft + $lenRight);

        $prefixWeight = (float) $options['weight_common_prefix'];
        if ($prefixWeight <= 0.0) {
            return $base;
        }

        $commonPrefix = 0;
        $max = min($lenLeft, $lenRight);
        for ($i = 0; $i < $max; $i++) {
            if ($left[$i] !== $right[$i]) {
                break;
            }
            $commonPrefix++;
        }

        if ($commonPrefix === 0) {
            return $base;
        }

        return min(1.0, $base + ($commonPrefix * $prefixWeight / $max));
    }

    /**
     * @param array<int, string> $left
     * @param array<int, string> $right
     */
    private static function scoreRatcliffObershelp(array $left, array $right): float
    {
        $lenLeft = count($left);
        $lenRight = count($right);

        if ($lenLeft === 0 && $lenRight === 0) {
            return 1.0;
        }

        if ($lenLeft === 0 || $lenRight === 0) {
            return 0.0;
        }

        $matches = self::ratcliffObershelpMatches($left, $right);

        return (2.0 * $matches) / ($lenLeft + $lenRight);
    }

    /**
     * @param array<int, string> $left
     * @param array<int, string> $right
     * @param array<string, mixed> $options
     */
    private static function scoreJaccard(array $left, array $right, array $options): float
    {
        if ($left === [] && $right === []) {
            return 1.0;
        }

        if ($left === [] || $right === []) {
            return 0.0;
        }

        if ($options['token_set']) {
            $setLeft = array_unique($left);
            $setRight = array_unique($right);

            $intersection = array_intersect($setLeft, $setRight);
            $union = array_unique(array_merge($setLeft, $setRight));

            $intersectionCount = count($intersection);
            $unionCount = count($union);

            if ($unionCount === 0) {
                return 0.0;
            }

            return $intersectionCount / $unionCount;
        }

        $countsLeft = array_count_values($left);
        $countsRight = array_count_values($right);

        $intersection = 0;
        $union = 0;

        $allTokens = array_unique(array_merge(array_keys($countsLeft), array_keys($countsRight)));
        foreach ($allTokens as $token) {
            $intersection += min($countsLeft[$token] ?? 0, $countsRight[$token] ?? 0);
            $union += max($countsLeft[$token] ?? 0, $countsRight[$token] ?? 0);
        }

        if ($union === 0) {
            return 0.0;
        }

        return $intersection / $union;
    }

    /**
     * @param array<int, string> $left
     * @param array<int, string> $right
     * @param array<string, mixed> $options
     */
    private static function scoreSorensenDice(array $left, array $right, array $options): float
    {
        if ($left === [] && $right === []) {
            return 1.0;
        }

        if ($left === [] || $right === []) {
            return 0.0;
        }

        if ($options['token_set']) {
            $setLeft = array_unique($left);
            $setRight = array_unique($right);

            $intersection = array_intersect($setLeft, $setRight);

            $intersectionCount = count($intersection);
            $total = count($setLeft) + count($setRight);

            if ($total === 0) {
                return 0.0;
            }

            return (2 * $intersectionCount) / $total;
        }

        $countsLeft = array_count_values($left);
        $countsRight = array_count_values($right);

        $intersection = 0;
        $sumLeft = 0;
        $sumRight = 0;

        foreach ($countsLeft as $token => $count) {
            $sumLeft += $count;
            $intersection += min($count, $countsRight[$token] ?? 0);
        }

        foreach ($countsRight as $count) {
            $sumRight += $count;
        }

        if (($sumLeft + $sumRight) === 0) {
            return 0.0;
        }

        return (2 * $intersection) / ($sumLeft + $sumRight);
    }

    /**
     * @param array{string: string, tokens: array<int, string>, characters: array<int, string>} $left
     * @param array{string: string, tokens: array<int, string>, characters: array<int, string>} $right
     * @param array<string, mixed> $options
     */
    private static function scoreCosineNgrams(array $left, array $right, array $options): float
    {
        $n = (int) $options['n'];
        $weighting = (string) $options['weighting'];

        $tokensLeft = $options['granularity'] === 'character' ? $left['characters'] : $left['tokens'];
        $tokensRight = $options['granularity'] === 'character' ? $right['characters'] : $right['tokens'];

        $ngramsLeft = self::buildNgrams($tokensLeft, $n);
        $ngramsRight = self::buildNgrams($tokensRight, $n);

        if ($ngramsLeft === [] && $ngramsRight === []) {
            return 1.0;
        }

        if ($ngramsLeft === [] || $ngramsRight === []) {
            return 0.0;
        }

        return self::cosineSimilarity($ngramsLeft, $ngramsRight, $weighting);
    }

    /**
     * @param array<int, string> $left
     * @param array<int, string> $right
     * @param array<string, mixed> $options
     */
    private static function scoreMongeElkan(array $left, array $right, array $options, string $encoding): float
    {
        if ($left === [] && $right === []) {
            return 1.0;
        }

        if ($left === [] || $right === []) {
            return 0.0;
        }

        $secondary = (string) $options['secondary_metric'];
        $tau = (float) $options['tau'];

        $leftScores = self::mongeElkanDirection($left, $right, $secondary, $tau, $encoding);
        $rightScores = self::mongeElkanDirection($right, $left, $secondary, $tau, $encoding);

        return ($leftScores + $rightScores) / 2.0;
    }

    /**
     * @param array<int, string> $left
     * @param array<int, string> $right
     * @param array<string, mixed> $options
     */
    private static function scoreSoftTfidf(array $left, array $right, array $options, string $encoding): float
    {
        if ($left === [] && $right === []) {
            return 1.0;
        }

        if ($left === [] || $right === []) {
            return 0.0;
        }

        $weighting = (string) ($options['weighting'] ?? 'tfidf');
        $secondary = (string) $options['secondary_metric'];
        $tau = (float) $options['tau'];

        $weights = self::tokenWeights($left, $right, $weighting);
        $weightsLeft = $weights['left'];
        $weightsRight = $weights['right'];

        $normLeft = self::vectorNorm($weightsLeft);
        $normRight = self::vectorNorm($weightsRight);

        if ($normLeft === 0.0 || $normRight === 0.0) {
            return 0.0;
        }

        $score = 0.0;

        foreach ($weightsLeft as $tokenLeft => $weightLeft) {
            $bestScore = 0.0;
            $bestToken = null;

            foreach ($weightsRight as $tokenRight => $weightRight) {
                $similarity = self::computeSecondary($tokenLeft, $tokenRight, $secondary, $encoding);
                if ($similarity < $tau) {
                    continue;
                }

                if ($similarity > $bestScore) {
                    $bestScore = $similarity;
                    $bestToken = $tokenRight;
                }
            }

            if ($bestToken !== null) {
                $score += $weightLeft * ($weightsRight[$bestToken] ?? 0.0) * $bestScore;
            }
        }

        return min(1.0, $score / ($normLeft * $normRight));
    }

    /**
     * @param array<int, string> $left
     * @param array<int, string> $right
     * @param array<string, mixed> $options
     */
    private static function scoreGithubStyle(array $left, array $right, array $options): float
    {
        $lenLeft = count($left);
        $lenRight = count($right);

        if ($lenLeft === 0 && $lenRight === 0) {
            return 1.0;
        }

        if ($lenLeft === 0 || $lenRight === 0) {
            return 0.0;
        }

        $lcs = self::lcsLength($left, $right);
        $ratio = (2.0 * $lcs) / ($lenLeft + $lenRight);

        $prefixScale = (float) $options['prefix_scale'];
        $prefixLimit = (int) $options['prefix_limit'];
        $commonPrefix = 0;
        $maxPrefix = min($prefixLimit, min($lenLeft, $lenRight));

        for ($i = 0; $i < $maxPrefix; $i++) {
            if ($left[$i] !== $right[$i]) {
                break;
            }
            $commonPrefix++;
        }

        if ($commonPrefix === 0) {
            return $ratio;
        }

        return min(1.0, $ratio + ($commonPrefix * $prefixScale));
    }

    /**
     * @param array<int, string> $source
     * @param array<int, string> $target
     */
    private static function damerauLevenshteinDistance(array $source, array $target, int $transpositionCost): int
    {
        $lenSource = count($source);
        $lenTarget = count($target);

        $maxDistance = $lenSource + $lenTarget;

        $distance = array_fill(0, $lenSource + 2, array_fill(0, $lenTarget + 2, 0));
        $distance[0][0] = $maxDistance;

        for ($i = 0; $i <= $lenSource; $i++) {
            $distance[$i + 1][1] = $i;
            $distance[$i + 1][0] = $maxDistance;
        }

        for ($j = 0; $j <= $lenTarget; $j++) {
            $distance[1][$j + 1] = $j;
            $distance[0][$j + 1] = $maxDistance;
        }

        /** @var array<string, int> $charIndex */
        $charIndex = [];

        for ($i = 1; $i <= $lenSource; $i++) {
            $db = 0;
            $sourceChar = $source[$i - 1];

            for ($j = 1; $j <= $lenTarget; $j++) {
                $targetChar = $target[$j - 1];
                $i1 = $charIndex[$targetChar] ?? 0;
                $j1 = $db;

                $cost = ($sourceChar === $targetChar) ? 0 : 1;
                if ($cost === 0) {
                    $db = $j;
                }

                $distance[$i + 1][$j + 1] = min(
                    $distance[$i][$j] + $cost,
                    $distance[$i + 1][$j] + 1,
                    $distance[$i][$j + 1] + 1,
                    $distance[$i1][$j1] + ($i - $i1 - 1) + ($j - $j1 - 1) + $transpositionCost
                );
            }

            $charIndex[$sourceChar] = $i;
        }

        return $distance[$lenSource + 1][$lenTarget + 1];
    }

    /**
     * @param array<int, string> $left
     * @param array<int, string> $right
     */
    private static function lcsLength(array $left, array $right): int
    {
        $lenLeft = count($left);
        $lenRight = count($right);

        if ($lenLeft === 0 || $lenRight === 0) {
            return 0;
        }

        $dp = array_fill(0, $lenLeft + 1, array_fill(0, $lenRight + 1, 0));

        for ($i = 1; $i <= $lenLeft; $i++) {
            for ($j = 1; $j <= $lenRight; $j++) {
                if ($left[$i - 1] === $right[$j - 1]) {
                    $dp[$i][$j] = $dp[$i - 1][$j - 1] + 1;
                } else {
                    $dp[$i][$j] = max($dp[$i - 1][$j], $dp[$i][$j - 1]);
                }
            }
        }

        return $dp[$lenLeft][$lenRight];
    }

    /**
     * @param array<int, string> $left
     * @param array<int, string> $right
     */
    private static function ratcliffObershelpMatches(array $left, array $right): int
    {
        $match = self::longestCommonSubstring($left, $right);
        if ($match['length'] === 0) {
            return 0;
        }

        $startLeft = $match['start_left'];
        $startRight = $match['start_right'];
        $length = $match['length'];

        $leftBefore = array_slice($left, 0, $startLeft);
        $rightBefore = array_slice($right, 0, $startRight);
        $leftAfter = array_slice($left, $startLeft + $length);
        $rightAfter = array_slice($right, $startRight + $length);

        return $length
            + self::ratcliffObershelpMatches($leftBefore, $rightBefore)
            + self::ratcliffObershelpMatches($leftAfter, $rightAfter);
    }

    /**
     * @param array<int, string> $left
     * @param array<int, string> $right
     * @return array{start_left: int, start_right: int, length: int}
     */
    private static function longestCommonSubstring(array $left, array $right): array
    {
        $lenLeft = count($left);
        $lenRight = count($right);

        $longest = ['start_left' => 0, 'start_right' => 0, 'length' => 0];

        if ($lenLeft === 0 || $lenRight === 0) {
            return $longest;
        }

        $matrix = array_fill(0, $lenLeft, array_fill(0, $lenRight, 0));

        for ($i = 0; $i < $lenLeft; $i++) {
            for ($j = 0; $j < $lenRight; $j++) {
                if ($left[$i] !== $right[$j]) {
                    continue;
                }

                $matrix[$i][$j] = ($i === 0 || $j === 0) ? 1 : $matrix[$i - 1][$j - 1] + 1;

                if ($matrix[$i][$j] > $longest['length']) {
                    $longest = [
                        'start_left' => $i - $matrix[$i][$j] + 1,
                        'start_right' => $j - $matrix[$i][$j] + 1,
                        'length' => $matrix[$i][$j],
                    ];
                }
            }
        }

        return $longest;
    }

    /**
     * @param array<int, string> $tokens
     * @return array<int, string>
     */
    private static function buildNgrams(array $tokens, int $n): array
    {
        $count = count($tokens);
        if ($count === 0) {
            return [];
        }

        if ($n <= 1 || $count === 1) {
            return $tokens;
        }

        $ngrams = [];
        $separator = "\u{241F}";
        if ($count < $n) {
            $ngrams[] = implode($separator, $tokens);
            return $ngrams;
        }

        for ($i = 0; $i <= $count - $n; $i++) {
            $ngrams[] = implode($separator, array_slice($tokens, $i, $n));
        }

        return $ngrams;
    }

    /**
     * @param array<int, string> $left
     * @param array<int, string> $right
     * @return array{left: array<string, float>, right: array<string, float>}
     */
    private static function tokenWeights(array $left, array $right, string $weighting): array
    {
        $countsLeft = array_count_values($left);
        $countsRight = array_count_values($right);

        switch ($weighting) {
            case 'binary':
                $weightsLeft = array_fill_keys(array_keys($countsLeft), 1.0);
                $weightsRight = array_fill_keys(array_keys($countsRight), 1.0);
                break;

            case 'tf':
                $weightsLeft = array_map(static fn (int $count): float => (float) $count, $countsLeft);
                $weightsRight = array_map(static fn (int $count): float => (float) $count, $countsRight);
                break;

            case 'log':
                $weightsLeft = array_map(static fn (int $count): float => 1.0 + log($count), $countsLeft);
                $weightsRight = array_map(static fn (int $count): float => 1.0 + log($count), $countsRight);
                break;

            case 'augmented':
            case 'double-normalization-0.5':
                $maxLeft = max($countsLeft ?: [1]);
                $maxRight = max($countsRight ?: [1]);
                $weightsLeft = array_map(static fn (int $count) => 0.5 + (0.5 * $count / $maxLeft), $countsLeft);
                $weightsRight = array_map(static fn (int $count) => 0.5 + (0.5 * $count / $maxRight), $countsRight);
                break;

            case 'tfidf':
            default:
                $weightsLeft = [];
                $weightsRight = [];
                $documentCount = 2;
                $allTokens = array_unique(array_merge(array_keys($countsLeft), array_keys($countsRight)));
                foreach ($allTokens as $token) {
                    $df = 0;
                    if (isset($countsLeft[$token])) {
                        $df++;
                    }
                    if (isset($countsRight[$token])) {
                        $df++;
                    }
                    $idf = log(($documentCount + 1) / ($df + 1)) + 1.0;
                    if (isset($countsLeft[$token])) {
                        $weightsLeft[$token] = $countsLeft[$token] * $idf;
                    }
                    if (isset($countsRight[$token])) {
                        $weightsRight[$token] = $countsRight[$token] * $idf;
                    }
                }
                break;
        }

        return [
            'left' => $weightsLeft,
            'right' => $weightsRight,
        ];
    }

    /**
     * @param array<int, string> $ngramsLeft
     * @param array<int, string> $ngramsRight
     */
    private static function cosineSimilarity(array $ngramsLeft, array $ngramsRight, string $weighting): float
    {
        $weights = self::tokenWeights($ngramsLeft, $ngramsRight, $weighting);
        $weightsLeft = $weights['left'];
        $weightsRight = $weights['right'];

        $dot = 0.0;
        foreach ($weightsLeft as $token => $weight) {
            $dot += $weight * ($weightsRight[$token] ?? 0.0);
        }

        $normLeft = self::vectorNorm($weightsLeft);
        $normRight = self::vectorNorm($weightsRight);

        if ($normLeft === 0.0 || $normRight === 0.0) {
            return 0.0;
        }

        return $dot / ($normLeft * $normRight);
    }

    /**
     * @param array<string, float> $vector
     */
    private static function vectorNorm(array $vector): float
    {
        $sum = 0.0;
        foreach ($vector as $value) {
            $sum += $value * $value;
        }

        return sqrt($sum);
    }

    private static function stringLength(string $value, string $mode, string $encoding): int
    {
        if ($value === '') {
            return 0;
        }

        return match ($mode) {
            'bytes' => strlen($value),
            'graphemes' => function_exists('grapheme_strlen') ? grapheme_strlen($value) ?: mb_strlen($value, $encoding) : mb_strlen($value, $encoding),
            default => mb_strlen($value, $encoding),
        };
    }

    private static function toLower(string $value, string $encoding): string
    {
        if ($value === '') {
            return '';
        }

        if (function_exists('mb_strtolower')) {
            return mb_strtolower($value, $encoding);
        }

        return strtolower($value);
    }

    private static function mongeElkanDirection(array $primary, array $secondaryTokens, string $secondaryMetric, float $tau, string $encoding): float
    {
        $count = count($primary);
        if ($count === 0) {
            return 0.0;
        }

        $total = 0.0;
        foreach ($primary as $token) {
            $best = 0.0;
            foreach ($secondaryTokens as $candidate) {
                $score = self::computeSecondary($token, $candidate, $secondaryMetric, $encoding);
                if ($score > $best && $score >= $tau) {
                    $best = $score;
                }
            }

            $total += $best;
        }

        return $total / $count;
    }

    private static function computeSecondary(string $left, string $right, string $metric, string $encoding): float
    {
        $options = [
            'granularity' => 'character',
            'case_sensitive' => true,
            'normalize_whitespace' => false,
            'strip_punctuation' => false,
            'threshold' => 0.0,
            'mode' => 'codepoints',
        ];

        $preparedLeft = self::prepareInput($left, $options, $encoding);
        $preparedRight = self::prepareInput($right, $options, $encoding);

        switch ($metric) {
            case 'levenshtein':
                return self::scoreLevenshtein($preparedLeft['string'], $preparedRight['string'], $options, $encoding);

            case 'damerau-levenshtein':
                return self::scoreDamerauLevenshtein($preparedLeft['characters'], $preparedRight['characters'], ['transposition_cost' => 1]);

            case 'jaro-winkler':
                return self::scoreJaroWinkler($preparedLeft['characters'], $preparedRight['characters'], [
                    'prefix_scale' => 0.1,
                    'prefix_limit' => 4,
                ]);

            case 'lcs-myers':
                return self::scoreLcsMyers($preparedLeft['tokens'], $preparedRight['tokens'], ['weight_common_prefix' => 0.0]);

            case 'ratcliff-obershelp':
                return self::scoreRatcliffObershelp($preparedLeft['tokens'], $preparedRight['tokens']);

            case 'jaccard':
                return self::scoreJaccard($preparedLeft['tokens'], $preparedRight['tokens'], ['token_set' => true]);

            case 'sorensen-dice':
                return self::scoreSorensenDice($preparedLeft['tokens'], $preparedRight['tokens'], ['token_set' => true]);

            case 'cosine-ngrams':
                return self::scoreCosineNgrams($preparedLeft, $preparedRight, ['n' => 3, 'weighting' => 'binary', 'granularity' => 'character']);

            case 'github-style':
                return self::scoreGithubStyle($preparedLeft['tokens'], $preparedRight['tokens'], [
                    'prefix_scale' => 0.05,
                    'prefix_limit' => 3,
                ]);

            default:
                return 0.0;
        }
    }
}

