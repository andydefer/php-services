# UniqueExtractorService - Référence Technique

## Description

Service d'extraction des éléments uniques (lettres et mots) d'un texte avec support de la normalisation, de la recherche par préfixe et de l'analyse de fréquence.

## Hiérarchie / Implémentations

```
UniqueExtractorInterface
    └── UniqueExtractorService (final)
```

## Rôle principal

Extrait les lettres et mots uniques d'un texte avec ou sans normalisation. Le service maintient des caches, un index de fréquence et une structure de type Trie pour la recherche par préfixe, le tout sans dépendances externes.

## Détails

[Voir la classe UniqueExtractorService](https://github.com/andydefer/php-services/blob/main/src/Services/UniqueExtractorService.php)

## API / Méthodes publiques

### `extractUniqueLetters(string $text, NormalizationMode $mode = NormalizationMode::WITHOUT): array`

Extrait les lettres uniques d'un texte.

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$text` | `string` | Texte à analyser |
| `$mode` | `NormalizationMode` | Mode de normalisation (défaut: WITHOUT) |

**Retourne :** `array<string>` - Liste des lettres uniques

**Exemple :**
```php
$letters = $extractor->extractUniqueLetters('Hello World');
// Résultat : ['H', 'e', 'l', 'o', 'W', 'r', 'd']
```

---

### `extractUniqueWords(string $text, NormalizationMode $mode = NormalizationMode::WITHOUT, bool $removeStopWords = false, bool $removeShortWords = false, int $minWordLength = 2): array`

Extrait les mots uniques d'un texte avec options de filtrage.

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$text` | `string` | Texte à analyser |
| `$mode` | `NormalizationMode` | Mode de normalisation |
| `$removeStopWords` | `bool` | Supprimer les stop-words |
| `$removeShortWords` | `bool` | Supprimer les mots courts |
| `$minWordLength` | `int` | Longueur minimale des mots |

**Retourne :** `array<string>` - Liste des mots uniques

**Exemple :**
```php
$words = $extractor->extractUniqueWords('The quick brown fox', NormalizationMode::WITH_NORMALIZATION);
// Résultat : ['the', 'quick', 'brown', 'fox']
```

---

### `extractUniqueLettersWithNormalization(string $text): array`

Extrait les lettres uniques avec normalisation (minuscules, suppression des accents).

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$text` | `string` | Texte à analyser |

**Retourne :** `array<string>` - Liste des lettres uniques normalisées

**Exemple :**
```php
$letters = $extractor->extractUniqueLettersWithNormalization('ÉLÉPHANT');
// Résultat : ['e', 'l', 'p', 'h', 'a', 'n', 't']
```

---

### `extractUniqueWordsWithNormalization(string $text, bool $removeStopWords = false, bool $removeShortWords = false, int $minWordLength = 2): array`

Extrait les mots uniques avec normalisation.

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$text` | `string` | Texte à analyser |
| `$removeStopWords` | `bool` | Supprimer les stop-words |
| `$removeShortWords` | `bool` | Supprimer les mots courts |
| `$minWordLength` | `int` | Longueur minimale des mots |

**Retourne :** `array<string>` - Liste des mots uniques normalisés

**Exemple :**
```php
$words = $extractor->extractUniqueWordsWithNormalization('PHP laravel PHP Symfony');
// Résultat : ['php', 'laravel', 'symfony']
```

---

### `letterExists(string $letter): bool`

Vérifie si une lettre a déjà été rencontrée.

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$letter` | `string` | Lettre à vérifier |

**Retourne :** `bool` - True si la lettre existe

**Exemple :**
```php
$exists = $extractor->letterExists('e'); // true
$exists = $extractor->letterExists('z'); // false
```

---

### `estimateUniqueLetters(): int`

Estime le nombre de lettres uniques rencontrées.

**Retourne :** `int` - Nombre de lettres uniques

**Exemple :**
```php
$count = $extractor->estimateUniqueLetters(); // 7
```

---

### `estimateUniqueWords(): int`

Estime le nombre de mots uniques rencontrés.

**Retourne :** `int` - Nombre de mots uniques

**Exemple :**
```php
$count = $extractor->estimateUniqueWords(); // 3
```

---

### `searchWordsByPrefix(string $prefix, int $limit = 10): array`

Recherche les mots commençant par un préfixe donné.

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$prefix` | `string` | Préfixe à rechercher |
| `$limit` | `int` | Nombre maximum de résultats |

**Retourne :** `array<string>` - Liste des mots correspondants

**Exemple :**
```php
$words = $extractor->searchWordsByPrefix('p', 5);
// Résultat : ['php', 'python', 'perl']
```

---

### `getLetterFrequency(string $letter): int`

Obtient la fréquence d'une lettre.

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$letter` | `string` | Lettre à analyser |

**Retourne :** `int` - Fréquence de la lettre

**Exemple :**
```php
$freq = $extractor->getLetterFrequency('l'); // 2
```

---

### `getMostFrequentWords(int $limit = 10): array`

Obtient les mots les plus fréquents.

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$limit` | `int` | Nombre maximum de résultats |

**Retourne :** `array<string>` - Liste des mots les plus fréquents

**Exemple :**
```php
$words = $extractor->getMostFrequentWords(3);
// Résultat : ['php', 'python', 'laravel']
```

---

### `getStats(): array`

Obtient les statistiques des structures de données internes.

**Retourne :** `array<string, mixed>` - Statistiques

**Exemple :**
```php
$stats = $extractor->getStats();
// Résultat :
// [
//     'unique_letters_estimated' => 10,
//     'unique_words_estimated' => 5,
//     'total_letters_processed' => 25,
//     'total_words_processed' => 15,
//     'unique_letter_count' => 8,
//     'unique_word_count' => 4,
//     'trie_node_count' => 12,
//     'cache_size' => 3
// ]
```

---

### `clear(): void`

Vide toutes les structures de données et caches internes.

**Retourne :** `void`

**Exemple :**
```php
$extractor->clear();
```

## Cas d'utilisation

### Cas 1 : Analyse de contenu pour SEO

**Problème :** Analyser un texte pour extraire les mots-clés uniques et leurs fréquences.

```php
<?php

class SEOAnalyzer
{
    private UniqueExtractorService $extractor;
    
    public function __construct(UniqueExtractorService $extractor)
    {
        $this->extractor = $extractor;
    }
    
    public function analyze(string $content): array
    {
        $words = $this->extractor->extractUniqueWordsWithNormalization(
            $content,
            removeStopWords: true,
            removeShortWords: true,
            minWordLength: 3
        );
        
        $frequencies = [];
        foreach ($words as $word) {
            $frequencies[$word] = $this->extractor->getLetterFrequency($word[0]);
        }
        
        return [
            'unique_words' => $words,
            'total_unique' => count($words),
            'letter_frequencies' => $frequencies,
            'stats' => $this->extractor->getStats(),
        ];
    }
}

$analyzer = new SEOAnalyzer($extractor);
$analysis = $analyzer->analyze($articleContent);
```

### Cas 2 : Autocomplétion de recherche

**Problème :** Implémenter un système d'autocomplétion basé sur les mots déjà indexés.

```php
<?php

class AutocompleteService
{
    private UniqueExtractorService $extractor;
    
    public function __construct(UniqueExtractorService $extractor)
    {
        $this->extractor = $extractor;
    }
    
    public function indexText(string $text): void
    {
        // Indexer les mots pour l'autocomplétion
        $this->extractor->extractUniqueWordsWithNormalization($text);
    }
    
    public function suggest(string $prefix, int $limit = 10): array
    {
        return $this->extractor->searchWordsByPrefix($prefix, $limit);
    }
}

$autocomplete = new AutocompleteService($extractor);
$autocomplete->indexText("php python laravel javascript golang");
$suggestions = $autocomplete->suggest('p', 3);
// ['php', 'python']
```

### Cas 3 : Détection de langue

**Problème :** Identifier la langue d'un texte en analysant la distribution des lettres.

```php
<?php

class LanguageDetector
{
    private UniqueExtractorService $extractor;
    private array $languageProfiles = [];
    
    public function __construct(UniqueExtractorService $extractor)
    {
        $this->extractor = $extractor;
    }
    
    public function train(string $language, string $text): void
    {
        $letters = $this->extractor->extractUniqueLettersWithNormalization($text);
        $frequencies = [];
        foreach ($letters as $letter) {
            $frequencies[$letter] = $this->extractor->getLetterFrequency($letter);
        }
        $this->languageProfiles[$language] = $frequencies;
    }
    
    public function detect(string $text): string
    {
        $letters = $this->extractor->extractUniqueLettersWithNormalization($text);
        $bestScore = 0;
        $bestLanguage = 'unknown';
        
        foreach ($this->languageProfiles as $language => $profile) {
            $score = 0;
            foreach ($letters as $letter) {
                if (isset($profile[$letter])) {
                    $score += $profile[$letter];
                }
            }
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestLanguage = $language;
            }
        }
        
        return $bestLanguage;
    }
}

$detector = new LanguageDetector($extractor);
$detector->train('en', 'Hello world this is English text');
$detector->train('fr', 'Bonjour le monde ceci est du français');
$language = $detector->detect('Hello how are you?'); // 'en'
```

## Gestion des erreurs

| Situation | Exception | Message |
|-----------|-----------|---------|
| Aucune | - | - |

**Note :** Ce service ne lève pas d'exceptions. Les textes vides retournent des tableaux vides.

## Performance

### Complexité

| Opération | Complexité | Détails |
|-----------|------------|---------|
| `extractUniqueLetters()` | O(n) | Parcourt le texte une fois |
| `extractUniqueWords()` | O(n) | Parcourt le texte et filtre |
| `searchWordsByPrefix()` | O(k) | k = nombre de nœuds parcourus |
| `getMostFrequentWords()` | O(n log n) | Tri des fréquences |

### Optimisations

- **Caches** : Les résultats des méthodes principales sont mis en cache par clé MD5
- **Trie** : Structure arborescente pour la recherche par préfixe en O(k)
- **Fréquences** : Tableaux associatifs pour un accès O(1)
- **Pas de dépendances** : Utilisation uniquement de fonctions PHP natives

### Recommandations

- Utiliser les caches pour les textes répétés
- Appeler `clear()` périodiquement pour libérer la mémoire
- Adapter `minWordLength` selon le contexte (2 pour le français, 3 pour l'anglais)

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
use AndyDefer\PhpServices\Services\UniqueExtractorService;

// Initialisation
$config = new TextNormalizerConfig();
$extractor = new UniqueExtractorService($config);

// Texte source
$text = "Hello world! PHP is a great programming language. 
         PHP is widely used for web development.";

// 1. Extraction des lettres uniques
$uniqueLetters = $extractor->extractUniqueLettersWithNormalization($text);
echo "Lettres uniques: " . implode(', ', $uniqueLetters) . "\n";
// Sortie: h, e, l, o, w, r, d, p, h, i, s, a, g, t, p, r, o, g, m, n, u, a, e, f, w, b, d, e, v, p, m, n, t

// 2. Extraction des mots uniques
$uniqueWords = $extractor->extractUniqueWordsWithNormalization(
    $text,
    removeStopWords: true,
    removeShortWords: true,
    minWordLength: 3
);
echo "Mots uniques: " . implode(', ', $uniqueWords) . "\n";
// Sortie: hello, world, php, great, programming, language, widely, used, web, development

// 3. Vérification d'existence
echo "La lettre 'p' existe: " . ($extractor->letterExists('p') ? 'oui' : 'non') . "\n";
echo "La lettre 'z' existe: " . ($extractor->letterExists('z') ? 'oui' : 'non') . "\n";

// 4. Estimation des uniques
echo "Lettres uniques estimées: " . $extractor->estimateUniqueLetters() . "\n";
echo "Mots uniques estimés: " . $extractor->estimateUniqueWords() . "\n";

// 5. Recherche par préfixe
$suggestions = $extractor->searchWordsByPrefix('p', 5);
echo "Mots commençant par 'p': " . implode(', ', $suggestions) . "\n";
// Sortie: php, programming

// 6. Fréquence des lettres
echo "Fréquence de 'l': " . $extractor->getLetterFrequency('l') . "\n";
echo "Fréquence de 'p': " . $extractor->getLetterFrequency('p') . "\n";

// 7. Mots les plus fréquents
$topWords = $extractor->getMostFrequentWords(5);
echo "Top 5 des mots: " . implode(', ', $topWords) . "\n";

// 8. Statistiques
$stats = $extractor->getStats();
echo "Statistiques:\n";
foreach ($stats as $key => $value) {
    echo "  $key: $value\n";
}

// 9. Nettoyage
$extractor->clear();
echo "Données nettoyées\n";
```

## Voir aussi

- `TextNormalizerService` - Normalisation des textes
- `TextNormalizerConfig` - Configuration du normaliseur
- `NormalizationMode` - Modes de normalisation
- `NGramGeneratorService` - Génération de n-grammes
- `WordVectorGeneratorService` - Génération de vecteurs de mots