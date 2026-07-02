<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Contracts;

/**
 * Interface for text normalization operations.
 *
 * Provides methods for cleaning, normalizing, and extracting words from text.
 *
 * @author Andy Defer
 */
interface TextNormalizerInterface
{
    /**
     * Normalizes text by removing unwanted characters and standardizing format.
     *
     * @param  string  $text  The text to normalize
     * @return string Normalized text
     */
    public function normalize(string $text): string;

    /**
     * Extracts words from text.
     *
     * @param  string  $text  The text to extract words from
     * @return array<string> List of words
     */
    public function extractWords(string $text): array;

    /**
     * Removes elided articles from text.
     *
     * @param  string  $text  The text to process
     * @return string Text without elided articles
     */
    public function removeElidedArticles(string $text): string;

    /**
     * Removes emojis from text.
     *
     * @param  string  $text  The text to process
     * @return string Text without emojis
     */
    public function removeEmojis(string $text): string;

    /**
     * Removes diacritics from text (accents, etc.).
     *
     * @param  string  $text  The text to process
     * @return string Text without diacritics
     */
    public function removeDiacritics(string $text): string;

    /**
     * Removes currency symbols from text.
     *
     * @param  string  $text  The text to process
     * @return string Text without currency symbols
     */
    public function removeCurrencySymbols(string $text): string;

    /**
     * Removes special characters from text.
     *
     * @param  string  $text  The text to process
     * @return string Text without special characters
     */
    public function removeSpecialChars(string $text): string;

    /**
     * Normalizes spaces in text (removes extra spaces, trims).
     *
     * @param  string  $text  The text to process
     * @return string Text with normalized spaces
     */
    public function normalizeSpaces(string $text): string;

    /**
     * Removes short words from an array of words.
     *
     * @param  array<string>  $words  List of words
     * @param  int  $minLength  Minimum length for words to keep (default: 2)
     * @return array<string> Filtered words
     */
    public function removeShortWords(array $words, int $minLength = 2): array;

    /**
     * Removes stop words from an array of words.
     *
     * @param  array<string>  $words  List of words
     * @return array<string> Filtered words
     */
    public function removeStopWords(array $words): array;

    /**
     * Processes text by extracting and filtering words.
     *
     * @param  string  $text  The text to process
     * @param  bool  $removeStopWords  Whether to remove stop words
     * @param  bool  $removeShortWords  Whether to remove short words
     * @param  int  $minWordLength  Minimum length for words
     * @return array<string> Processed words
     */
    public function processText(
        string $text,
        bool $removeStopWords = true,
        bool $removeShortWords = true,
        int $minWordLength = 2
    ): array;

    /**
     * Clears internal caches.
     */
    public function clearCache(): void;
}
