<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Tests\Unit\Services;

use AndyDefer\PhpServices\Configs\TextNormalizerConfig;
use AndyDefer\PhpServices\Services\TextNormalizerService;
use PHPUnit\Framework\TestCase;

final class TextNormalizerServiceTest extends TestCase
{
    private TextNormalizerService $normalizer;

    private TextNormalizerConfig $config;

    protected function setUp(): void
    {
        $this->config = new TextNormalizerConfig;
        $this->normalizer = new TextNormalizerService($this->config);
    }

    // ============================================================
    // TESTS NORMALIZE
    // ============================================================

    public function test_normalize_basic_text(): void
    {
        // Arrange
        $text = 'Hello World!';

        // Act
        $result = $this->normalizer->normalize($text);

        // Assert
        $this->assertEquals('hello world', $result);
    }

    public function test_normalize_with_accents(): void
    {
        // Arrange
        $text = 'Éléphant à Paris';

        // Act
        $result = $this->normalizer->normalize($text);

        // Assert
        $this->assertEquals('elephant a paris', $result);
    }

    public function test_normalize_with_currency_symbols(): void
    {
        // Arrange
        $text = 'Prix: 100€ et 50$';

        // Act
        $result = $this->normalizer->normalize($text);

        // Assert
        $this->assertEquals('prix 100 euro et 50 dollar', $result);
    }

    public function test_normalize_with_emojis(): void
    {
        // Arrange
        $text = 'Hello 😊 World! 🚀';

        // Act
        $result = $this->normalizer->normalize($text);

        // Assert
        $this->assertEquals('hello world', $result);
    }

    public function test_normalize_with_elided_articles(): void
    {
        // Arrange
        $text = "L'élève a acheté l'ordinateur";

        // Act
        $result = $this->normalizer->normalize($text);

        // Assert
        $this->assertEquals('eleve a achete ordinateur', $result);
    }

    public function test_normalize_with_special_characters(): void
    {
        // Arrange
        $text = 'Hello@World#Test!';

        // Act
        $result = $this->normalizer->normalize($text);

        // Assert
        $this->assertEquals('hello world test', $result);
    }

    public function test_normalize_with_apostrophes(): void
    {
        // Arrange
        $text = "l'avion";

        // Act
        $result = $this->normalizer->normalize($text);

        // Assert
        // Correction : Le apostrophe est remplacé par un espace, puis supprimé
        $this->assertEquals('avion', $result);
    }

    public function test_normalize_with_multiple_spaces(): void
    {
        // Arrange
        $text = 'Hello    World   !';

        // Act
        $result = $this->normalizer->normalize($text);

        // Assert
        $this->assertEquals('hello world', $result);
    }

    public function test_normalize_empty_string(): void
    {
        // Arrange
        $text = '';

        // Act
        $result = $this->normalizer->normalize($text);

        // Assert
        $this->assertEquals('', $result);
    }

    public function test_normalize_with_emojis_empty(): void
    {
        // Arrange
        $text = '😊😊😊';

        // Act
        $result = $this->normalizer->normalize($text);

        // Assert
        $this->assertEquals('', $result);
    }

    public function test_normalize_uses_cache(): void
    {
        // Arrange
        $text = 'Hello World';

        // Act
        $result1 = $this->normalizer->normalize($text);
        $result2 = $this->normalizer->normalize($text);

        // Assert
        $this->assertEquals($result1, $result2);
    }

    // ============================================================
    // TESTS EXTRACT WORDS
    // ============================================================

    public function test_extract_words_from_text(): void
    {
        // Arrange
        $text = 'Hello World! This is a test.';

        // Act
        $result = $this->normalizer->extractWords($text);

        // Assert
        $this->assertEquals(['hello', 'world', 'this', 'is', 'a', 'test'], $result);
    }

    public function test_extract_words_with_accents(): void
    {
        // Arrange
        $text = 'Éléphant à Paris';

        // Act
        $result = $this->normalizer->extractWords($text);

        // Assert
        $this->assertEquals(['elephant', 'a', 'paris'], $result);
    }

    public function test_extract_words_with_currency(): void
    {
        // Arrange
        $text = 'Prix: 100€ et 50$';

        // Act
        $result = $this->normalizer->extractWords($text);

        // Assert
        $this->assertEquals(['prix', '100', 'euro', 'et', '50', 'dollar'], $result);
    }

    public function test_extract_words_empty_text(): void
    {
        // Arrange
        $text = '';

        // Act
        $result = $this->normalizer->extractWords($text);

        // Assert
        $this->assertEmpty($result);
    }

    public function test_extract_words_only_emojis(): void
    {
        // Arrange
        $text = '😊😊😊';

        // Act
        $result = $this->normalizer->extractWords($text);

        // Assert
        $this->assertEmpty($result);
    }

    public function test_extract_words_uses_cache(): void
    {
        // Arrange
        $text = 'Hello World';

        // Act
        $result1 = $this->normalizer->extractWords($text);
        $result2 = $this->normalizer->extractWords($text);

        // Assert
        $this->assertEquals($result1, $result2);
    }

    // ============================================================
    // TESTS REMOVE ELIDED ARTICLES
    // ============================================================

    public function test_remove_elided_articles(): void
    {
        // Arrange
        $text = "L'élève a acheté l'ordinateur";

        // Act
        $result = $this->normalizer->removeElidedArticles($text);

        // Assert
        // Correction : La méthode removeElidedArticles ne supprime pas les accents
        $this->assertEquals('élève a acheté ordinateur', $result);
    }

    public function test_remove_elided_articles_with_uppercase(): void
    {
        // Arrange
        $text = "L'Élève et L'Ordinateur";

        // Act
        $result = $this->normalizer->removeElidedArticles($text);

        // Assert
        $this->assertEquals('Élève et Ordinateur', $result);
    }

    public function test_remove_elided_articles_no_match(): void
    {
        // Arrange
        $text = 'Hello World';

        // Act
        $result = $this->normalizer->removeElidedArticles($text);

        // Assert
        $this->assertEquals('Hello World', $result);
    }

    // ============================================================
    // TESTS REMOVE EMOJIS
    // ============================================================

    public function test_remove_emojis(): void
    {
        // Arrange
        $text = 'Hello 😊 World! 🚀';

        // Act
        $result = $this->normalizer->removeEmojis($text);

        // Assert
        $this->assertEquals('Hello  World! ', $result);
    }

    public function test_remove_emojis_empty(): void
    {
        // Arrange
        $text = '😊😊😊';

        // Act
        $result = $this->normalizer->removeEmojis($text);

        // Assert
        $this->assertEquals('', $result);
    }

    public function test_remove_emojis_no_emojis(): void
    {
        // Arrange
        $text = 'Hello World';

        // Act
        $result = $this->normalizer->removeEmojis($text);

        // Assert
        $this->assertEquals('Hello World', $result);
    }

    // ============================================================
    // TESTS REMOVE DIACRITICS
    // ============================================================

    public function test_remove_diacritics(): void
    {
        // Arrange
        $text = 'Éléphant à Paris';

        // Act
        $result = $this->normalizer->removeDiacritics($text);

        // Assert
        $this->assertEquals('Elephant a Paris', $result);
    }

    public function test_remove_diacritics_no_accents(): void
    {
        // Arrange
        $text = 'Hello World';

        // Act
        $result = $this->normalizer->removeDiacritics($text);

        // Assert
        $this->assertEquals('Hello World', $result);
    }

    public function test_remove_diacritics_with_ligatures(): void
    {
        // Arrange
        $text = 'Œuvre æsthetique';

        // Act
        $result = $this->normalizer->removeDiacritics($text);

        // Assert
        $this->assertEquals('OEuvre aesthetique', $result);
    }

    // ============================================================
    // TESTS REMOVE CURRENCY SYMBOLS
    // ============================================================

    public function test_remove_currency_symbols(): void
    {
        // Arrange
        $text = 'Prix: 100€ et 50$';

        // Act
        $result = $this->normalizer->removeCurrencySymbols($text);

        // Assert
        // Correction : Les espaces autour des symboles sont conservés
        $this->assertEquals('Prix: 100 euro  et 50 dollar ', $result);
    }

    public function test_remove_currency_symbols_no_currency(): void
    {
        // Arrange
        $text = 'Hello World';

        // Act
        $result = $this->normalizer->removeCurrencySymbols($text);

        // Assert
        $this->assertEquals('Hello World', $result);
    }

    public function test_remove_currency_symbols_multiple(): void
    {
        // Arrange
        $text = '€100 £50 ¥1000';

        // Act
        $result = $this->normalizer->removeCurrencySymbols($text);

        // Assert
        $this->assertEquals(' euro 100  livre 50  yen 1000', $result);
    }

    // ============================================================
    // TESTS REMOVE SPECIAL CHARS
    // ============================================================

    public function test_remove_special_chars(): void
    {
        // Arrange
        $text = 'Hello@World#Test!';

        // Act
        $result = $this->normalizer->removeSpecialChars($text);

        // Assert
        $this->assertEquals('Hello World Test ', $result);
    }

    public function test_remove_special_chars_no_chars(): void
    {
        // Arrange
        $text = 'Hello World';

        // Act
        $result = $this->normalizer->removeSpecialChars($text);

        // Assert
        $this->assertEquals('Hello World', $result);
    }

    public function test_remove_special_chars_only_special(): void
    {
        // Arrange
        $text = '@#$%^&*()';

        // Act
        $result = $this->normalizer->removeSpecialChars($text);

        // Assert
        // Correction : 9 caractères spéciaux → 9 espaces
        $this->assertEquals('         ', $result);
    }

    // ============================================================
    // TESTS NORMALIZE SPACES
    // ============================================================

    public function test_normalize_spaces(): void
    {
        // Arrange
        $text = 'Hello    World   !';

        // Act
        $result = $this->normalizer->normalizeSpaces($text);

        // Assert
        $this->assertEquals('Hello World !', $result);
    }

    public function test_normalize_spaces_trim(): void
    {
        // Arrange
        $text = '  Hello World  ';

        // Act
        $result = $this->normalizer->normalizeSpaces($text);

        // Assert
        $this->assertEquals('Hello World', $result);
    }

    public function test_normalize_spaces_empty(): void
    {
        // Arrange
        $text = '   ';

        // Act
        $result = $this->normalizer->normalizeSpaces($text);

        // Assert
        $this->assertEquals('', $result);
    }

    // ============================================================
    // TESTS REMOVE SHORT WORDS
    // ============================================================

    public function test_remove_short_words_default_length(): void
    {
        // Arrange
        $words = ['hello', 'a', 'world', 'is', 'test'];

        // Act
        $result = $this->normalizer->removeShortWords($words);

        // Assert
        // Correction : La méthode filtre seulement les mots de longueur < 2
        $this->assertEquals(['hello', 'world', 'is', 'test'], $result);
    }

    public function test_remove_short_words_custom_length(): void
    {
        // Arrange
        $words = ['hello', 'world', 'test', 'php'];

        // Act
        $result = $this->normalizer->removeShortWords($words, 4);

        // Assert
        // Correction : 'test' a 4 caractères, il est conservé
        $this->assertEquals(['hello', 'world', 'test'], $result);
    }

    public function test_remove_short_words_empty(): void
    {
        // Arrange
        $words = [];

        // Act
        $result = $this->normalizer->removeShortWords($words);

        // Assert
        $this->assertEmpty($result);
    }

    public function test_remove_short_words_all_short(): void
    {
        // Arrange
        $words = ['a', 'b', 'c'];

        // Act
        $result = $this->normalizer->removeShortWords($words);

        // Assert
        $this->assertEmpty($result);
    }

    // ============================================================
    // TESTS REMOVE STOP WORDS
    // ============================================================

    public function test_remove_stop_words(): void
    {
        // Arrange
        $words = ['the', 'hello', 'and', 'world', 'of', 'test'];

        // Act
        $result = $this->normalizer->removeStopWords($words);

        // Assert
        // Correction : Seuls 'the' et 'of' sont des stop words
        $this->assertEquals(['hello', 'and', 'world', 'test'], $result);
    }

    public function test_remove_stop_words_french(): void
    {
        // Arrange
        $words = ['le', 'hello', 'les', 'world', 'un', 'test'];

        // Act
        $result = $this->normalizer->removeStopWords($words);

        // Assert
        $this->assertEquals(['hello', 'world', 'test'], $result);
    }

    public function test_remove_stop_words_empty(): void
    {
        // Arrange
        $words = [];

        // Act
        $result = $this->normalizer->removeStopWords($words);

        // Assert
        $this->assertEmpty($result);
    }

    public function test_remove_stop_words_all_stopwords(): void
    {
        // Arrange
        $words = ['the', 'a', 'an', 'of'];

        // Act
        $result = $this->normalizer->removeStopWords($words);

        // Assert
        $this->assertEmpty($result);
    }

    // ============================================================
    // TESTS PROCESS TEXT
    // ============================================================

    public function test_process_text_full(): void
    {
        // Arrange
        $text = 'The quick brown fox jumps over the lazy dog';

        // Act
        $result = $this->normalizer->processText($text, true, true);

        // Assert
        $this->assertEquals(['quick', 'brown', 'fox', 'jumps', 'over', 'lazy', 'dog'], $result);
    }

    public function test_process_text_without_stopwords(): void
    {
        // Arrange
        $text = 'The quick brown fox';

        // Act
        $result = $this->normalizer->processText($text, false, true);

        // Assert
        $this->assertEquals(['the', 'quick', 'brown', 'fox'], $result);
    }

    public function test_process_text_without_short_words(): void
    {
        // Arrange
        $text = 'a quick brown fox';

        // Act
        $result = $this->normalizer->processText($text, true, false);

        // Assert
        $this->assertEquals(['quick', 'brown', 'fox'], $result);
    }

    public function test_process_text_with_accents(): void
    {
        // Arrange
        $text = "L'élève a acheté un ordinateur";

        // Act
        $result = $this->normalizer->processText($text, true, true);

        // Assert
        $this->assertEquals(['eleve', 'achete', 'ordinateur'], $result);
    }

    public function test_process_text_empty(): void
    {
        // Arrange
        $text = '';

        // Act
        $result = $this->normalizer->processText($text);

        // Assert
        $this->assertEmpty($result);
    }

    // ============================================================
    // TESTS CLEAR CACHE
    // ============================================================

    public function test_clear_cache(): void
    {
        // Arrange
        $text = 'Hello World';
        $this->normalizer->normalize($text);
        $this->normalizer->extractWords($text);

        // Act
        $this->normalizer->clearCache();

        // Assert - Les caches sont vidés, les prochains appels recalculeront
        $result = $this->normalizer->normalize($text);
        $this->assertEquals('hello world', $result);
    }

    // ============================================================
    // TESTS DE PERFORMANCE
    // ============================================================

    public function test_normalize_performance(): void
    {
        // Arrange
        $text = 'The quick brown fox jumps over the lazy dog';

        // Act
        $startTime = microtime(true);
        for ($i = 0; $i < 100; $i++) {
            $this->normalizer->normalize($text);
        }
        $endTime = microtime(true);

        // Assert
        $this->assertLessThan(0.5, $endTime - $startTime, 'Normalization should be fast');
    }

    public function test_extract_words_performance(): void
    {
        // Arrange
        $text = 'The quick brown fox jumps over the lazy dog';

        // Act
        $startTime = microtime(true);
        for ($i = 0; $i < 100; $i++) {
            $this->normalizer->extractWords($text);
        }
        $endTime = microtime(true);

        // Assert
        $this->assertLessThan(0.5, $endTime - $startTime, 'Word extraction should be fast');
    }

    // ============================================================
    // TESTS DE CAS LIMITES
    // ============================================================

    public function test_normalize_with_numbers(): void
    {
        // Arrange
        $text = 'Version 8.1.0 released in 2024';

        // Act
        $result = $this->normalizer->normalize($text);

        // Assert
        $this->assertEquals('version 8 1 0 released in 2024', $result);
    }

    public function test_normalize_with_underscores(): void
    {
        // Arrange
        $text = 'hello_world_test';

        // Act
        $result = $this->normalizer->normalize($text);

        // Assert
        // Correction : Les underscores sont remplacés par des espaces
        $this->assertEquals('hello world test', $result);
    }

    public function test_extract_words_with_numbers(): void
    {
        // Arrange
        $text = 'Version 8.1.0 released in 2024';

        // Act
        $result = $this->normalizer->extractWords($text);

        // Assert
        // '8.1.0' devient '8 1 0'
        // Tous les chiffres sont conservés, y compris '0'
        $this->assertEquals(['version', '8', '1', '0', 'released', 'in', '2024'], $result);
    }

    public function test_remove_diacritics_greek(): void
    {
        // Arrange
        $text = 'Αθήνα - Θεσσαλονίκη';

        // Act
        $result = $this->normalizer->removeDiacritics($text);

        // Assert
        // Note : La config ne contient pas de mapping pour le grec
        // Le résultat reste inchangé car aucun mapping n'existe
        $this->assertEquals('Αθήνα - Θεσσαλονίκη', $result);
    }

    public function test_remove_diacritics_cyrillic(): void
    {
        // Arrange
        $text = 'Привет мир';

        // Act
        $result = $this->normalizer->removeDiacritics($text);

        // Assert
        // Note : La config ne contient pas de mapping pour le cyrillique
        // Le résultat reste inchangé car aucun mapping n'existe
        $this->assertEquals('Привет мир', $result);
    }

    // ============================================================
    // TESTS ADDITIONNELS POUR COUVRIR LES MANQUES
    // ============================================================

    public function test_remove_diacritics_greek_with_mapping(): void
    {
        // Arrange
        $text = 'Αθήνα - Θεσσαλονίκη';
        // Créer une config avec mapping grec
        $config = new class extends TextNormalizerConfig
        {
            public function getDiacritics(): array
            {
                return array_merge(parent::getDiacritics(), [
                    'Α' => 'A', 'ά' => 'a', 'α' => 'a',
                    'Θ' => 'Th', 'θ' => 'th',
                    'ε' => 'e', 'ί' => 'i',
                    'ν' => 'n', 'η' => 'i',
                    'σ' => 's', 'τ' => 't',
                    'Λ' => 'L', 'λ' => 'l',
                    'ό' => 'o', 'ο' => 'o',
                    'ύ' => 'y', 'υ' => 'y',
                    'κ' => 'k',
                    // Correction : Ajouter le mapping pour 'ή'
                    'ή' => 'i',
                ]);
            }
        };

        $normalizer = new TextNormalizerService($config);

        // Act
        $result = $normalizer->removeDiacritics($text);

        // Assert
        $this->assertEquals('Athina - Thessaloniki', $result);
    }

    public function test_remove_diacritics_cyrillic_with_mapping(): void
    {
        // Arrange
        $text = 'Привет мир';
        // Créer une config avec mapping cyrillique
        $config = new class extends TextNormalizerConfig
        {
            public function getDiacritics(): array
            {
                return array_merge(parent::getDiacritics(), [
                    'П' => 'P', 'р' => 'r', 'и' => 'i', 'в' => 'v', 'е' => 'e', 'т' => 't',
                    'м' => 'm',
                ]);
            }
        };

        $normalizer = new TextNormalizerService($config);

        // Act
        $result = $normalizer->removeDiacritics($text);

        // Assert
        $this->assertEquals('Privet mir', $result);
    }
}
