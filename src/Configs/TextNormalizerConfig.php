<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Configs;

use AndyDefer\PhpServices\Contracts\TextNormalizerConfigInterface;

class TextNormalizerConfig implements TextNormalizerConfigInterface
{
    private array $elidedArticles = ["l'", "L'", "d'", "D'"];

    private array $diacritics = [
        'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A',
        'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
        'Ç' => 'C', 'ç' => 'c',
        'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
        'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
        'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
        'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
        'Ñ' => 'N', 'ñ' => 'n',
        'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O',
        'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o',
        'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U',
        'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
        'Ý' => 'Y', 'Ÿ' => 'Y', 'ý' => 'y', 'ÿ' => 'y',
        'Æ' => 'AE', 'æ' => 'ae',
        'Œ' => 'OE', 'œ' => 'oe',
        'ß' => 'ss',
    ];

    private array $currencySymbols = [
        '€' => ' euro ',
        '£' => ' livre ',
        '¥' => ' yen ',
        '$' => ' dollar ',
        '¢' => ' cent ',
    ];

    private array $stopWords = [
        'le', 'la', 'les', 'un', 'une', 'des',
        'et', 'ou', 'mais', 'donc', 'or', 'car',
        'the', 'a', 'an', 'of', 'for', 'on', 'at',
    ];

    public function getElidedArticles(): array
    {
        return $this->elidedArticles;
    }

    public function getDiacritics(): array
    {
        return $this->diacritics;
    }

    public function getCurrencySymbols(): array
    {
        return $this->currencySymbols;
    }

    public function getStopWords(): array
    {
        return $this->stopWords;
    }

    public function isStopWord(string $word): bool
    {
        return in_array(strtolower($word), $this->stopWords, true);
    }

    public function getMinWordLength(): int
    {
        return 2;
    }
}
