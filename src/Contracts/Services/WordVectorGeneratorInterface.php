<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Contracts\Services;

use AndyDefer\DomainStructures\Collections\Utility\FloatTypedCollection;
use AndyDefer\PhpServices\Enums\NormalizationMode;

/**
 * Interface for generating word vectors.
 *
 * Provides methods to generate vector representations of words
 * for similarity calculations and machine learning applications.
 */
interface WordVectorGeneratorInterface
{
    /**
     * Generates a vector for a word.
     *
     * @param  string  $word  The word to vectorize
     * @param  int  $dimension  Vector dimension (default: 1000)
     * @param  int  $nGramSize  N-gram size for vectorization (default: 2)
     * @param  NormalizationMode  $mode  Normalization mode
     * @return FloatTypedCollection Vector representation
     */
    public function generate(
        string $word,
        int $dimension = 1000,
        int $nGramSize = 2,
        NormalizationMode $mode = NormalizationMode::WITH_NORMALIZATION
    ): FloatTypedCollection;

    /**
     * Generates a vector with normalization applied.
     *
     * @param  string  $word  The word to vectorize
     * @param  int  $dimension  Vector dimension (default: 1000)
     * @param  int  $nGramSize  N-gram size for vectorization (default: 2)
     * @return FloatTypedCollection Normalized vector
     */
    public function generateWithNormalization(
        string $word,
        int $dimension = 1000,
        int $nGramSize = 2
    ): FloatTypedCollection;

    /**
     * Generates a vector using bigrams.
     *
     * @param  string  $word  The word to vectorize
     * @param  int  $dimension  Vector dimension (default: 1000)
     * @param  NormalizationMode  $mode  Normalization mode
     * @return FloatTypedCollection Vector representation
     */
    public function generateWithBigrams(
        string $word,
        int $dimension = 1000,
        NormalizationMode $mode = NormalizationMode::WITH_NORMALIZATION
    ): FloatTypedCollection;

    /**
     * Generates a vector using trigrams.
     *
     * @param  string  $word  The word to vectorize
     * @param  int  $dimension  Vector dimension (default: 1000)
     * @param  NormalizationMode  $mode  Normalization mode
     * @return FloatTypedCollection Vector representation
     */
    public function generateWithTrigrams(
        string $word,
        int $dimension = 1000,
        NormalizationMode $mode = NormalizationMode::WITH_NORMALIZATION
    ): FloatTypedCollection;

    /**
     * Calculates cosine similarity between two vectors.
     *
     * @param  FloatTypedCollection  $vector1  First vector
     * @param  FloatTypedCollection  $vector2  Second vector
     * @return float Similarity score between 0 and 1
     *
     * @throws \InvalidArgumentException If vectors have different dimensions
     */
    public function cosineSimilarity(FloatTypedCollection $vector1, FloatTypedCollection $vector2): float;

    /**
     * Normalizes a vector to unit length.
     *
     * @param  FloatTypedCollection  $vector  The vector to normalize
     * @return FloatTypedCollection Normalized vector
     */
    public function normalizeVector(FloatTypedCollection $vector): FloatTypedCollection;
}
