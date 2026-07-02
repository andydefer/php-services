<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Services;

use AndyDefer\DomainStructures\Collections\Utility\FloatTypedCollection;
use AndyDefer\PhpServices\Contracts\Services\NGramGeneratorInterface;
use AndyDefer\PhpServices\Contracts\Services\WordVectorGeneratorInterface;
use AndyDefer\PhpServices\Contracts\TextNormalizerConfigInterface;
use AndyDefer\PhpServices\Contracts\TextNormalizerInterface;
use AndyDefer\PhpServices\Enums\NormalizationMode;

/**
 * Service for generating word vectors.
 *
 * Uses n-gram hashing to create vector representations of words.
 *
 * @author Andy Defer
 */
final class WordVectorGeneratorService implements WordVectorGeneratorInterface
{
    private const DEFAULT_DIMENSION = 1000;

    private const DEFAULT_NGRAM_SIZE = 2;

    private const NORM_EPSILON = 1e-10;

    private TextNormalizerInterface $normalizer;

    private NGramGeneratorInterface $ngramGenerator;

    public function __construct(
        TextNormalizerConfigInterface $config
    ) {
        $this->normalizer = new TextNormalizerService($config);
        $this->ngramGenerator = new NGramGeneratorService($config);
    }

    public function generate(
        string $word,
        int $dimension = self::DEFAULT_DIMENSION,
        int $nGramSize = self::DEFAULT_NGRAM_SIZE,
        NormalizationMode $mode = NormalizationMode::WITH_NORMALIZATION
    ): FloatTypedCollection {
        $processedWord = $this->processWord($word, $mode);

        if ($processedWord === '') {
            return FloatTypedCollection::from(array_fill(0, $dimension, 0.0));
        }

        $ngrams = $this->ngramGenerator->generateBySize($processedWord, $nGramSize, NormalizationMode::WITHOUT);

        $vector = array_fill(0, $dimension, 0.0);

        foreach ($ngrams as $ngram) {
            $hashIndex = abs(crc32($ngram)) % $dimension;
            $vector[$hashIndex] += 1.0;
        }

        $collection = FloatTypedCollection::from($vector);

        return $this->normalizeVector($collection);
    }

    public function generateWithNormalization(
        string $word,
        int $dimension = self::DEFAULT_DIMENSION,
        int $nGramSize = self::DEFAULT_NGRAM_SIZE
    ): FloatTypedCollection {
        return $this->generate($word, $dimension, $nGramSize, NormalizationMode::WITH_NORMALIZATION);
    }

    public function generateWithBigrams(
        string $word,
        int $dimension = self::DEFAULT_DIMENSION,
        NormalizationMode $mode = NormalizationMode::WITH_NORMALIZATION
    ): FloatTypedCollection {
        return $this->generate($word, $dimension, 2, $mode);
    }

    public function generateWithTrigrams(
        string $word,
        int $dimension = self::DEFAULT_DIMENSION,
        NormalizationMode $mode = NormalizationMode::WITH_NORMALIZATION
    ): FloatTypedCollection {
        return $this->generate($word, $dimension, 3, $mode);
    }

    public function cosineSimilarity(FloatTypedCollection $vector1, FloatTypedCollection $vector2): float
    {
        $array1 = $vector1->toArray();
        $array2 = $vector2->toArray();

        if (count($array1) !== count($array2)) {
            throw new \InvalidArgumentException('Vectors must have the same dimension');
        }

        $dotProduct = 0.0;
        $norm1 = 0.0;
        $norm2 = 0.0;

        foreach ($array1 as $i => $value) {
            $dotProduct += $value * $array2[$i];
            $norm1 += $value * $value;
            $norm2 += $array2[$i] * $array2[$i];
        }

        $norm1 = sqrt($norm1);
        $norm2 = sqrt($norm2);

        if ($norm1 < self::NORM_EPSILON || $norm2 < self::NORM_EPSILON) {
            return 0.0;
        }

        return $dotProduct / ($norm1 * $norm2);
    }

    public function normalizeVector(FloatTypedCollection $vector): FloatTypedCollection
    {
        $array = $vector->toArray();

        $norm = sqrt(array_sum(array_map(fn ($v) => $v * $v, $array)));

        if ($norm < self::NORM_EPSILON) {
            return $vector;
        }

        $normalized = array_map(fn ($v) => $v / $norm, $array);

        return FloatTypedCollection::from($normalized);
    }

    private function processWord(string $word, NormalizationMode $mode): string
    {
        if ($mode === NormalizationMode::WITH_NORMALIZATION) {
            return $this->normalizer->normalize($word);
        }

        return $word;
    }
}
