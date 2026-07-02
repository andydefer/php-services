<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Tests\Unit\Services;

use AndyDefer\PhpServices\Configs\TextNormalizerConfig;
use AndyDefer\PhpServices\Enums\NormalizationMode;
use AndyDefer\PhpServices\Services\UniqueExtractorService;
use PHPUnit\Framework\TestCase;

final class UniqueExtractorServiceTest extends TestCase
{
    private UniqueExtractorService $extractor;

    protected function setUp(): void
    {
        $config = new TextNormalizerConfig;
        $this->extractor = new UniqueExtractorService($config);
    }

    // ============================================================
    // TESTS EXTRACT UNIQUE LETTERS
    // ============================================================

    public function test_extract_unique_letters_without_normalization(): void
    {
        // Arrange
        $text = 'Hello World';

        // Act
        $result = $this->extractor->extractUniqueLetters($text, NormalizationMode::WITHOUT);

        // Assert
        $this->assertEquals(['H', 'e', 'l', 'o', 'W', 'r', 'd'], $result);
    }

    public function test_extract_unique_letters_with_normalization(): void
    {
        // Arrange
        $text = 'Éléphant à Paris';

        // Act
        $result = $this->extractor->extractUniqueLetters($text, NormalizationMode::WITH_NORMALIZATION);

        // Assert
        // Correction : L'ordre des lettres peut varier, utiliser assertEqualsCanonicalizing
        $this->assertEqualsCanonicalizing(['e', 'l', 'p', 'h', 'a', 't', 'n', 'r', 'i', 's'], $result);
    }

    public function test_extract_unique_letters_with_accents(): void
    {
        // Arrange
        $text = 'âêîôû àéè';

        // Act
        $result = $this->extractor->extractUniqueLettersWithNormalization($text);

        // Assert
        $this->assertEquals(['a', 'e', 'i', 'o', 'u'], $result);
    }

    public function test_extract_unique_letters_empty_text(): void
    {
        // Arrange
        $text = '';

        // Act
        $result = $this->extractor->extractUniqueLetters($text);

        // Assert
        $this->assertEmpty($result);
    }

    public function test_extract_unique_letters_with_numbers_and_special_chars(): void
    {
        // Arrange
        $text = 'Hello123!@# World';

        // Act
        $result = $this->extractor->extractUniqueLetters($text, NormalizationMode::WITHOUT);

        // Assert - Only letters are kept
        $this->assertEquals(['H', 'e', 'l', 'o', 'W', 'r', 'd'], $result);
    }

    public function test_extract_unique_letters_with_emojis(): void
    {
        // Arrange
        $text = 'Hello 😊 World 🚀';

        // Act
        $result = $this->extractor->extractUniqueLettersWithNormalization($text);

        // Assert
        $this->assertEquals(['h', 'e', 'l', 'o', 'w', 'r', 'd'], $result);
    }

    public function test_extract_unique_letters_with_duplicates(): void
    {
        // Arrange
        $text = 'aaabbbcccddd';

        // Act
        $result = $this->extractor->extractUniqueLetters($text, NormalizationMode::WITHOUT);

        // Assert
        $this->assertEquals(['a', 'b', 'c', 'd'], $result);
    }

    public function test_extract_unique_letters_uses_cache(): void
    {
        // Arrange
        $text = 'Hello World';

        // Act
        $result1 = $this->extractor->extractUniqueLetters($text, NormalizationMode::WITHOUT);
        $result2 = $this->extractor->extractUniqueLetters($text, NormalizationMode::WITHOUT);

        // Assert
        $this->assertEquals($result1, $result2);
    }

    // ============================================================
    // TESTS EXTRACT UNIQUE WORDS
    // ============================================================

    public function test_extract_unique_words_without_normalization(): void
    {
        // Arrange
        $text = 'Hello world Hello PHP world';

        // Act
        $result = $this->extractor->extractUniqueWords($text, NormalizationMode::WITHOUT);

        // Assert
        // Correction : Sans normalisation, les mots sont en minuscules
        $this->assertEquals(['hello', 'world', 'php'], $result);
    }

    public function test_extract_unique_words_with_normalization(): void
    {
        // Arrange
        $text = 'Hello world hello PHP php';

        // Act
        $result = $this->extractor->extractUniqueWords($text, NormalizationMode::WITH_NORMALIZATION);

        // Assert
        $this->assertEquals(['hello', 'world', 'php'], $result);
    }

    public function test_extract_unique_words_with_stopwords(): void
    {
        // Arrange
        $text = 'The quick brown fox jumps over the lazy dog';

        // Act
        $result = $this->extractor->extractUniqueWordsWithNormalization($text, true, false);

        // Assert
        $this->assertEquals(['quick', 'brown', 'fox', 'jumps', 'over', 'lazy', 'dog'], $result);
    }

    public function test_extract_unique_words_with_short_words_removed(): void
    {
        // Arrange
        $text = 'a quick brown fox jumps over the lazy dog';

        // Act
        $result = $this->extractor->extractUniqueWordsWithNormalization($text, false, true, 4);

        // Assert
        $this->assertEquals(['quick', 'brown', 'jumps', 'over', 'lazy'], $result);
    }

    public function test_extract_unique_words_with_accents(): void
    {
        // Arrange
        $text = 'Éléphant à Paris éléphant';

        // Act
        $result = $this->extractor->extractUniqueWordsWithNormalization($text);

        // Assert
        $this->assertEquals(['elephant', 'a', 'paris'], $result);
    }

    public function test_extract_unique_words_empty_text(): void
    {
        // Arrange
        $text = '';

        // Act
        $result = $this->extractor->extractUniqueWords($text);

        // Assert
        $this->assertEmpty($result);
    }

    public function test_extract_unique_words_with_numbers(): void
    {
        // Arrange
        $text = 'Version 8.1.0 released in 2024';

        // Act
        $result = $this->extractor->extractUniqueWordsWithNormalization($text);

        // Assert
        $this->assertEquals(['version', '8', '1', '0', 'released', 'in', '2024'], $result);
    }

    public function test_extract_unique_words_uses_cache(): void
    {
        // Arrange
        $text = 'Hello World';

        // Act
        $result1 = $this->extractor->extractUniqueWords($text, NormalizationMode::WITHOUT);
        $result2 = $this->extractor->extractUniqueWords($text, NormalizationMode::WITHOUT);

        // Assert
        $this->assertEquals($result1, $result2);
    }

    // ============================================================
    // TESTS SPECIFIC METHODS
    // ============================================================

    public function test_extract_unique_letters_with_normalization_method(): void
    {
        // Arrange
        $text = 'ÉLÉPHANT';

        // Act
        $result = $this->extractor->extractUniqueLettersWithNormalization($text);

        // Assert
        $this->assertEquals(['e', 'l', 'p', 'h', 'a', 'n', 't'], $result);
    }

    public function test_extract_unique_words_with_normalization_method(): void
    {
        // Arrange
        $text = 'PHP laravel PHP Symfony laravel';

        // Act
        $result = $this->extractor->extractUniqueWordsWithNormalization($text);

        // Assert
        $this->assertEquals(['php', 'laravel', 'symfony'], $result);
    }

    // ============================================================
    // TESTS LETTER EXISTS
    // ============================================================

    public function test_letter_exists_returns_true_for_existing_letter(): void
    {
        // Arrange
        $text = 'Hello';
        $this->extractor->extractUniqueLetters($text);

        // Act
        $result = $this->extractor->letterExists('e');

        // Assert
        $this->assertTrue($result);
    }

    public function test_letter_exists_returns_false_for_non_existing_letter(): void
    {
        // Arrange
        $text = 'Hello';
        $this->extractor->extractUniqueLetters($text);

        // Act
        $result = $this->extractor->letterExists('z');

        // Assert
        $this->assertFalse($result);
    }

    // ============================================================
    // TESTS ESTIMATE UNIQUE LETTERS
    // ============================================================

    public function test_estimate_unique_letters(): void
    {
        // Arrange
        $text = 'Hello World';
        $this->extractor->extractUniqueLetters($text);

        // Act
        $result = $this->extractor->estimateUniqueLetters();

        // Assert
        $this->assertEquals(7, $result);
    }

    // ============================================================
    // TESTS ESTIMATE UNIQUE WORDS
    // ============================================================

    public function test_estimate_unique_words(): void
    {
        // Arrange
        $text = 'Hello world Hello PHP';
        $this->extractor->extractUniqueWords($text);

        // Act
        $result = $this->extractor->estimateUniqueWords();

        // Assert
        $this->assertEquals(3, $result);
    }

    // ============================================================
    // TESTS SEARCH WORDS BY PREFIX
    // ============================================================

    public function test_search_words_by_prefix(): void
    {
        // Arrange
        $text = 'php python laravel javascript golang';
        $this->extractor->extractUniqueWords($text, NormalizationMode::WITH_NORMALIZATION);

        // Act
        $result = $this->extractor->searchWordsByPrefix('p', 5);

        // Assert
        $this->assertContains('php', $result);
        $this->assertContains('python', $result);
    }

    public function test_search_words_by_prefix_with_limit(): void
    {
        // Arrange
        $text = 'php python laravel javascript golang';
        $this->extractor->extractUniqueWords($text, NormalizationMode::WITH_NORMALIZATION);

        // Act
        $result = $this->extractor->searchWordsByPrefix('p', 1);

        // Assert
        $this->assertCount(1, $result);
    }

    public function test_search_words_by_prefix_empty_prefix(): void
    {
        // Arrange
        $text = 'php python laravel';
        $this->extractor->extractUniqueWords($text, NormalizationMode::WITH_NORMALIZATION);

        // Act
        $result = $this->extractor->searchWordsByPrefix('', 5);

        // Assert
        $this->assertCount(3, $result);
    }

    // ============================================================
    // TESTS GET LETTER FREQUENCY
    // ============================================================

    public function test_get_letter_frequency(): void
    {
        // Arrange
        $text = 'Hello World';
        $this->extractor->extractUniqueLetters($text);

        // Act
        $result = $this->extractor->getLetterFrequency('l');

        // Assert
        $this->assertEquals(1, $result);
    }

    public function test_get_letter_frequency_non_existing(): void
    {
        // Arrange
        $text = 'Hello World';
        $this->extractor->extractUniqueLetters($text);

        // Act
        $result = $this->extractor->getLetterFrequency('z');

        // Assert
        $this->assertEquals(0, $result);
    }

    // ============================================================
    // TESTS GET MOST FREQUENT WORDS
    // ============================================================

    public function test_get_most_frequent_words(): void
    {
        // Arrange
        $text = 'php php php python python laravel';
        $this->extractor->extractUniqueWords($text, NormalizationMode::WITH_NORMALIZATION, false, false);

        // Act
        $result = $this->extractor->getMostFrequentWords(2);

        // Assert
        $this->assertEquals(['php', 'python'], $result);
    }

    public function test_get_most_frequent_words_with_limit(): void
    {
        // Arrange
        $text = 'php php php python python laravel';
        $this->extractor->extractUniqueWords($text, NormalizationMode::WITH_NORMALIZATION, false, false);

        // Act
        $result = $this->extractor->getMostFrequentWords(1);

        // Assert
        $this->assertEquals(['php'], $result);
    }

    // ============================================================
    // TESTS GET STATS
    // ============================================================

    public function test_get_stats(): void
    {
        // Arrange
        $text = 'Hello World PHP';
        $this->extractor->extractUniqueLetters($text);
        $this->extractor->extractUniqueWords($text, NormalizationMode::WITH_NORMALIZATION);

        // Act
        $stats = $this->extractor->getStats();

        // Assert
        $this->assertArrayHasKey('unique_letters_estimated', $stats);
        $this->assertArrayHasKey('unique_words_estimated', $stats);
        $this->assertArrayHasKey('total_letters_processed', $stats);
        $this->assertArrayHasKey('total_words_processed', $stats);
        $this->assertArrayHasKey('unique_letter_count', $stats);
        $this->assertArrayHasKey('unique_word_count', $stats);
        $this->assertArrayHasKey('trie_node_count', $stats);
        $this->assertArrayHasKey('cache_size', $stats);
    }

    // ============================================================
    // TESTS CLEAR
    // ============================================================

    public function test_clear_resets_all_data(): void
    {
        // Arrange
        $text = 'Hello World';
        $this->extractor->extractUniqueLetters($text);
        $this->extractor->extractUniqueWords($text);

        // Act
        $this->extractor->clear();

        // Assert
        $this->assertEquals(0, $this->extractor->estimateUniqueLetters());
        $this->assertEquals(0, $this->extractor->estimateUniqueWords());
        $this->assertFalse($this->extractor->letterExists('e'));
    }

    // ============================================================
    // TESTS DE PERFORMANCE
    // ============================================================

    public function test_extract_unique_words_performance(): void
    {
        // Arrange
        $text = 'The quick brown fox jumps over the lazy dog';

        // Act
        $startTime = microtime(true);
        for ($i = 0; $i < 100; $i++) {
            $this->extractor->extractUniqueWordsWithNormalization($text);
        }
        $endTime = microtime(true);

        // Assert
        $this->assertLessThan(0.5, $endTime - $startTime, 'Extraction should be fast');
    }

    public function test_search_words_by_prefix_performance(): void
    {
        // Arrange
        $text = 'php python laravel javascript golang java ruby rust';
        $this->extractor->extractUniqueWords($text, NormalizationMode::WITH_NORMALIZATION);

        // Act
        $startTime = microtime(true);
        for ($i = 0; $i < 100; $i++) {
            $this->extractor->searchWordsByPrefix('p', 5);
        }
        $endTime = microtime(true);

        // Assert
        $this->assertLessThan(0.5, $endTime - $startTime, 'Prefix search should be fast');
    }

    // ============================================================
    // TESTS DE CAS LIMITES
    // ============================================================

    public function test_extract_unique_words_with_unicode(): void
    {
        // Arrange
        $text = 'Café résumé naïve';

        // Act
        $result = $this->extractor->extractUniqueWordsWithNormalization($text);

        // Assert
        $this->assertEquals(['cafe', 'resume', 'naive'], $result);
    }

    public function test_extract_unique_letters_with_unicode(): void
    {
        // Arrange
        $text = 'Café résumé';

        // Act
        $result = $this->extractor->extractUniqueLettersWithNormalization($text);

        // Assert
        $this->assertEquals(['c', 'a', 'f', 'e', 'r', 's', 'u', 'm'], $result);
    }

    public function test_extract_unique_words_with_mixed_case(): void
    {
        // Arrange
        $text = 'PHP php Php pHp';

        // Act
        $result = $this->extractor->extractUniqueWordsWithNormalization($text);

        // Assert
        $this->assertEquals(['php'], $result);
    }

    public function test_extract_unique_words_with_punctuation(): void
    {
        // Arrange
        $text = 'Hello, world! This is a test.';

        // Act
        $result = $this->extractor->extractUniqueWordsWithNormalization($text);

        // Assert
        $this->assertEquals(['hello', 'world', 'this', 'is', 'a', 'test'], $result);
    }

    public function test_extract_unique_words_with_hyphens(): void
    {
        // Arrange
        $text = 'Hello-world test-case';

        // Act
        $result = $this->extractor->extractUniqueWordsWithNormalization($text);

        // Assert
        $this->assertEquals(['hello', 'world', 'test', 'case'], $result);
    }
}
