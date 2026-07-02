<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Tests\Unit\Services;

use AndyDefer\DomainStructures\Collections\Utility\StringTypedCollection;
use AndyDefer\PhpServices\Configs\TextNormalizerConfig;
use AndyDefer\PhpServices\Enums\NormalizationMode;
use AndyDefer\PhpServices\Services\NGramGeneratorService;
use PHPUnit\Framework\TestCase;

final class NGramGeneratorServiceTest extends TestCase
{
    private NGramGeneratorService $generator;

    protected function setUp(): void
    {
        $config = new TextNormalizerConfig;
        $this->generator = new NGramGeneratorService($config);
    }

    // ============================================================
    // TESTS GENERATE
    // ============================================================

    public function test_generate_default_ngrams(): void
    {
        // Arrange
        $text = 'hello';

        // Act
        $result = $this->generator->generate($text, 2, 4, NormalizationMode::WITHOUT);

        // Assert
        $expected = ['he', 'el', 'll', 'lo', 'hel', 'ell', 'llo', 'hell', 'ello'];
        $this->assertEquals($expected, $result->toArray());
    }

    public function test_generate_with_normalization(): void
    {
        // Arrange
        $text = 'ÉLÉPHANT';

        // Act
        $result = $this->generator->generateWithNormalization($text, 2, 3);

        // Assert
        $expected = ['el', 'le', 'ep', 'ph', 'ha', 'an', 'nt', 'ele', 'lep', 'eph', 'pha', 'han', 'ant'];
        $this->assertEquals($expected, $result->toArray());
    }

    public function test_generate_custom_range(): void
    {
        // Arrange
        $text = 'test';

        // Act
        $result = $this->generator->generate($text, 2, 2, NormalizationMode::WITHOUT);

        // Assert
        $this->assertEquals(['te', 'es', 'st'], $result->toArray());
    }

    public function test_generate_empty_text(): void
    {
        // Arrange
        $text = '';

        // Act
        $result = $this->generator->generate($text, 2, 4);

        // Assert
        $this->assertInstanceOf(StringTypedCollection::class, $result);
        $this->assertCount(0, $result);
    }

    public function test_generate_with_invalid_range_throws_exception(): void
    {
        // Arrange
        $text = 'hello';

        // Expect
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('minSize must be less than or equal to maxSize');

        // Act
        $this->generator->generate($text, 4, 2);
    }

    // ============================================================
    // TESTS GENERATE BY SIZE
    // ============================================================

    public function test_generate_bigrams(): void
    {
        // Arrange
        $text = 'hello';

        // Act
        $result = $this->generator->generateBigrams($text, NormalizationMode::WITHOUT);

        // Assert
        $this->assertEquals(['he', 'el', 'll', 'lo'], $result->toArray());
    }

    public function test_generate_bigrams_with_normalization(): void
    {
        // Arrange
        $text = 'ÉLÉPHANT';

        // Act
        $result = $this->generator->generateBigrams($text, NormalizationMode::WITH_NORMALIZATION);

        // Assert
        $this->assertEquals(['el', 'le', 'ep', 'ph', 'ha', 'an', 'nt'], $result->toArray());
    }

    public function test_generate_trigrams(): void
    {
        // Arrange
        $text = 'hello';

        // Act
        $result = $this->generator->generateTrigrams($text, NormalizationMode::WITHOUT);

        // Assert
        $this->assertEquals(['hel', 'ell', 'llo'], $result->toArray());
    }

    public function test_generate_quadrigrams(): void
    {
        // Arrange
        $text = 'hello';

        // Act
        $result = $this->generator->generateQuadrigrams($text, NormalizationMode::WITHOUT);

        // Assert
        $this->assertEquals(['hell', 'ello'], $result->toArray());
    }

    public function test_generate_by_size_invalid_size(): void
    {
        // Arrange
        $text = 'hello';

        // Act
        $result = $this->generator->generateBySize($text, 0, NormalizationMode::WITHOUT);

        // Assert
        $this->assertCount(0, $result);
    }

    // ============================================================
    // TESTS AVEC ACCENTS ET CARACTÈRES SPÉCIAUX
    // ============================================================

    public function test_generate_with_accents(): void
    {
        // Arrange
        $text = 'éléphant';

        // Act
        $result = $this->generator->generateWithNormalization($text, 2, 3);

        // Assert
        $expected = ['el', 'le', 'ep', 'ph', 'ha', 'an', 'nt', 'ele', 'lep', 'eph', 'pha', 'han', 'ant'];
        $this->assertEquals($expected, $result->toArray());
    }

    public function test_generate_with_spaces(): void
    {
        // Arrange
        $text = 'hello world';

        // Act
        $result = $this->generator->generate($text, 2, 2, NormalizationMode::WITHOUT);

        // Assert - Les espaces sont conservés
        $this->assertEquals(['he', 'el', 'll', 'lo', 'o ', ' w', 'wo', 'or', 'rl', 'ld'], $result->toArray());
    }

    public function test_generate_with_numbers(): void
    {
        // Arrange
        $text = '12345';

        // Act
        $result = $this->generator->generate($text, 2, 3, NormalizationMode::WITHOUT);

        // Assert
        $expected = ['12', '23', '34', '45', '123', '234', '345'];
        $this->assertEquals($expected, $result->toArray());
    }

    public function test_generate_single_character(): void
    {
        // Arrange
        $text = 'a';

        // Act
        $result = $this->generator->generate($text, 2, 4);

        // Assert
        $this->assertCount(0, $result);
    }

    // ============================================================
    // TESTS DE PERFORMANCE
    // ============================================================

    public function test_generate_performance(): void
    {
        // Arrange
        $text = 'The quick brown fox jumps over the lazy dog';

        // Act
        $startTime = microtime(true);
        for ($i = 0; $i < 100; $i++) {
            $this->generator->generateWithNormalization($text, 2, 3);
        }
        $endTime = microtime(true);

        // Assert
        $this->assertLessThan(0.5, $endTime - $startTime, 'N-gram generation should be fast');
    }
}
