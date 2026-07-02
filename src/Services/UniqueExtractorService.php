<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Services;

use AndyDefer\PhpServices\Contracts\Services\UniqueExtractorInterface;
use AndyDefer\PhpServices\Contracts\TextNormalizerConfigInterface;
use AndyDefer\PhpServices\Contracts\TextNormalizerInterface;
use AndyDefer\PhpServices\Enums\NormalizationMode;

/**
 * Service for extracting unique letters and unique words from text.
 *
 * Uses native PHP arrays and functions for all operations.
 * No external dependencies required.
 *
 * @author Andy Defer
 */
final class UniqueExtractorService implements UniqueExtractorInterface
{
    private TextNormalizerInterface $normalizer;

    /**
     * Cache for unique letters.
     *
     * @var array<string, array<string>>
     */
    private array $uniqueLettersCache = [];

    /**
     * Cache for unique words.
     *
     * @var array<string, array<string>>
     */
    private array $uniqueWordsCache = [];

    /**
     * Cache for word frequencies.
     *
     * @var array<string, int>
     */
    private array $wordFrequencies = [];

    /**
     * Cache for letter frequencies.
     *
     * @var array<string, int>
     */
    private array $letterFrequencies = [];

    /**
     * Trie structure for prefix search.
     *
     * @var array<string, mixed>
     */
    private array $trie = [];

    /**
     * All words stored.
     *
     * @var array<string>
     */
    private array $allWords = [];

    /**
     * All letters stored.
     *
     * @var array<string>
     */
    private array $allLetters = [];

    public function __construct(
        TextNormalizerConfigInterface $config
    ) {
        $this->normalizer = new TextNormalizerService($config);
    }

    public function extractUniqueLetters(string $text, NormalizationMode $mode = NormalizationMode::WITHOUT): array
    {
        $cacheKey = md5($text.$mode->value);
        if (isset($this->uniqueLettersCache[$cacheKey])) {
            return $this->uniqueLettersCache[$cacheKey];
        }

        $processedText = $this->processText($text, $mode);

        $letters = preg_split('//u', $processedText, -1, PREG_SPLIT_NO_EMPTY);
        $letters = array_filter($letters, function ($char) {
            return preg_match('/\p{L}/u', $char) === 1;
        });

        $uniqueLetters = array_values(array_unique($letters));

        // Update caches
        foreach ($uniqueLetters as $letter) {
            $this->allLetters[] = $letter;
            $this->letterFrequencies[$letter] = ($this->letterFrequencies[$letter] ?? 0) + 1;
        }

        $this->uniqueLettersCache[$cacheKey] = $uniqueLetters;

        return $uniqueLetters;
    }

    public function extractUniqueWords(
        string $text,
        NormalizationMode $mode = NormalizationMode::WITHOUT,
        bool $removeStopWords = false,
        bool $removeShortWords = false,
        int $minWordLength = 2
    ): array {
        $cacheKey = md5($text.$mode->value.($removeStopWords ? '1' : '0').($removeShortWords ? '1' : '0').$minWordLength);
        if (isset($this->uniqueWordsCache[$cacheKey])) {
            return $this->uniqueWordsCache[$cacheKey];
        }

        $processedText = $this->processText($text, $mode);

        if ($processedText === '') {
            return [];
        }

        $words = $this->normalizer->extractWords($processedText);

        if ($removeStopWords) {
            $words = $this->normalizer->removeStopWords($words);
        }

        if ($removeShortWords) {
            $words = $this->normalizer->removeShortWords($words, $minWordLength);
        }

        $uniqueWords = array_values(array_unique($words));

        // Update caches and trie
        foreach ($uniqueWords as $word) {
            $this->allWords[] = $word;
            $this->wordFrequencies[$word] = ($this->wordFrequencies[$word] ?? 0) + 1;
            $this->insertIntoTrie($word);
        }

        $this->uniqueWordsCache[$cacheKey] = $uniqueWords;

        return $uniqueWords;
    }

    public function extractUniqueLettersWithNormalization(string $text): array
    {
        return $this->extractUniqueLetters($text, NormalizationMode::WITH_NORMALIZATION);
    }

    public function extractUniqueWordsWithNormalization(
        string $text,
        bool $removeStopWords = false,
        bool $removeShortWords = false,
        int $minWordLength = 2
    ): array {
        return $this->extractUniqueWords($text, NormalizationMode::WITH_NORMALIZATION, $removeStopWords, $removeShortWords, $minWordLength);
    }

    /**
     * Vérifie si une lettre existe déjà.
     *
     * @param  string  $letter  La lettre à vérifier
     * @return bool True si la lettre existe
     */
    public function letterExists(string $letter): bool
    {
        return isset($this->letterFrequencies[$letter]);
    }

    /**
     * Estime le nombre de lettres uniques.
     *
     * @return int Estimation du nombre de lettres uniques
     */
    public function estimateUniqueLetters(): int
    {
        return count(array_unique($this->allLetters));
    }

    /**
     * Estime le nombre de mots uniques.
     *
     * @return int Estimation du nombre de mots uniques
     */
    public function estimateUniqueWords(): int
    {
        return count(array_unique($this->allWords));
    }

    /**
     * Recherche des mots par préfixe (Trie).
     *
     * @param  string  $prefix  Le préfixe à rechercher
     * @param  int  $limit  Nombre maximum de résultats
     * @return array<string> Liste des mots correspondants
     */
    public function searchWordsByPrefix(string $prefix, int $limit = 10): array
    {
        $results = [];
        $this->searchTrie($this->trie, $prefix, '', $results, $limit);

        return $results;
    }

    /**
     * Obtient la fréquence d'une lettre.
     *
     * @param  string  $letter  La lettre
     * @return int Fréquence estimée
     */
    public function getLetterFrequency(string $letter): int
    {
        return $this->letterFrequencies[$letter] ?? 0;
    }

    /**
     * Obtient les mots les plus fréquents.
     *
     * @param  int  $limit  Nombre maximum de résultats
     * @return array<string> Liste des mots les plus fréquents
     */
    public function getMostFrequentWords(int $limit = 10): array
    {
        arsort($this->wordFrequencies);

        return array_slice(array_keys($this->wordFrequencies), 0, $limit);
    }

    /**
     * Obtient l'état actuel des structures de données.
     *
     * @return array<string, mixed> Statistiques
     */
    public function getStats(): array
    {
        return [
            'unique_letters_estimated' => $this->estimateUniqueLetters(),
            'unique_words_estimated' => $this->estimateUniqueWords(),
            'total_letters_processed' => count($this->allLetters),
            'total_words_processed' => count($this->allWords),
            'unique_letter_count' => count($this->letterFrequencies),
            'unique_word_count' => count($this->wordFrequencies),
            'trie_node_count' => $this->countTrieNodes($this->trie),
            'cache_size' => count($this->uniqueLettersCache) + count($this->uniqueWordsCache),
        ];
    }

    /**
     * Vide toutes les structures de données.
     */
    public function clear(): void
    {
        $this->uniqueLettersCache = [];
        $this->uniqueWordsCache = [];
        $this->wordFrequencies = [];
        $this->letterFrequencies = [];
        $this->trie = [];
        $this->allWords = [];
        $this->allLetters = [];
    }

    private function processText(string $text, NormalizationMode $mode): string
    {
        if ($mode === NormalizationMode::WITH_NORMALIZATION) {
            return $this->normalizer->normalize($text);
        }

        return $text;
    }

    /**
     * Inserts a word into the trie structure.
     */
    private function insertIntoTrie(string $word): void
    {
        $node = &$this->trie;
        $wordLength = strlen($word);

        for ($i = 0; $i < $wordLength; $i++) {
            $char = $word[$i];
            if (! isset($node[$char])) {
                $node[$char] = [];
            }
            $node = &$node[$char];
        }

        $node['#'] = true; // End of word marker
    }

    /**
     * Searches the trie for words with a given prefix.
     */
    private function searchTrie(array $node, string $prefix, string $current, array &$results, int $limit): void
    {
        if (count($results) >= $limit) {
            return;
        }

        if ($prefix === '') {
            // Found a word
            if (isset($node['#']) && $current !== '') {
                $results[] = $current;
            }

            foreach ($node as $char => $child) {
                if ($char === '#') {
                    continue;
                }
                $this->searchTrie($child, '', $current.$char, $results, $limit);
            }
        } else {
            $char = $prefix[0];
            $remaining = substr($prefix, 1);

            if (isset($node[$char])) {
                $this->searchTrie($node[$char], $remaining, $current.$char, $results, $limit);
            }
        }
    }

    /**
     * Counts the number of nodes in the trie.
     */
    private function countTrieNodes(array $node): int
    {
        $count = 1;

        foreach ($node as $key => $value) {
            if ($key === '#') {
                continue;
            }
            $count += $this->countTrieNodes($value);
        }

        return $count;
    }
}
