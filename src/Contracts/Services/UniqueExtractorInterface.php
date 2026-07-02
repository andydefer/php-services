<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Contracts\Services;

use AndyDefer\PhpServices\Enums\NormalizationMode;

/**
 * Interface for extracting unique elements from text.
 *
 * Provides methods to extract unique letters and unique words from text,
 * with optional normalization.
 */
interface UniqueExtractorInterface
{
    /**
     * Extracts unique letters from a text.
     *
     * @param  string  $text  The text to process
     * @param  NormalizationMode  $mode  Normalization mode (WITHOUT, WITH_NORMALIZATION)
     * @return array<string> List of unique letters
     */
    public function extractUniqueLetters(string $text, NormalizationMode $mode = NormalizationMode::WITHOUT): array;

    /**
     * Extracts unique words from a text.
     *
     * @param  string  $text  The text to process
     * @param  NormalizationMode  $mode  Normalization mode (WITHOUT, WITH_NORMALIZATION)
     * @param  bool  $removeStopWords  Whether to remove stop words
     * @param  bool  $removeShortWords  Whether to remove short words
     * @param  int  $minWordLength  Minimum length for words
     * @return array<string> List of unique words
     */
    public function extractUniqueWords(
        string $text,
        NormalizationMode $mode = NormalizationMode::WITHOUT,
        bool $removeStopWords = false,
        bool $removeShortWords = false,
        int $minWordLength = 2
    ): array;

    /**
     * Extracts unique letters from a text with normalization.
     *
     * @param  string  $text  The text to process
     * @return array<string> List of unique letters (normalized)
     */
    public function extractUniqueLettersWithNormalization(string $text): array;

    /**
     * Extracts unique words from a text with normalization.
     *
     * @param  string  $text  The text to process
     * @param  bool  $removeStopWords  Whether to remove stop words
     * @param  bool  $removeShortWords  Whether to remove short words
     * @param  int  $minWordLength  Minimum length for words
     * @return array<string> List of unique words (normalized)
     */
    public function extractUniqueWordsWithNormalization(
        string $text,
        bool $removeStopWords = false,
        bool $removeShortWords = false,
        int $minWordLength = 2
    ): array;

    /**
     * Checks if a letter already exists.
     *
     * @param  string  $letter  The letter to check
     * @return bool True if the letter exists
     */
    public function letterExists(string $letter): bool;

    /**
     * Estimates the number of unique letters.
     *
     * @return int Estimated unique letters count
     */
    public function estimateUniqueLetters(): int;

    /**
     * Estimates the number of unique words.
     *
     * @return int Estimated unique words count
     */
    public function estimateUniqueWords(): int;

    /**
     * Searches words by prefix.
     *
     * @param  string  $prefix  The prefix to search
     * @param  int  $limit  Maximum number of results
     * @return array<string> List of matching words
     */
    public function searchWordsByPrefix(string $prefix, int $limit = 10): array;

    /**
     * Gets the frequency of a letter.
     *
     * @param  string  $letter  The letter
     * @return int Estimated frequency
     */
    public function getLetterFrequency(string $letter): int;

    /**
     * Gets the most frequent words.
     *
     * @param  int  $limit  Maximum number of results
     * @return array<string> List of most frequent words
     */
    public function getMostFrequentWords(int $limit = 10): array;

    /**
     * Gets current statistics of data structures.
     *
     * @return array<string, mixed> Statistics
     */
    public function getStats(): array;

    /**
     * Clears all data structures.
     */
    public function clear(): void;
}
