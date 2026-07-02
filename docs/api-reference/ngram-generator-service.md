# NGramGeneratorService - Référence Technique

## Description

Service de génération de n-grammes pour l'analyse et le traitement de texte. Permet d'extraire des séquences de caractères de longueur variable (bigrammes, trigrammes, quadrigrammes, etc.) avec ou sans normalisation.

## Hiérarchie / Implémentations

```
NGramGeneratorInterface
    └── NGramGeneratorService (final)
```

## Rôle principal

Génère des n-grammes à partir d'un texte pour des applications telles que la recherche floue, l'indexation, la correction orthographique ou l'analyse linguistique. Le service supporte des plages de tailles configurables et offre des méthodes dédiées pour les n-grammes les plus courants.

## Détails

[Voir la classe NGramGeneratorService](https://github.com/andydefer/php-services/blob/main/src/Services/NGramGeneratorService.php)

## API / Méthodes publiques

### `generate(string $text, int $minSize = 2, int $maxSize = 4, NormalizationMode $mode = NormalizationMode::WITHOUT): StringTypedCollection`

Génère tous les n-grammes dans une plage de tailles donnée.

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$text` | `string` | Texte à analyser |
| `$minSize` | `int` | Taille minimale des n-grammes (défaut: 2) |
| `$maxSize` | `int` | Taille maximale des n-grammes (défaut: 4) |
| `$mode` | `NormalizationMode` | Mode de normalisation (WITHOUT ou WITH_NORMALIZATION) |

**Retourne :** `StringTypedCollection` - Collection des n-grammes générés

**Exceptions :** 
- `InvalidArgumentException` - Si `minSize` > `maxSize`

**Exemple :**
```php
$ngrams = $generator->generate('hello', 2, 4);
// Résultat : ['he', 'el', 'll', 'lo', 'hel', 'ell', 'llo', 'hell', 'ello']
```

---

### `generateWithNormalization(string $text, int $minSize = 2, int $maxSize = 4): StringTypedCollection`

Génère des n-grammes avec normalisation du texte (suppression des accents, mise en minuscules, etc.).

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$text` | `string` | Texte à analyser |
| `$minSize` | `int` | Taille minimale des n-grammes (défaut: 2) |
| `$maxSize` | `int` | Taille maximale des n-grammes (défaut: 4) |

**Retourne :** `StringTypedCollection` - Collection des n-grammes normalisés

**Exemple :**
```php
$ngrams = $generator->generateWithNormalization('ÉLÉPHANT', 2, 3);
// Résultat : ['el', 'le', 'ep', 'ph', 'ha', 'an', 'nt', 'ele', 'lep', 'eph', 'pha', 'han', 'ant']
```

---

### `generateBigrams(string $text, NormalizationMode $mode = NormalizationMode::WITHOUT): StringTypedCollection`

Génère les bigrammes (n-grammes de taille 2) du texte.

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$text` | `string` | Texte à analyser |
| `$mode` | `NormalizationMode` | Mode de normalisation |

**Retourne :** `StringTypedCollection` - Collection des bigrammes

**Exemple :**
```php
$bigrams = $generator->generateBigrams('hello');
// Résultat : ['he', 'el', 'll', 'lo']
```

---

### `generateTrigrams(string $text, NormalizationMode $mode = NormalizationMode::WITHOUT): StringTypedCollection`

Génère les trigrammes (n-grammes de taille 3) du texte.

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$text` | `string` | Texte à analyser |
| `$mode` | `NormalizationMode` | Mode de normalisation |

**Retourne :** `StringTypedCollection` - Collection des trigrammes

**Exemple :**
```php
$trigrams = $generator->generateTrigrams('hello');
// Résultat : ['hel', 'ell', 'llo']
```

---

### `generateQuadrigrams(string $text, NormalizationMode $mode = NormalizationMode::WITHOUT): StringTypedCollection`

Génère les quadrigrammes (n-grammes de taille 4) du texte.

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$text` | `string` | Texte à analyser |
| `$mode` | `NormalizationMode` | Mode de normalisation |

**Retourne :** `StringTypedCollection` - Collection des quadrigrammes

**Exemple :**
```php
$quadrigrams = $generator->generateQuadrigrams('hello');
// Résultat : ['hell', 'ello']
```

---

### `generateBySize(string $text, int $size, NormalizationMode $mode = NormalizationMode::WITHOUT): StringTypedCollection`

Génère les n-grammes d'une taille spécifique.

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$text` | `string` | Texte à analyser |
| `$size` | `int` | Taille des n-grammes à générer |
| `$mode` | `NormalizationMode` | Mode de normalisation |

**Retourne :** `StringTypedCollection` - Collection des n-grammes de la taille demandée

**Exemple :**
```php
$ngrams = $generator->generateBySize('hello', 3);
// Résultat : ['hel', 'ell', 'llo']
```

## Cas d'utilisation

### Cas 1 : Recherche floue par n-grammes

**Problème :** Implémenter une recherche tolérante aux fautes de frappe en comparant les n-grammes des mots.

```php
<?php

declare(strict_types=1);

class FuzzySearch
{
    private NGramGeneratorService $generator;
    
    public function __construct(NGramGeneratorService $generator)
    {
        $this->generator = $generator;
    }
    
    public function search(string $query, array $documents): array
    {
        $queryNgrams = $this->generator->generateWithNormalization($query, 2, 3);
        $results = [];
        
        foreach ($documents as $doc) {
            $docNgrams = $this->generator->generateWithNormalization($doc, 2, 3);
            $intersection = array_intersect(
                $queryNgrams->toArray(),
                $docNgrams->toArray()
            );
            
            $score = count($intersection) / count($queryNgrams);
            if ($score > 0.5) {
                $results[] = ['document' => $doc, 'score' => $score];
            }
        }
        
        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);
        return $results;
    }
}

$search = new FuzzySearch($generator);
$results = $search->search('helo', ['hello', 'hell', 'hallo']);
// 'hello' aura un score élevé car il partage des n-grammes
```

### Cas 2 : Indexation pour correction orthographique

**Problème :** Créer un index de n-grammes pour suggérer des corrections de mots.

```php
<?php

declare(strict_types=1);

class SpellChecker
{
    private NGramGeneratorService $generator;
    private array $dictionary = [];
    private array $ngramIndex = [];
    
    public function __construct(NGramGeneratorService $generator)
    {
        $this->generator = $generator;
    }
    
    public function buildDictionary(array $words): void
    {
        foreach ($words as $word) {
            $normalized = strtolower($word);
            $this->dictionary[] = $normalized;
            
            $ngrams = $this->generator->generateBigrams($normalized);
            foreach ($ngrams as $ngram) {
                if (!isset($this->ngramIndex[$ngram])) {
                    $this->ngramIndex[$ngram] = [];
                }
                $this->ngramIndex[$ngram][] = $normalized;
            }
        }
    }
    
    public function suggest(string $word): array
    {
        $word = strtolower($word);
        $ngrams = $this->generator->generateBigrams($word);
        $scores = [];
        
        foreach ($ngrams as $ngram) {
            if (isset($this->ngramIndex[$ngram])) {
                foreach ($this->ngramIndex[$ngram] as $candidate) {
                    $scores[$candidate] = ($scores[$candidate] ?? 0) + 1;
                }
            }
        }
        
        arsort($scores);
        return array_keys($scores);
    }
}

$checker = new SpellChecker($generator);
$checker->buildDictionary(['hello', 'world', 'php', 'laravel']);
$suggestions = $checker->suggest('helo');
// ['hello', 'hell'] 
```

### Cas 3 : Génération de features pour machine learning

**Problème :** Extraire des caractéristiques (features) d'un texte pour un modèle de classification.

```php
<?php

declare(strict_types=1);

class FeatureExtractor
{
    private NGramGeneratorService $generator;
    
    public function __construct(NGramGeneratorService $generator)
    {
        $this->generator = $generator;
    }
    
    public function extract(string $text): array
    {
        return [
            'unigrams' => $this->generator->generateBySize($text, 1),
            'bigrams' => $this->generator->generateBigrams($text),
            'trigrams' => $this->generator->generateTrigrams($text),
            'all_ngrams' => $this->generator->generateWithNormalization($text, 1, 3),
        ];
    }
}

$extractor = new FeatureExtractor($generator);
$features = $extractor->extract('hello world');
```

## Gestion des erreurs

| Situation | Exception | Message |
|-----------|-----------|---------|
| `minSize` > `maxSize` | `InvalidArgumentException` | `minSize must be less than or equal to maxSize` |
| Taille invalide (< 1) | (aucune) | Retourne une collection vide |

**Note :** Ce service ne lève pas d'exceptions pour les textes vides ou les tailles invalides. Il retourne simplement une collection vide.

## Intégration

### Avec TextNormalizerService

Le service utilise `TextNormalizerService` pour la normalisation des textes.

```php
<?php

use AndyDefer\PhpServices\Configs\TextNormalizerConfig;
use AndyDefer\PhpServices\Services\NGramGeneratorService;

$config = new TextNormalizerConfig();
$generator = new NGramGeneratorService($config);
```

### Avec StringTypedCollection

Toutes les méthodes retournent des `StringTypedCollection` pour un typage fort et des opérations fluides.

```php
<?php

$ngrams = $generator->generate('hello', 2, 3);
$array = $ngrams->toArray();
$count = $ngrams->count();
```

## Performance

### Complexité

| Opération | Complexité | Détails |
|-----------|------------|---------|
| `generate()` | O(n × m) | n = longueur du texte, m = nombre de tailles |
| `generateBySize()` | O(n) | Parcourt le texte une fois |
| `generateBigrams()` | O(n) | Parcourt le texte une fois |
| `generateTrigrams()` | O(n) | Parcourt le texte une fois |

### Optimisations

- **Fonctions multibyte** : Utilisation de `mb_strlen()` et `mb_substr()` pour support Unicode
- **Collection typée** : `StringTypedCollection` pour des opérations efficaces
- **Pas de cache** : Les n-grammes sont générés à la demande

### Recommandations

- Utiliser `generateWithNormalization()` pour une normalisation automatique
- Éviter les plages de tailles trop larges (> 5) sur de longs textes
- Préférer `generateBigrams()` ou `generateTrigrams()` pour des cas spécifiques

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
use AndyDefer\PhpServices\Services\NGramGeneratorService;

// Initialisation
$config = new TextNormalizerConfig();
$generator = new NGramGeneratorService($config);

// Texte source
$text = "Bonjour le monde!";

// 1. Génération de tous les n-grammes (2 à 4)
$allNgrams = $generator->generate($text, 2, 4);
echo "N-grammes (2-4): " . implode(', ', $allNgrams->toArray()) . "\n";
// Sortie: Bo, on, nj, jo, ou, ur, r ,  l, le, e ,  m, mo, on, nd, de, e!, 
// Bon, onj, njo, jou, our, ur , r l, le , e m, mo , mon, ond, nde, de!, 

// 2. Génération avec normalisation
$normalizedNgrams = $generator->generateWithNormalization($text, 2, 3);
echo "N-grammes normalisés: " . implode(', ', $normalizedNgrams->toArray()) . "\n";
// Sortie: bo, on, nj, jo, ou, ur, rl, le, em, mo, on, nd, de, bon, onj, njo, jou, our, url, rle, lem, emo, mon, ond, nde

// 3. Bigrammes uniquement
$bigrams = $generator->generateBigrams($text, NormalizationMode::WITH_NORMALIZATION);
echo "Bigrammes: " . implode(', ', $bigrams->toArray()) . "\n";
// Sortie: bo, on, nj, jo, ou, ur, rl, le, em, mo, on, nd, de

// 4. Trigrammes
$trigrams = $generator->generateTrigrams($text, NormalizationMode::WITH_NORMALIZATION);
echo "Trigrammes: " . implode(', ', $trigrams->toArray()) . "\n";
// Sortie: bon, onj, njo, jou, our, url, rle, lem, emo, mon, ond, nde

// 5. Quadrigrammes
$quadrigrams = $generator->generateQuadrigrams($text, NormalizationMode::WITH_NORMALIZATION);
echo "Quadrigrammes: " . implode(', ', $quadrigrams->toArray()) . "\n";
// Sortie: bonj, onjo, njou, jour, ourl, urle, rlem, lemo, emon, mond, onde

// 6. N-grammes d'une taille spécifique
$ngrams = $generator->generateBySize('hello', 2);
echo "N-grammes taille 2: " . implode(', ', $ngrams->toArray()) . "\n";
// Sortie: he, el, ll, lo

// 7. Comparaison de similarité entre mots
function similarity(string $word1, string $word2, NGramGeneratorService $generator): float
{
    $ngrams1 = $generator->generateBigrams($word1, NormalizationMode::WITH_NORMALIZATION);
    $ngrams2 = $generator->generateBigrams($word2, NormalizationMode::WITH_NORMALIZATION);
    
    $intersection = array_intersect($ngrams1->toArray(), $ngrams2->toArray());
    $union = array_unique(array_merge($ngrams1->toArray(), $ngrams2->toArray()));
    
    return count($intersection) / count($union);
}

$sim = similarity('hello', 'hallo', $generator);
echo "Similarité 'hello' vs 'hallo': " . round($sim * 100, 2) . "%\n";
// Sortie: ~60%
```

## Voir aussi

- `TextNormalizerService` - Normalisation des textes
- `TextNormalizerConfig` - Configuration du normaliseur
- `NormalizationMode` - Modes de normalisation
- `StringTypedCollection` - Collection typée pour les résultats