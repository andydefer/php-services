<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Configs;

use AndyDefer\PhpServices\Contracts\Services\SimilarityConfigInterface;

/**
 * Configuration for similarity calculation service.
 *
 * Provides all configurable parameters for the similarity algorithm:
 * - N-gram generation (min/max size)
 * - Vector dimension for hashing
 * - Weight distribution between textual and phonetic similarity
 * - Bonus multipliers for common letters and bigrams
 * - Letter and gram weighting
 * - Performance limits (max words, max pairs, timeout)
 * - Levenshtein bonus thresholds and values
 *
 * All values can be overridden via setters.
 */
final class SimilarityConfig implements SimilarityConfigInterface
{
    // ============================================================================
    // Default values
    // ============================================================================

    private const DEFAULT_GRAM_MIN_SIZE = 2;

    private const DEFAULT_GRAM_MAX_SIZE = 4;

    private const DEFAULT_VECTOR_DIMENSION = 128;

    private const DEFAULT_TEXTUAL_WEIGHT = 0.6;

    private const DEFAULT_PHONETIC_WEIGHT = 0.4;

    private const DEFAULT_LETTER_BONUS = 0.05;

    private const DEFAULT_BIGRAM_BONUS = 0.03;

    private const DEFAULT_MIN_WORD_LENGTH = 2;

    private const DEFAULT_MAX_WORDS = 50;

    private const DEFAULT_MAX_PAIRS = 2500;

    private const DEFAULT_TIMEOUT_SECONDS = 0.5;

    // Levenshtein bonus defaults
    private const DEFAULT_METAPHONE_BONUS_THRESHOLD = 3;

    private const DEFAULT_METAPHONE_BONUS_VALUE = 0.175;

    private const DEFAULT_LEXICAL_BONUS_THRESHOLD = 3;

    private const DEFAULT_LEXICAL_BONUS_MEDIUM = 0.225;

    private const DEFAULT_LEXICAL_BONUS_HIGH = 0.275;

    private const DEFAULT_MAX_LEVENSHTEIN_BONUS = 0.45;

    private const DEFAULT_GRAM_WEIGHTS = [
        2 => 0.3,
        3 => 0.5,
        4 => 0.7,
        'default' => 1.0,
    ];

    private const DEFAULT_LETTER_WEIGHTS = [
        'e' => 15.0, 'a' => 7.5, 's' => 7.5, 'i' => 7.0,
        'n' => 7.0, 't' => 7.0, 'r' => 6.5, 'u' => 6.0,
        'l' => 5.0, 'o' => 5.0, 'd' => 3.5, 'c' => 3.5,
        'p' => 3.0, 'm' => 3.0, 'v' => 2.0,
        'q' => 1.0, 'g' => 1.0, 'b' => 1.0, 'f' => 1.0,
        'h' => 0.75, 'j' => 0.75,
        'z' => 0.25, 'w' => 0.25, 'k' => 0.25, 'y' => 0.25, 'x' => 0.5,
        'é' => 4.0, 'è' => 3.0, 'ê' => 2.0, 'à' => 1.5,
        'ù' => 1.0, 'ç' => 1.5, 'â' => 1.5, 'î' => 1.0,
        'ô' => 1.0, 'û' => 0.5, 'ë' => 0.5, 'ï' => 0.5, 'ü' => 0.5,
    ];

    // ============================================================================
    // Clamping bounds
    // ============================================================================

    private const CLAMP_GRAM_MIN_SIZE = [1, 10];

    private const CLAMP_GRAM_MAX_SIZE = [1, 10];

    private const CLAMP_VECTOR_DIMENSION = [16, 4096];

    private const CLAMP_TEXTUAL_WEIGHT = [0.0, 1.0];

    private const CLAMP_PHONETIC_WEIGHT = [0.0, 1.0];

    private const CLAMP_LETTER_BONUS = [0.0, 0.5];

    private const CLAMP_BIGRAM_BONUS = [0.0, 0.5];

    private const CLAMP_MIN_WORD_LENGTH = [1, 10];

    private const CLAMP_MAX_WORDS = [1, 500];

    private const CLAMP_MAX_PAIRS = [10, 100000];

    private const CLAMP_TIMEOUT_SECONDS = [0.01, 10.0];

    private const CLAMP_METAPHONE_BONUS_THRESHOLD = [1, 10];

    private const CLAMP_METAPHONE_BONUS_VALUE = [0.0, 1.0];

    private const CLAMP_LEXICAL_BONUS_THRESHOLD = [1, 10];

    private const CLAMP_LEXICAL_BONUS_MEDIUM = [0.0, 1.0];

    private const CLAMP_LEXICAL_BONUS_HIGH = [0.0, 1.0];

    private const CLAMP_MAX_LEVENSHTEIN_BONUS = [0.0, 1.0];

    // ============================================================================
    // Properties
    // ============================================================================

    private int $gramMinSize;

    private int $gramMaxSize;

    private int $vectorDimension;

    private float $textualWeight;

    private float $phoneticWeight;

    private float $letterBonus;

    private float $bigramBonus;

    private int $minWordLength;

    private int $maxWords;

    private int $maxPairs;

    private float $timeoutSeconds;

    private int $metaphoneBonusThreshold;

    private float $metaphoneBonusValue;

    private int $lexicalBonusThreshold;

    private float $lexicalBonusMedium;

    private float $lexicalBonusHigh;

    private float $maxLevenshteinBonus;

    private array $letterWeights;

    private array $gramWeights;

    // ============================================================================
    // Constructor
    // ============================================================================

    public function __construct()
    {
        $this->resetToDefaults();
    }

    // ============================================================================
    // Private helpers
    // ============================================================================

    /**
     * Clamps a value between minimum and maximum bounds.
     *
     * @param  int|float  $value  The value to clamp
     * @param  int|float  $min  The minimum allowed value
     * @param  int|float  $max  The maximum allowed value
     * @return int|float The clamped value
     */
    private function clamp(int|float $value, int|float $min, int|float $max): int|float
    {
        if ($value < $min) {
            return $min;
        }

        if ($value > $max) {
            return $max;
        }

        return $value;
    }

    /**
     * Reset all values to defaults.
     */
    private function resetToDefaults(): void
    {
        $this->gramMinSize = self::DEFAULT_GRAM_MIN_SIZE;
        $this->gramMaxSize = self::DEFAULT_GRAM_MAX_SIZE;
        $this->vectorDimension = self::DEFAULT_VECTOR_DIMENSION;
        $this->textualWeight = self::DEFAULT_TEXTUAL_WEIGHT;
        $this->phoneticWeight = self::DEFAULT_PHONETIC_WEIGHT;
        $this->letterBonus = self::DEFAULT_LETTER_BONUS;
        $this->bigramBonus = self::DEFAULT_BIGRAM_BONUS;
        $this->minWordLength = self::DEFAULT_MIN_WORD_LENGTH;
        $this->maxWords = self::DEFAULT_MAX_WORDS;
        $this->maxPairs = self::DEFAULT_MAX_PAIRS;
        $this->timeoutSeconds = self::DEFAULT_TIMEOUT_SECONDS;
        $this->metaphoneBonusThreshold = self::DEFAULT_METAPHONE_BONUS_THRESHOLD;
        $this->metaphoneBonusValue = self::DEFAULT_METAPHONE_BONUS_VALUE;
        $this->lexicalBonusThreshold = self::DEFAULT_LEXICAL_BONUS_THRESHOLD;
        $this->lexicalBonusMedium = self::DEFAULT_LEXICAL_BONUS_MEDIUM;
        $this->lexicalBonusHigh = self::DEFAULT_LEXICAL_BONUS_HIGH;
        $this->maxLevenshteinBonus = self::DEFAULT_MAX_LEVENSHTEIN_BONUS;
        $this->letterWeights = self::DEFAULT_LETTER_WEIGHTS;
        $this->gramWeights = self::DEFAULT_GRAM_WEIGHTS;
    }

    // ============================================================================
    // Getters (Interface)
    // ============================================================================

    public function getGramMinSize(): int
    {
        return $this->gramMinSize;
    }

    public function getGramMaxSize(): int
    {
        return $this->gramMaxSize;
    }

    public function getVectorDimension(): int
    {
        return $this->vectorDimension;
    }

    public function getTextualWeight(): float
    {
        return $this->textualWeight;
    }

    public function getPhoneticWeight(): float
    {
        return $this->phoneticWeight;
    }

    public function getLetterBonus(): float
    {
        return $this->letterBonus;
    }

    public function getBigramBonus(): float
    {
        return $this->bigramBonus;
    }

    public function getMinWordLength(): int
    {
        return $this->minWordLength;
    }

    public function getMaxWords(): int
    {
        return $this->maxWords;
    }

    public function getMaxPairs(): int
    {
        return $this->maxPairs;
    }

    public function getTimeoutSeconds(): float
    {
        return $this->timeoutSeconds;
    }

    public function getMetaphoneBonusThreshold(): int
    {
        return $this->metaphoneBonusThreshold;
    }

    public function getMetaphoneBonusValue(): float
    {
        return $this->metaphoneBonusValue;
    }

    public function getLexicalBonusThreshold(): int
    {
        return $this->lexicalBonusThreshold;
    }

    public function getLexicalBonusMedium(): float
    {
        return $this->lexicalBonusMedium;
    }

    public function getLexicalBonusHigh(): float
    {
        return $this->lexicalBonusHigh;
    }

    public function getMaxLevenshteinBonus(): float
    {
        return $this->maxLevenshteinBonus;
    }

    public function getLetterWeight(string $letter): float
    {
        return $this->letterWeights[$letter] ?? 0.5;
    }

    public function getGramWeight(int $length): float
    {
        if (isset($this->gramWeights[$length])) {
            return (float) $this->gramWeights[$length];
        }

        return (float) ($this->gramWeights['default'] ?? 1.0);
    }

    // ============================================================================
    // Setters (Fluent interface)
    // ============================================================================

    public function setGramMinSize(int $value): self
    {
        [$min, $max] = self::CLAMP_GRAM_MIN_SIZE;
        $this->gramMinSize = $this->clamp($value, $min, $max);

        return $this;
    }

    public function setGramMaxSize(int $value): self
    {
        [$min, $max] = self::CLAMP_GRAM_MAX_SIZE;
        $this->gramMaxSize = $this->clamp($value, $min, $max);

        return $this;
    }

    public function setVectorDimension(int $value): self
    {
        [$min, $max] = self::CLAMP_VECTOR_DIMENSION;
        $this->vectorDimension = $this->clamp($value, $min, $max);

        return $this;
    }

    public function setTextualWeight(float $value): self
    {
        [$min, $max] = self::CLAMP_TEXTUAL_WEIGHT;
        $this->textualWeight = $this->clamp($value, $min, $max);

        return $this;
    }

    public function setPhoneticWeight(float $value): self
    {
        [$min, $max] = self::CLAMP_PHONETIC_WEIGHT;
        $this->phoneticWeight = $this->clamp($value, $min, $max);

        return $this;
    }

    public function setLetterBonus(float $value): self
    {
        [$min, $max] = self::CLAMP_LETTER_BONUS;
        $this->letterBonus = $this->clamp($value, $min, $max);

        return $this;
    }

    public function setBigramBonus(float $value): self
    {
        [$min, $max] = self::CLAMP_BIGRAM_BONUS;
        $this->bigramBonus = $this->clamp($value, $min, $max);

        return $this;
    }

    public function setMinWordLength(int $value): self
    {
        [$min, $max] = self::CLAMP_MIN_WORD_LENGTH;
        $this->minWordLength = $this->clamp($value, $min, $max);

        return $this;
    }

    public function setMaxWords(int $value): self
    {
        [$min, $max] = self::CLAMP_MAX_WORDS;
        $this->maxWords = $this->clamp($value, $min, $max);

        return $this;
    }

    public function setMaxPairs(int $value): self
    {
        [$min, $max] = self::CLAMP_MAX_PAIRS;
        $this->maxPairs = $this->clamp($value, $min, $max);

        return $this;
    }

    public function setTimeoutSeconds(float $value): self
    {
        [$min, $max] = self::CLAMP_TIMEOUT_SECONDS;
        $this->timeoutSeconds = $this->clamp($value, $min, $max);

        return $this;
    }

    public function setMetaphoneBonusThreshold(int $value): self
    {
        [$min, $max] = self::CLAMP_METAPHONE_BONUS_THRESHOLD;
        $this->metaphoneBonusThreshold = $this->clamp($value, $min, $max);

        return $this;
    }

    public function setMetaphoneBonusValue(float $value): self
    {
        [$min, $max] = self::CLAMP_METAPHONE_BONUS_VALUE;
        $this->metaphoneBonusValue = $this->clamp($value, $min, $max);

        return $this;
    }

    public function setLexicalBonusThreshold(int $value): self
    {
        [$min, $max] = self::CLAMP_LEXICAL_BONUS_THRESHOLD;
        $this->lexicalBonusThreshold = $this->clamp($value, $min, $max);

        return $this;
    }

    public function setLexicalBonusMedium(float $value): self
    {
        [$min, $max] = self::CLAMP_LEXICAL_BONUS_MEDIUM;
        $this->lexicalBonusMedium = $this->clamp($value, $min, $max);

        return $this;
    }

    public function setLexicalBonusHigh(float $value): self
    {
        [$min, $max] = self::CLAMP_LEXICAL_BONUS_HIGH;
        $this->lexicalBonusHigh = $this->clamp($value, $min, $max);

        return $this;
    }

    public function setMaxLevenshteinBonus(float $value): self
    {
        [$min, $max] = self::CLAMP_MAX_LEVENSHTEIN_BONUS;
        $this->maxLevenshteinBonus = $this->clamp($value, $min, $max);

        return $this;
    }

    /**
     * Set letter weights (merges with defaults).
     *
     * @param  array<string, float>  $weights
     */
    public function setLetterWeights(array $weights): self
    {
        $this->letterWeights = array_merge(self::DEFAULT_LETTER_WEIGHTS, $weights);

        return $this;
    }

    /**
     * Set gram weights (merges with defaults).
     *
     * @param  array<int|string, float>  $weights
     */
    public function setGramWeights(array $weights): self
    {
        $this->gramWeights = array_merge(self::DEFAULT_GRAM_WEIGHTS, $weights);

        return $this;
    }

    /**
     * Reset all values to defaults.
     */
    public function reset(): self
    {
        $this->resetToDefaults();

        return $this;
    }

    /**
     * Set multiple configuration values at once.
     */
    public function configure(array $config): self
    {
        $map = [
            'gram_min_size' => 'setGramMinSize',
            'gram_max_size' => 'setGramMaxSize',
            'vector_dimension' => 'setVectorDimension',
            'textual_weight' => 'setTextualWeight',
            'phonetic_weight' => 'setPhoneticWeight',
            'letter_bonus' => 'setLetterBonus',
            'bigram_bonus' => 'setBigramBonus',
            'min_word_length' => 'setMinWordLength',
            'max_words' => 'setMaxWords',
            'max_pairs' => 'setMaxPairs',
            'timeout_seconds' => 'setTimeoutSeconds',
            'metaphone_threshold' => 'setMetaphoneBonusThreshold',
            'metaphone_bonus' => 'setMetaphoneBonusValue',
            'lexical_threshold' => 'setLexicalBonusThreshold',
            'lexical_bonus_medium' => 'setLexicalBonusMedium',
            'lexical_bonus_high' => 'setLexicalBonusHigh',
            'max_levenshtein_bonus' => 'setMaxLevenshteinBonus',
            'letter_weights' => 'setLetterWeights',
            'gram_weights' => 'setGramWeights',
        ];

        foreach ($map as $key => $method) {
            if (array_key_exists($key, $config)) {
                $this->$method($config[$key]);
            }
        }

        return $this;
    }
}
