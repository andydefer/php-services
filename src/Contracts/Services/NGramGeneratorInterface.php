<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Contracts\Services;

use AndyDefer\DomainStructures\Collections\Utility\StringTypedCollection;
use AndyDefer\PhpServices\Enums\NormalizationMode;

/**
 * Interface for generating n-grams from text.
 *
 * Provides methods to generate n-grams (bigrams, trigrams, etc.)
 * with configurable size ranges.
 */
interface NGramGeneratorInterface
{
    /**
     * Generates n-grams from text.
     *
     * @param  string  $text  The text to process
     * @param  int  $minSize  Minimum n-gram size (default: 2)
     * @param  int  $maxSize  Maximum n-gram size (default: 4)
     * @param  NormalizationMode  $mode  Normalization mode
     * @return StringTypedCollection List of n-grams
     */
    public function generate(
        string $text,
        int $minSize = 2,
        int $maxSize = 4,
        NormalizationMode $mode = NormalizationMode::WITHOUT
    ): StringTypedCollection;

    /**
     * Generates n-grams with normalization applied.
     *
     * @param  string  $text  The text to process
     * @param  int  $minSize  Minimum n-gram size (default: 2)
     * @param  int  $maxSize  Maximum n-gram size (default: 4)
     * @return StringTypedCollection List of normalized n-grams
     */
    public function generateWithNormalization(
        string $text,
        int $minSize = 2,
        int $maxSize = 4
    ): StringTypedCollection;

    /**
     * Generates bigrams (2-grams).
     *
     * @param  string  $text  The text to process
     * @param  NormalizationMode  $mode  Normalization mode
     * @return StringTypedCollection List of bigrams
     */
    public function generateBigrams(string $text, NormalizationMode $mode = NormalizationMode::WITHOUT): StringTypedCollection;

    /**
     * Generates trigrams (3-grams).
     *
     * @param  string  $text  The text to process
     * @param  NormalizationMode  $mode  Normalization mode
     * @return StringTypedCollection List of trigrams
     */
    public function generateTrigrams(string $text, NormalizationMode $mode = NormalizationMode::WITHOUT): StringTypedCollection;

    /**
     * Generates quadrigrams (4-grams).
     *
     * @param  string  $text  The text to process
     * @param  NormalizationMode  $mode  Normalization mode
     * @return StringTypedCollection List of quadrigrams
     */
    public function generateQuadrigrams(string $text, NormalizationMode $mode = NormalizationMode::WITHOUT): StringTypedCollection;

    /**
     * Generates all n-grams from text for a specific size.
     *
     * @param  string  $text  The text to process
     * @param  int  $size  The n-gram size
     * @param  NormalizationMode  $mode  Normalization mode
     * @return StringTypedCollection List of n-grams
     */
    public function generateBySize(string $text, int $size, NormalizationMode $mode = NormalizationMode::WITHOUT): StringTypedCollection;
}
