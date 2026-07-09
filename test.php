<?php

declare(strict_types=1);

require './vendor/autoload.php';

use AndyDefer\DomainStructures\Collections\Utility\FloatTypedCollection;
use AndyDefer\PhpServices\Configs\TextNormalizerConfig;
use AndyDefer\PhpServices\Enums\NormalizationMode;
use AndyDefer\PhpServices\Services\WordVectorGeneratorService;

// Initialisation
$config = new TextNormalizerConfig;
$generator = new WordVectorGeneratorService($config);

// 1. Génération de vecteurs
$word1 = 'hello';
$word2 = 'world';
$word3 = 'php';

$vector1 = $generator->generate($word1, 100, 2, NormalizationMode::WITH_NORMALIZATION);
$vector2 = $generator->generate($word2, 100, 2, NormalizationMode::WITH_NORMALIZATION);
$vector3 = $generator->generate($word3, 100, 2, NormalizationMode::WITH_NORMALIZATION);

echo "Vecteur 'hello' dimension: ".count($vector1)."\n";
echo "Vecteur 'hello' norme: ".sqrt(array_sum(array_map(fn ($v) => $v * $v, $vector1->toArray())))."\n";

// 2. Similarité cosinus
$similarity12 = $generator->cosineSimilarity($vector1, $vector2);
$similarity13 = $generator->cosineSimilarity($vector1, $vector3);

echo "Similarité 'hello' vs 'world': ".round($similarity12, 4)."\n";
echo "Similarité 'hello' vs 'php': ".round($similarity13, 4)."\n";

// 3. Avec bigrammes
$bigramVector = $generator->generateWithBigrams('hello', 100);
echo 'Vecteur avec bigrammes: '.count($bigramVector)." dimensions\n";

// 4. Avec trigrammes
$trigramVector = $generator->generateWithTrigrams('hello', 100);
echo 'Vecteur avec trigrammes: '.count($trigramVector)." dimensions\n";

// 5. Normalisation manuelle
$rawVector = FloatTypedCollection::from([1.0, 2.0, 3.0]);
$normalized = $generator->normalizeVector($rawVector);
$norm = sqrt(array_sum(array_map(fn ($v) => $v * $v, $normalized->toArray())));
echo 'Norme du vecteur normalisé: '.round($norm, 4)."\n";

// 6. Calcul de similarité entre groupes de mots
function groupSimilarity(string $word1, string $word2, WordVectorGeneratorService $generator): float
{
    $v1 = $generator->generateWithBigrams($word1, 1000);
    $v2 = $generator->generateWithBigrams($word2, 1000);

    return $generator->cosineSimilarity($v1, $v2);
}

$pairs = [
    ['hello', 'hallo'],
    ['hello', 'world'],
    ['php', 'python'],
    ['php', 'java'],
];

foreach ($pairs as $pair) {
    $sim = groupSimilarity($pair[0], $pair[1], $generator);
    echo "Similarité '{$pair[0]}' vs '{$pair[1]}': ".round($sim, 4)."\n";
}

// 7. Vecteur pour mot vide (retourne un vecteur nul)
$emptyVector = $generator->generate('', 100);
$isEmpty = array_sum($emptyVector->toArray()) === 0.0;
echo 'Vecteur vide: '.($isEmpty ? 'vecteur nul' : 'vecteur non nul')."\n";
