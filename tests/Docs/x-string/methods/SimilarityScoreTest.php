<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class SimilarityScoreTest extends TestCase
{
    public function testSimilarityLevenshtein(): void
    {
        $score = XString::new('kitten')->similarityScore('kitten', 'levenshtein');
        self::assertSame(1.0, $score);
    }

    public function testSimilarityDamerau(): void
    {
        $score = XString::new('cares')->similarityScore('cares', 'damerau-levenshtein');
        self::assertSame(1.0, $score);
    }

    public function testSimilarityJaroWinkler(): void
    {
        $score = XString::new('MARTHA')->similarityScore('MARTHA', 'jaro-winkler');
        self::assertSame(1.0, $score);
    }

    public function testSimilarityLcs(): void
    {
        $score = XString::new('diff this')->similarityScore('diff this', 'lcs-myers');
        self::assertSame(1.0, $score);
    }

    public function testSimilarityRatcliff(): void
    {
        $score = XString::new('pattern')->similarityScore('pattern', 'ratcliff-obershelp');
        self::assertSame(1.0, $score);
    }

    public function testSimilarityJaccard(): void
    {
        $score = XString::new('foo bar baz')->similarityScore('foo bar baz', 'jaccard');
        self::assertSame(1.0, $score);
    }

    public function testSimilaritySorensen(): void
    {
        $score = XString::new('quick brown fox')->similarityScore('quick brown fox', 'sorensen-dice');
        self::assertSame(1.0, $score);
    }

    public function testSimilarityCosine(): void
    {
        $score = XString::new('vector space')->similarityScore('vector space', 'cosine-ngrams', ['n' => 2]);
        self::assertSame(1.0, $score);
    }

    public function testSimilarityMongeElkan(): void
    {
        $score = XString::new('data science')->similarityScore('data science', 'monge-elkan');
        self::assertSame(1.0, $score);
    }

    public function testSimilaritySoftTfidf(): void
    {
        $score = XString::new('fuzzy logic')->similarityScore('fuzzy logic', 'soft-tfidf');
        self::assertSame(1.0, $score);
    }

    public function testSimilarityGithub(): void
    {
        $score = XString::new('function similarityScore')->similarityScore('function similarityScore');
        self::assertSame(1.0, $score);
    }

}
