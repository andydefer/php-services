<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Contracts;

/**
 * Interface for text normalization configuration.
 *
 * This interface defines only the configuration methods needed for
 * text normalization operations. It is framework-agnostic and can be
 * implemented by any configuration system.
 *
 * @author Andy Defer
 */
interface TextNormalizerConfigInterface
{
    /**
     * Returns the list of elided articles to remove from text.
     *
     * Elided articles are articles that are contracted with the following word
     * (e.g., "l'" in French, "d'" in Italian).
     *
     * @return array<string> List of elided articles
     */
    public function getElidedArticles(): array;

    /**
     * Returns the diacritics mapping for character normalization.
     *
     * The array maps accented/foreign characters to their ASCII equivalents.
     *
     * @return array<string, string> Mapping of diacritics to ASCII characters
     */
    public function getDiacritics(): array;

    /**
     * Returns the currency symbols mapping for normalization.
     *
     * The array maps currency symbols to their text representations.
     *
     * @return array<string, string> Mapping of currency symbols to text
     */
    public function getCurrencySymbols(): array;

    /**
     * Returns the list of stop words to ignore.
     *
     * Stop words are common words that are typically filtered out (e.g., "the", "and").
     *
     * @return array<string> List of stop words
     */
    public function getStopWords(): array;

    /**
     * Checks if a given word is a stop word.
     *
     * @param  string  $word  The word to check
     * @return bool True if the word is a stop word
     */
    public function isStopWord(string $word): bool;

    /**
     * Returns the minimum length for words to be kept.
     *
     * Words shorter than this length will be filtered out.
     *
     * @return int Minimum word length
     */
    public function getMinWordLength(): int;
}
