<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Services;

use AndyDefer\DomainStructures\Collections\Utility\StringTypedCollection;
use AndyDefer\PhpServices\Contracts\Services\NGramGeneratorInterface;
use AndyDefer\PhpServices\Contracts\TextNormalizerConfigInterface;
use AndyDefer\PhpServices\Contracts\TextNormalizerInterface;
use AndyDefer\PhpServices\Enums\NormalizationMode;

/**
 * Service for generating n-grams from text.
 *
 * @author Andy Defer
 */
final class NGramGeneratorService implements NGramGeneratorInterface
{
    private const MIN_SIZE = 2;

    private const MAX_SIZE = 10;

    private TextNormalizerInterface $normalizer;

    public function __construct(
        TextNormalizerConfigInterface $config
    ) {
        $this->normalizer = new TextNormalizerService($config);
    }

    public function generate(
        string $text,
        int $minSize = 2,
        int $maxSize = 4,
        NormalizationMode $mode = NormalizationMode::WITHOUT
    ): StringTypedCollection {
        $minSize = max(self::MIN_SIZE, $minSize);
        $maxSize = min(self::MAX_SIZE, $maxSize);

        if ($minSize > $maxSize) {
            throw new \InvalidArgumentException('minSize must be less than or equal to maxSize');
        }

        $processedText = $this->processText($text, $mode);

        if ($processedText === '') {
            return new StringTypedCollection;
        }

        $allNGrams = [];

        for ($size = $minSize; $size <= $maxSize; $size++) {
            $ngrams = $this->generateBySize($processedText, $size, $mode);
            $allNGrams = array_merge($allNGrams, $ngrams->toArray());
        }

        return StringTypedCollection::from($allNGrams);
    }

    public function generateWithNormalization(
        string $text,
        int $minSize = 2,
        int $maxSize = 4
    ): StringTypedCollection {
        return $this->generate($text, $minSize, $maxSize, NormalizationMode::WITH_NORMALIZATION);
    }

    public function generateBigrams(string $text, NormalizationMode $mode = NormalizationMode::WITHOUT): StringTypedCollection
    {
        return $this->generateBySize($text, 2, $mode);
    }

    public function generateTrigrams(string $text, NormalizationMode $mode = NormalizationMode::WITHOUT): StringTypedCollection
    {
        return $this->generateBySize($text, 3, $mode);
    }

    public function generateQuadrigrams(string $text, NormalizationMode $mode = NormalizationMode::WITHOUT): StringTypedCollection
    {
        return $this->generateBySize($text, 4, $mode);
    }

    public function generateBySize(string $text, int $size, NormalizationMode $mode = NormalizationMode::WITHOUT): StringTypedCollection
    {
        $processedText = $this->processText($text, $mode);

        if ($processedText === '' || $size < 1) {
            return new StringTypedCollection;
        }

        $ngrams = [];
        $length = mb_strlen($processedText);

        for ($i = 0; $i <= $length - $size; $i++) {
            $ngrams[] = mb_substr($processedText, $i, $size);
        }

        return StringTypedCollection::from($ngrams);
    }

    private function processText(string $text, NormalizationMode $mode): string
    {
        if ($mode === NormalizationMode::WITH_NORMALIZATION) {
            return $this->normalizer->normalize($text);
        }

        return $text;
    }
}
