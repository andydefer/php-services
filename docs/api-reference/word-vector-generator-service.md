# WordVectorGeneratorService - Référence Technique

## Description

Service de génération de vecteurs pour les mots utilisant le hachage de n-grammes. Permet de créer des représentations vectorielles de mots pour des applications de similarité sémantique et de machine learning.

## Hiérarchie / Implémentations

```
WordVectorGeneratorInterface
    └── WordVectorGeneratorService (final)
```

## Rôle principal

Transforme un mot en vecteur numérique de dimension fixe en utilisant le hachage de ses n-grammes. Les vecteurs générés peuvent être comparés via la similarité cosinus pour mesurer la proximité sémantique entre les mots.

## Détails

[Voir la classe WordVectorGeneratorService](https://github.com/andydefer/php-services/blob/main/src/Services/WordVectorGeneratorService.php)

## API / Méthodes publiques

### `generate(string $word, int $dimension = 1000, int $nGramSize = 2, NormalizationMode $mode = NormalizationMode::WITH_NORMALIZATION): FloatTypedCollection`

Génère un vecteur pour un mot donné.

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$word` | `string` | Mot à vectoriser |
| `$dimension` | `int` | Dimension du vecteur (défaut: 1000) |
| `$nGramSize` | `int` | Taille des n-grammes (défaut: 2) |
| `$mode` | `NormalizationMode` | Mode de normalisation |

**Retourne :** `FloatTypedCollection` - Vecteur normalisé du mot

**Exemple :**
```php
$vector = $generator->generate('hello', 100, 2);
// Retourne un vecteur de dimension 100
```

---

### `generateWithNormalization(string $word, int $dimension = 1000, int $nGramSize = 2): FloatTypedCollection`

Génère un vecteur avec normalisation automatique.

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$word` | `string` | Mot à vectoriser |
| `$dimension` | `int` | Dimension du vecteur |
| `$nGramSize` | `int` | Taille des n-grammes |

**Retourne :** `FloatTypedCollection` - Vecteur normalisé

**Exemple :**
```php
$vector = $generator->generateWithNormalization('ÉLÉPHANT', 100, 2);
// Mot normalisé en 'éléphant' avant vectorisation
```

---

### `generateWithBigrams(string $word, int $dimension = 1000, NormalizationMode $mode = NormalizationMode::WITH_NORMALIZATION): FloatTypedCollection`

Génère un vecteur en utilisant les bigrammes (n-grammes de taille 2).

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$word` | `string` | Mot à vectoriser |
| `$dimension` | `int` | Dimension du vecteur |
| `$mode` | `NormalizationMode` | Mode de normalisation |

**Retourne :** `FloatTypedCollection` - Vecteur basé sur les bigrammes

**Exemple :**
```php
$vector = $generator->generateWithBigrams('hello', 100);
// Utilise les bigrammes : he, el, ll, lo
```

---

### `generateWithTrigrams(string $word, int $dimension = 1000, NormalizationMode $mode = NormalizationMode::WITH_NORMALIZATION): FloatTypedCollection`

Génère un vecteur en utilisant les trigrammes (n-grammes de taille 3).

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$word` | `string` | Mot à vectoriser |
| `$dimension` | `int` | Dimension du vecteur |
| `$mode` | `NormalizationMode` | Mode de normalisation |

**Retourne :** `FloatTypedCollection` - Vecteur basé sur les trigrammes

**Exemple :**
```php
$vector = $generator->generateWithTrigrams('hello', 100);
// Utilise les trigrammes : hel, ell, llo
```

---

### `cosineSimilarity(FloatTypedCollection $vector1, FloatTypedCollection $vector2): float`

Calcule la similarité cosinus entre deux vecteurs.

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$vector1` | `FloatTypedCollection` | Premier vecteur |
| `$vector2` | `FloatTypedCollection` | Deuxième vecteur |

**Retourne :** `float` - Score de similarité entre 0 et 1

**Exceptions :** 
- `InvalidArgumentException` - Si les vecteurs n'ont pas la même dimension

**Exemple :**
```php
$similarity = $generator->cosineSimilarity($vector1, $vector2);
// 0.85 (les mots sont similaires)
```

---

### `normalizeVector(FloatTypedCollection $vector): FloatTypedCollection`

Normalise un vecteur à la norme unitaire.

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$vector` | `FloatTypedCollection` | Vecteur à normaliser |

**Retourne :** `FloatTypedCollection` - Vecteur normalisé

**Exemple :**
```php
$normalized = $generator->normalizeVector($vector);
// La norme du vecteur est maintenant égale à 1
```

## Cas d'utilisation

### Cas 1 : Recherche de mots similaires

**Problème :** Trouver les mots les plus similaires à un mot donné dans un dictionnaire.

```php
<?php

class WordSimilaritySearch
{
    private WordVectorGeneratorService $generator;
    private array $wordVectors = [];
    
    public function __construct(WordVectorGeneratorService $generator)
    {
        $this->generator = $generator;
    }
    
    public function indexWords(array $words): void
    {
        foreach ($words as $word) {
            $this->wordVectors[$word] = $this->generator->generateWithNormalization($word);
        }
    }
    
    public function findSimilar(string $word, int $limit = 5): array
    {
        $targetVector = $this->generator->generateWithNormalization($word);
        $similarities = [];
        
        foreach ($this->wordVectors as $candidate => $vector) {
            if ($candidate === $word) continue;
            $score = $this->generator->cosineSimilarity($targetVector, $vector);
            $similarities[$candidate] = $score;
        }
        
        arsort($similarities);
        return array_slice($similarities, 0, $limit);
    }
}

$search = new WordSimilaritySearch($generator);
$search->indexWords(['hello', 'world', 'php', 'python', 'java', 'javascript']);
$similar = $search->findSimilar('hello', 3);
// ['world' => 0.45, 'php' => 0.32, 'python' => 0.28]
```

### Cas 2 : Détection de plagiat

**Problème :** Comparer la similarité entre deux textes en utilisant les vecteurs de mots.

```php
<?php

class PlagiarismDetector
{
    private WordVectorGeneratorService $generator;
    
    public function __construct(WordVectorGeneratorService $generator)
    {
        $this->generator = $generator;
    }
    
    public function compare(string $text1, string $text2): float
    {
        $words1 = $this->extractWords($text1);
        $words2 = $this->extractWords($text2);
        
        $vector1 = $this->averageVector($words1);
        $vector2 = $this->averageVector($words2);
        
        return $this->generator->cosineSimilarity($vector1, $vector2);
    }
    
    private function averageVector(array $words): FloatTypedCollection
    {
        $dimension = 100;
        $sum = array_fill(0, $dimension, 0.0);
        $count = 0;
        
        foreach ($words as $word) {
            $vector = $this->generator->generateWithNormalization($word, $dimension);
            $array = $vector->toArray();
            foreach ($array as $i => $value) {
                $sum[$i] += $value;
            }
            $count++;
        }
        
        if ($count === 0) {
            return FloatTypedCollection::from(array_fill(0, $dimension, 0.0));
        }
        
        $avg = array_map(fn($v) => $v / $count, $sum);
        return FloatTypedCollection::from($avg);
    }
    
    private function extractWords(string $text): array
    {
        $words = str_word_count(strtolower($text), 1);
        return array_unique($words);
    }
}

$detector = new PlagiarismDetector($generator);
$similarity = $detector->compare(
    "PHP is a programming language",
    "PHP is a scripting language for web development"
);
echo "Similarité: " . round($similarity * 100, 2) . "%\n";
```

### Cas 3 : Classification de texte simplifiée

**Problème :** Classifier des courts textes en catégories (positif/négatif) basé sur la similarité vectorielle.

```php
<?php

class TextClassifier
{
    private WordVectorGeneratorService $generator;
    private array $categoryVectors = [];
    
    public function __construct(WordVectorGeneratorService $generator)
    {
        $this->generator = $generator;
    }
    
    public function train(array $category, string $text): void
    {
        $words = $this->extractWords($text);
        $vector = $this->averageVector($words);
        $this->categoryVectors[$category] = $vector;
    }
    
    public function predict(string $text): string
    {
        $words = $this->extractWords($text);
        $vector = $this->averageVector($words);
        
        $bestCategory = 'unknown';
        $bestScore = -1;
        
        foreach ($this->categoryVectors as $category => $categoryVector) {
            $score = $this->generator->cosineSimilarity($vector, $categoryVector);
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestCategory = $category;
            }
        }
        
        return $bestCategory;
    }
    
    private function averageVector(array $words): FloatTypedCollection
    {
        $dimension = 100;
        $sum = array_fill(0, $dimension, 0.0);
        
        foreach ($words as $word) {
            $vector = $this->generator->generateWithNormalization($word, $dimension);
            $array = $vector->toArray();
            foreach ($array as $i => $value) {
                $sum[$i] += $value;
            }
        }
        
        $count = count($words);
        if ($count === 0) return FloatTypedCollection::from(array_fill(0, $dimension, 0.0));
        
        $avg = array_map(fn($v) => $v / $count, $sum);
        return FloatTypedCollection::from($avg);
    }
    
    private function extractWords(string $text): array
    {
        $words = str_word_count(strtolower($text), 1);
        return array_unique($words);
    }
}

$classifier = new TextClassifier($generator);
$classifier->train('positive', 'great awesome fantastic wonderful excellent');
$classifier->train('negative', 'bad terrible awful horrible poor');
$result = $classifier->predict('This is a fantastic product'); // 'positive'
```

## Gestion des erreurs

| Situation | Exception | Message |
|-----------|-----------|---------|
| Vecteurs de dimensions différentes | `InvalidArgumentException` | `Vectors must have the same dimension` |

## Performance

### Complexité

| Opération | Complexité | Détails |
|-----------|------------|---------|
| `generate()` | O(n + d) | n = longueur du mot, d = dimension |
| `cosineSimilarity()` | O(d) | d = dimension des vecteurs |
| `normalizeVector()` | O(d) | d = dimension du vecteur |

### Optimisations

- **Hachage CRC32** : Distribution uniforme des n-grammes
- **Normalisation** : Vecteurs de norme unitaire pour similarité stable
- **Dimension configurable** : Compromis précision/performance
- **Collections typées** : `FloatTypedCollection` pour opérations efficaces

### Recommandations

- Dimension 100-300 pour de petites applications
- Dimension 500-1000 pour de meilleures performances
- Utiliser `generateWithBigrams()` pour la plupart des cas
- Utiliser `generateWithTrigrams()` pour plus de précision

## Compatibilité

| Version PHP | Support | Notes |
|-------------|---------|-------|
| PHP 8.5 | ✅ Complet | Support total |
| PHP 8.4 | ✅ Complet | Support total |
| PHP 8.3 | ✅ Complet | Support total |
| PHP 8.2 | ✅ Complet | Support total |
| PHP 8.1 | ✅ Complet | Version minimale requise |

## Exemple complet

```php
<?php

declare(strict_types=1);

use AndyDefer\PhpServices\Configs\TextNormalizerConfig;
use AndyDefer\PhpServices\Enums\NormalizationMode;
use AndyDefer\PhpServices\Services\WordVectorGeneratorService;

// Initialisation
$config = new TextNormalizerConfig();
$generator = new WordVectorGeneratorService($config);

// 1. Génération de vecteurs
$word1 = "hello";
$word2 = "world";
$word3 = "php";

$vector1 = $generator->generate($word1, 100, 2, NormalizationMode::WITH_NORMALIZATION);
$vector2 = $generator->generate($word2, 100, 2, NormalizationMode::WITH_NORMALIZATION);
$vector3 = $generator->generate($word3, 100, 2, NormalizationMode::WITH_NORMALIZATION);

echo "Vecteur 'hello' dimension: " . count($vector1) . "\n";
echo "Vecteur 'hello' norme: " . sqrt(array_sum(array_map(fn($v) => $v * $v, $vector1->toArray()))) . "\n";

// 2. Similarité cosinus
$similarity12 = $generator->cosineSimilarity($vector1, $vector2);
$similarity13 = $generator->cosineSimilarity($vector1, $vector3);

echo "Similarité 'hello' vs 'world': " . round($similarity12, 4) . "\n";
echo "Similarité 'hello' vs 'php': " . round($similarity13, 4) . "\n";

// 3. Avec bigrammes
$bigramVector = $generator->generateWithBigrams('hello', 100);
echo "Vecteur avec bigrammes: " . count($bigramVector) . " dimensions\n";

// 4. Avec trigrammes
$trigramVector = $generator->generateWithTrigrams('hello', 100);
echo "Vecteur avec trigrammes: " . count($trigramVector) . " dimensions\n";

// 5. Normalisation manuelle
$rawVector = FloatTypedCollection::from([1.0, 2.0, 3.0]);
$normalized = $generator->normalizeVector($rawVector);
$norm = sqrt(array_sum(array_map(fn($v) => $v * $v, $normalized->toArray())));
echo "Norme du vecteur normalisé: " . round($norm, 4) . "\n";

// 6. Calcul de similarité entre groupes de mots
function groupSimilarity(string $word1, string $word2, WordVectorGeneratorService $generator): float
{
    $v1 = $generator->generateWithBigrams($word1, 100);
    $v2 = $generator->generateWithBigrams($word2, 100);
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
    echo "Similarité '{$pair[0]}' vs '{$pair[1]}': " . round($sim, 4) . "\n";
}

// 7. Vecteur pour mot vide (retourne un vecteur nul)
$emptyVector = $generator->generate('', 100);
$isEmpty = array_sum($emptyVector->toArray()) === 0.0;
echo "Vecteur vide: " . ($isEmpty ? 'vecteur nul' : 'vecteur non nul') . "\n";
```

## Voir aussi

- `NGramGeneratorService` - Génération de n-grammes
- `TextNormalizerService` - Normalisation des textes
- `FloatTypedCollection` - Collection typée de flottants
- `NormalizationMode` - Modes de normalisation