<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Services;

use AndyDefer\PhpServices\Contracts\TextNormalizerConfigInterface;
use AndyDefer\PhpServices\Contracts\TextNormalizerInterface;

/**
 * Service for normalizing and processing text.
 *
 * This service is responsible for cleaning, normalizing, and extracting
 * words from text. It is framework-agnostic and uses only PHP built-in functions.
 *
 * Single Responsibility: Text normalization and word extraction.
 *
 * @author Andy Defer
 */
final class TextNormalizerService implements TextNormalizerInterface
{
    /**
     * Cache for normalized text.
     *
     * @var array<string, string>
     */
    private array $normalizeCache = [];

    /**
     * Cache for extracted words.
     *
     * @var array<string, array<string>>
     */
    private array $extractCache = [];

    public function __construct(
        private readonly TextNormalizerConfigInterface $config
    ) {}

    public function normalize(string $text): string
    {
        $cacheKey = md5($text);
        if (isset($this->normalizeCache[$cacheKey])) {
            return $this->normalizeCache[$cacheKey];
        }

        $text = str_replace('-', ' ', $text);
        $text = $this->removeEmojis($text);
        $text = $this->removeDiacritics($text);
        $text = $this->removeCurrencySymbols($text);
        $text = $this->removeElidedArticles($text);
        $text = $this->removeSpecialChars($text);
        $text = $this->normalizeSpaces($text);
        $text = mb_strtolower($text);

        $this->normalizeCache[$cacheKey] = $text;

        return $text;
    }

    public function extractWords(string $text): array
    {
        $cacheKey = md5($text);
        if (isset($this->extractCache[$cacheKey])) {
            return $this->extractCache[$cacheKey];
        }

        $normalized = $this->normalize($text);

        if ($normalized === '') {
            return [];
        }

        $words = explode(' ', $normalized);
        $result = array_values(array_filter($words, function ($word) {
            return $word !== '' && $word !== ' ';
        }));

        $this->extractCache[$cacheKey] = $result;

        return $result;
    }

    public function removeElidedArticles(string $text): string
    {
        $articles = $this->config->getElidedArticles();

        foreach ($articles as $article) {
            $text = preg_replace('/\b'.preg_quote($article, '/').'(\p{L}+)/u', '$1', $text);
        }

        return $text;
    }

    public function removeEmojis(string $text): string
    {
        $cleaned = preg_replace('/\p{Extended_Pictographic}/u', '', $text);

        return $cleaned ?? '';
    }

    public function removeDiacritics(string $text): string
    {
        return strtr($text, $this->config->getDiacritics());
    }

    public function removeCurrencySymbols(string $text): string
    {
        return strtr($text, $this->config->getCurrencySymbols());
    }

    public function removeSpecialChars(string $text): string
    {
        $text = preg_replace('/[^\p{L}\p{N}\s\'-]/u', ' ', $text);

        return $text ?? '';
    }

    public function normalizeSpaces(string $text): string
    {
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text ?? '');
    }

    public function removeShortWords(array $words, int $minLength = 2): array
    {
        return array_values(array_filter($words, function ($word) use ($minLength) {
            return mb_strlen($word) >= $minLength;
        }));
    }

    public function removeStopWords(array $words): array
    {
        return array_values(array_filter($words, function ($word) {
            return ! $this->config->isStopWord($word);
        }));
    }

    public function processText(
        string $text,
        bool $removeStopWords = true,
        bool $removeShortWords = true,
        int $minWordLength = 2
    ): array {
        $words = $this->extractWords($text);

        if ($removeStopWords) {
            $words = $this->removeStopWords($words);
        }

        if ($removeShortWords) {
            $words = $this->removeShortWords($words, $minWordLength);
        }

        return array_values($words);
    }

    public function clearCache(): void
    {
        $this->normalizeCache = [];
        $this->extractCache = [];
    }
}
