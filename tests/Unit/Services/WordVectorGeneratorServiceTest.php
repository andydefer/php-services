<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Tests\Unit\Services;

use AndyDefer\DomainStructures\Collections\Utility\FloatTypedCollection;
use AndyDefer\PhpServices\Configs\TextNormalizerConfig;
use AndyDefer\PhpServices\Enums\NormalizationMode;
use AndyDefer\PhpServices\Services\WordVectorGeneratorService;
use PHPUnit\Framework\TestCase;

final class WordVectorGeneratorServiceTest extends TestCase
{
    private WordVectorGeneratorService $generator;

    protected function setUp(): void
    {
        $config = new TextNormalizerConfig;
        $this->generator = new WordVectorGeneratorService($config);
    }

    // ============================================================
    // TESTS GENERATE
    // ============================================================

    public function test_generate_with_normalization(): void
    {
        // Arrange
        $word = 'hello';
        $dimension = 100;

        // Act
        $result = $this->generator->generate($word, $dimension, 2, NormalizationMode::WITH_NORMALIZATION);

        // Assert
        $this->assertInstanceOf(FloatTypedCollection::class, $result);
        $this->assertCount($dimension, $result);

        // Vérifier que le vecteur est normalisé (norme ≈ 1)
        $array = $result->toArray();
        $norm = sqrt(array_sum(array_map(fn ($v) => $v * $v, $array)));
        $this->assertGreaterThan(0.9, $norm);
        $this->assertLessThan(1.1, $norm);
    }

    public function test_generate_without_normalization(): void
    {
        // Arrange
        $word = 'HELLO';
        $dimension = 100;

        // Act
        $result = $this->generator->generate($word, $dimension, 2, NormalizationMode::WITHOUT);

        // Assert
        $this->assertCount($dimension, $result);

        // Vérifier que le vecteur est normalisé
        $array = $result->toArray();
        $norm = sqrt(array_sum(array_map(fn ($v) => $v * $v, $array)));
        $this->assertGreaterThan(0.9, $norm);
        $this->assertLessThan(1.1, $norm);
    }

    public function test_generate_empty_word(): void
    {
        // Arrange
        $word = '';
        $dimension = 100;

        // Act
        $result = $this->generator->generate($word, $dimension);

        // Assert
        $this->assertCount($dimension, $result);
        $this->assertEquals(array_fill(0, $dimension, 0.0), $result->toArray());
    }

    public function test_generate_with_custom_dimension(): void
    {
        // Arrange
        $word = 'test';
        $dimension = 50;

        // Act
        $result = $this->generator->generate($word, $dimension, 2, NormalizationMode::WITH_NORMALIZATION);

        // Assert
        $this->assertCount($dimension, $result);
    }

    public function test_generate_with_bigrams(): void
    {
        // Arrange
        $word = 'hello';
        $dimension = 100;

        // Act
        $result1 = $this->generator->generate($word, $dimension, 2, NormalizationMode::WITH_NORMALIZATION);
        $result2 = $this->generator->generateWithBigrams($word, $dimension, NormalizationMode::WITH_NORMALIZATION);

        // Assert
        $this->assertEquals($result1->toArray(), $result2->toArray());
    }

    public function test_generate_with_trigrams(): void
    {
        // Arrange
        $word = 'hello';
        $dimension = 100;

        // Act
        $result1 = $this->generator->generate($word, $dimension, 3, NormalizationMode::WITH_NORMALIZATION);
        $result2 = $this->generator->generateWithTrigrams($word, $dimension, NormalizationMode::WITH_NORMALIZATION);

        // Assert
        $this->assertEquals($result1->toArray(), $result2->toArray());
    }

    public function test_generate_with_normalization_method(): void
    {
        // Arrange
        $word = 'ÉLÉPHANT';
        $dimension = 100;

        // Act
        $result = $this->generator->generateWithNormalization($word, $dimension, 2);

        // Assert
        $this->assertCount($dimension, $result);

        // Vérifier que le mot a été normalisé (éléphant)
        $array = $result->toArray();
        $norm = sqrt(array_sum(array_map(fn ($v) => $v * $v, $array)));
        $this->assertGreaterThan(0.9, $norm);
        $this->assertLessThan(1.1, $norm);
    }

    // ============================================================
    // TESTS COSINE SIMILARITY
    // ============================================================

    public function test_cosine_similarity_identical_vectors(): void
    {
        // Arrange
        $vector = FloatTypedCollection::from([1.0, 2.0, 3.0]);

        // Act
        $similarity = $this->generator->cosineSimilarity($vector, $vector);

        // Assert
        $this->assertEquals(1.0, $similarity);
    }

    public function test_cosine_similarity_orthogonal_vectors(): void
    {
        // Arrange
        $vector1 = FloatTypedCollection::from([1.0, 0.0, 0.0]);
        $vector2 = FloatTypedCollection::from([0.0, 1.0, 0.0]);

        // Act
        $similarity = $this->generator->cosineSimilarity($vector1, $vector2);

        // Assert
        $this->assertEquals(0.0, $similarity);
    }

    public function test_cosine_similarity_similar_vectors(): void
    {
        // Arrange
        $vector1 = FloatTypedCollection::from([1.0, 2.0, 3.0]);
        $vector2 = FloatTypedCollection::from([2.0, 4.0, 6.0]);

        // Act
        $similarity = $this->generator->cosineSimilarity($vector1, $vector2);

        // Assert
        $this->assertEquals(1.0, $similarity);
    }

    public function test_cosine_similarity_different_dimensions_throws_exception(): void
    {
        // Arrange
        $vector1 = FloatTypedCollection::from([1.0, 2.0, 3.0]);
        $vector2 = FloatTypedCollection::from([1.0, 2.0]);

        // Expect
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Vectors must have the same dimension');

        // Act
        $this->generator->cosineSimilarity($vector1, $vector2);
    }

    public function test_cosine_similarity_with_zero_vector(): void
    {
        // Arrange
        $vector1 = FloatTypedCollection::from([0.0, 0.0, 0.0]);
        $vector2 = FloatTypedCollection::from([1.0, 2.0, 3.0]);

        // Act
        $similarity = $this->generator->cosineSimilarity($vector1, $vector2);

        // Assert
        $this->assertEquals(0.0, $similarity);
    }

    // ============================================================
    // TESTS NORMALIZE VECTOR
    // ============================================================

    public function test_normalize_vector(): void
    {
        // Arrange
        $vector = FloatTypedCollection::from([1.0, 2.0, 3.0]);

        // Act
        $normalized = $this->generator->normalizeVector($vector);

        // Assert
        $array = $normalized->toArray();
        $norm = sqrt(array_sum(array_map(fn ($v) => $v * $v, $array)));
        $this->assertEquals(1.0, $norm);
    }

    public function test_normalize_zero_vector(): void
    {
        // Arrange
        $vector = FloatTypedCollection::from([0.0, 0.0, 0.0]);

        // Act
        $normalized = $this->generator->normalizeVector($vector);

        // Assert
        $this->assertEquals([0.0, 0.0, 0.0], $normalized->toArray());
    }

    // ============================================================
    // TESTS DE COMPARAISON DE MOTS
    // ============================================================

    public function test_similar_words_have_high_similarity(): void
    {
        // Arrange
        $word1 = 'hello';
        $word2 = 'hallo';
        $dimension = 100;

        // Act
        $vector1 = $this->generator->generateWithNormalization($word1, $dimension, 2);
        $vector2 = $this->generator->generateWithNormalization($word2, $dimension, 2);
        $similarity = $this->generator->cosineSimilarity($vector1, $vector2);

        // Assert
        $this->assertGreaterThan(0.3, $similarity, 'Similar words should have some similarity');
    }

    public function test_different_words_have_low_similarity(): void
    {
        // Arrange
        $word1 = 'hello';
        $word2 = 'xyz';
        $dimension = 100;

        // Act
        $vector1 = $this->generator->generateWithNormalization($word1, $dimension, 2);
        $vector2 = $this->generator->generateWithNormalization($word2, $dimension, 2);
        $similarity = $this->generator->cosineSimilarity($vector1, $vector2);

        // Assert
        $this->assertLessThan(0.5, $similarity, 'Different words should have low similarity');
    }

    // ============================================================
    // TESTS DE PERFORMANCE
    // ============================================================

    public function test_generate_performance(): void
    {
        // Arrange
        $word = 'hello';
        $dimension = 100;

        // Act
        $startTime = microtime(true);
        for ($i = 0; $i < 100; $i++) {
            $this->generator->generateWithNormalization($word, $dimension, 2);
        }
        $endTime = microtime(true);

        // Assert
        $this->assertLessThan(0.5, $endTime - $startTime, 'Vector generation should be fast');
    }

    public function test_cosine_similarity_performance(): void
    {
        // Arrange
        $dimension = 1000;
        $vector1 = FloatTypedCollection::from(array_fill(0, $dimension, 1.0));
        $vector2 = FloatTypedCollection::from(array_fill(0, $dimension, 0.5));

        // Act
        $startTime = microtime(true);
        for ($i = 0; $i < 100; $i++) {
            $this->generator->cosineSimilarity($vector1, $vector2);
        }
        $endTime = microtime(true);

        // Assert
        $this->assertLessThan(0.5, $endTime - $startTime, 'Cosine similarity should be fast');
    }

    // ============================================================
    // TESTS DE CAS LIMITES
    // ============================================================

    public function test_generate_with_special_characters(): void
    {
        // Arrange
        $word = 'hello-world!';
        $dimension = 100;

        // Act
        $result = $this->generator->generateWithNormalization($word, $dimension, 2);

        // Assert
        $this->assertCount($dimension, $result);
        $array = $result->toArray();
        $norm = sqrt(array_sum(array_map(fn ($v) => $v * $v, $array)));
        $this->assertGreaterThan(0.9, $norm);
        $this->assertLessThan(1.1, $norm);
    }

    public function test_generate_with_numbers(): void
    {
        // Arrange
        $word = 'hello123';
        $dimension = 100;

        // Act
        $result = $this->generator->generateWithNormalization($word, $dimension, 2);

        // Assert
        $this->assertCount($dimension, $result);
        $array = $result->toArray();
        $norm = sqrt(array_sum(array_map(fn ($v) => $v * $v, $array)));
        $this->assertGreaterThan(0.9, $norm);
        $this->assertLessThan(1.1, $norm);
    }
}
