# TextNormalizerService - Référence Technique

## Description

Service de normalisation et d'extraction de mots pour le traitement de texte. Il nettoie, normalise et prépare les textes pour l'indexation et la recherche.

## Hiérarchie / Implémentations

```
TextNormalizerInterface
    └── TextNormalizerService (final)
```

## Rôle principal

Assure le nettoyage et la normalisation des textes en vue d'une utilisation dans des moteurs de recherche ou des systèmes d'indexation. Le service est **framework-agnostic** et ne dépend d'aucune bibliothèque externe.

## Détails

[Voir la classe TextNormalizerService](https://github.com/andydefer/php-services/blob/main/src/Services/TextNormalizerService.php)

## API / Méthodes publiques

### `normalize(string $text): string`

Normalise un texte en supprimant les caractères indésirables, les accents, les émojis et en standardisant le format.

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$text` | `string` | Texte à normaliser |

**Retourne :** `string` - Texte normalisé en minuscules

**Exemple :**
```php
$text = "L'élève a acheté 100€ !";
$normalized = $normalizer->normalize($text);
// Résultat : "eleve a achete 100 euro"
```

---

### `extractWords(string $text): array`

Extrait et normalise tous les mots d'un texte en les retournant sous forme de tableau.

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$text` | `string` | Texte à analyser |

**Retourne :** `array<string>` - Liste des mots extraits et normalisés

**Exemple :**
```php
$text = "Hello World! This is a test.";
$words = $normalizer->extractWords($text);
// Résultat : ['hello', 'world', 'this', 'is', 'a', 'test']
```

---

### `removeElidedArticles(string $text): string`

Supprime les articles élidés (ex: "l'", "d'" en français).

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$text` | `string` | Texte à traiter |

**Retourne :** `string` - Texte sans articles élidés

**Exemple :**
```php
$text = "L'élève a acheté l'ordinateur";
$result = $normalizer->removeElidedArticles($text);
// Résultat : "élève a acheté ordinateur"
```

---

### `removeEmojis(string $text): string`

Supprime tous les émojis du texte.

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$text` | `string` | Texte à traiter |

**Retourne :** `string` - Texte sans émojis

**Exemple :**
```php
$text = "Hello 😊 World! 🚀";
$result = $normalizer->removeEmojis($text);
// Résultat : "Hello  World! "
```

---

### `removeDiacritics(string $text): string`

Supprime les accents et diacritiques (é → e, à → a, etc.).

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$text` | `string` | Texte à traiter |

**Retourne :** `string` - Texte sans diacritiques

**Exemple :**
```php
$text = "Éléphant à Paris";
$result = $normalizer->removeDiacritics($text);
// Résultat : "Elephant a Paris"
```

---

### `removeCurrencySymbols(string $text): string`

Remplace les symboles monétaires par leur nom textuel (€ → euro, $ → dollar).

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$text` | `string` | Texte à traiter |

**Retourne :** `string` - Texte avec symboles monétaires remplacés

**Exemple :**
```php
$text = "Prix: 100€ et 50$";
$result = $normalizer->removeCurrencySymbols($text);
// Résultat : "Prix: 100 euro et 50 dollar"
```

---

### `removeSpecialChars(string $text): string`

Remplace les caractères spéciaux par des espaces. Conserve uniquement les lettres, chiffres, espaces, apostrophes et tirets.

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$text` | `string` | Texte à traiter |

**Retourne :** `string` - Texte nettoyé

**Exemple :**
```php
$text = "Hello@World#Test!";
$result = $normalizer->removeSpecialChars($text);
// Résultat : "Hello World Test "
```

---

### `normalizeSpaces(string $text): string`

Normalise les espaces : supprime les espaces multiples et les espaces en début/fin de chaîne.

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$text` | `string` | Texte à traiter |

**Retourne :** `string` - Texte avec espaces normalisés

**Exemple :**
```php
$text = "Hello    World   !";
$result = $normalizer->normalizeSpaces($text);
// Résultat : "Hello World !"
```

---

### `removeShortWords(array $words, int $minLength = 2): array`

Filtre les mots courts d'un tableau de mots.

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$words` | `array<string>` | Liste de mots |
| `$minLength` | `int` | Longueur minimale (défaut: 2) |

**Retourne :** `array<string>` - Mots filtrés

**Exemple :**
```php
$words = ['hello', 'a', 'world', 'is', 'test'];
$result = $normalizer->removeShortWords($words);
// Résultat : ['hello', 'world', 'is', 'test']
```

---

### `removeStopWords(array $words): array`

Supprime les stop-words (mots vides) d'un tableau de mots.

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$words` | `array<string>` | Liste de mots |

**Retourne :** `array<string>` - Mots filtrés

**Exemple :**
```php
$words = ['the', 'hello', 'and', 'world', 'of', 'test'];
$result = $normalizer->removeStopWords($words);
// Résultat : ['hello', 'and', 'world', 'test']
```

---

### `processText(string $text, bool $removeStopWords = true, bool $removeShortWords = true, int $minWordLength = 2): array`

Traite complètement un texte : extraction, normalisation et filtrage en une seule opération.

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$text` | `string` | Texte à traiter |
| `$removeStopWords` | `bool` | Supprimer les stop-words |
| `$removeShortWords` | `bool` | Supprimer les mots courts |
| `$minWordLength` | `int` | Longueur minimale des mots |

**Retourne :** `array<string>` - Mots traités

**Exemple :**
```php
$text = "The quick brown fox jumps over the lazy dog";
$result = $normalizer->processText($text, true, true);
// Résultat : ['quick', 'brown', 'fox', 'jumps', 'over', 'lazy', 'dog']
```

---

### `clearCache(): void`

Vide les caches internes du service.

| Paramètre | Type | Description |
|-----------|------|-------------|
| Aucun | - | - |

**Retourne :** `void`

**Exemple :**
```php
$normalizer->clearCache();
```

## Cas d'utilisation

### Cas 1 : Préparation de texte pour indexation

**Problème :** Préparer un contenu textuel pour l'indexation dans un moteur de recherche.

```php
<?php

declare(strict_types=1);

use AndyDefer\PhpServices\Configs\TextNormalizerConfig;
use AndyDefer\PhpServices\Services\TextNormalizerService;

$config = new TextNormalizerConfig();
$normalizer = new TextNormalizerService($config);

$article = "L'élève a acheté un ordinateur à 1000€ !";
$words = $normalizer->processText($article, true, true);
// Résultat : ['eleve', 'achete', 'ordinateur', '1000', 'euro']
```

### Cas 2 : Nettoyage de contenu utilisateur

**Problème :** Nettoyer et normaliser les entrées utilisateur pour la recherche.

```php
<?php

declare(strict_types=1);

class UserSearch
{
    private TextNormalizerService $normalizer;
    
    public function __construct(TextNormalizerService $normalizer)
    {
        $this->normalizer = $normalizer;
    }
    
    public function search(string $query): array
    {
        // Nettoyer la requête utilisateur
        $cleaned = $this->normalizer->normalize($query);
        $words = $this->normalizer->extractWords($cleaned);
        $filtered = $this->normalizer->removeStopWords($words);
        
        return $filtered;
    }
}

$search = new UserSearch($normalizer);
$results = $search->search("L'utilisateur a acheté un produit");
// Résultat : ['utilisateur', 'achete', 'produit']
```

### Cas 3 : Extraction de mots-clés

**Problème :** Extraire les mots-clés pertinents d'un texte long.

```php
<?php

declare(strict_types=1);

class KeywordExtractor
{
    private TextNormalizerService $normalizer;
    
    public function __construct(TextNormalizerService $normalizer)
    {
        $this->normalizer = $normalizer;
    }
    
    public function extract(string $text, int $minLength = 4): array
    {
        $words = $this->normalizer->extractWords($text);
        $filtered = $this->normalizer->removeStopWords($words);
        $filtered = $this->normalizer->removeShortWords($filtered, $minLength);
        
        // Compter les fréquences
        $frequencies = array_count_values($filtered);
        arsort($frequencies);
        
        return array_keys($frequencies);
    }
}

$extractor = new KeywordExtractor($normalizer);
$keywords = $extractor->extract("Le développement PHP moderne utilise Laravel");
// Résultat : ['developpement', 'moderne', 'laravel']
```

## Gestion des erreurs

| Situation | Exception | Message |
|-----------|-----------|---------|
| Aucune erreur levée par ce service | - | - |

**Note :** Ce service n'utilise pas d'exceptions. Les opérations échouent silencieusement (retournent une chaîne vide ou un tableau vide).

## Intégration

### Avec TextNormalizerConfig

Le service dépend de `TextNormalizerConfigInterface` pour obtenir les règles de normalisation.

```php
<?php

use AndyDefer\PhpServices\Configs\TextNormalizerConfig;
use AndyDefer\PhpServices\Services\TextNormalizerService;

$config = new TextNormalizerConfig();
$normalizer = new TextNormalizerService($config);
```

### Avec des configurations personnalisées

```php
<?php

use AndyDefer\PhpServices\Contracts\TextNormalizerConfigInterface;

$customConfig = new class implements TextNormalizerConfigInterface {
    public function getElidedArticles(): array { return ["l'"]; }
    public function getDiacritics(): array { return ['é' => 'e']; }
    public function getCurrencySymbols(): array { return ['€' => 'euro']; }
    public function getStopWords(): array { return ['le', 'la']; }
    public function isStopWord(string $word): bool { return in_array($word, $this->getStopWords()); }
    public function getMinWordLength(): int { return 3; }
};

$normalizer = new TextNormalizerService($customConfig);
```

## Performance

### Complexité

| Opération | Complexité | Détails |
|-----------|------------|---------|
| `normalize()` | O(n) | Parcourt le texte une fois |
| `extractWords()` | O(n) | Normalisation + extraction |
| `removeShortWords()` | O(n) | Parcourt le tableau de mots |
| `removeStopWords()` | O(n) | Parcourt le tableau de mots |
| `processText()` | O(n) | Combine plusieurs opérations |

### Optimisations

- **Cache intégré** : Les résultats de `normalize()` et `extractWords()` sont mis en cache par MD5
- **Fonctions natives** : Utilisation de `preg_replace`, `strtr`, `mb_strlen` optimisées en C
- **Aucune allocation inutile** : Les tableaux sont créés uniquement quand nécessaire

### Recommandations

- Utiliser `processText()` pour une seule passe de traitement
- Profiter du cache pour les textes répétés
- Adapter la longueur minimale selon le contexte d'utilisation

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
use AndyDefer\PhpServices\Services\TextNormalizerService;

// Initialisation
$config = new TextNormalizerConfig();
$normalizer = new TextNormalizerService($config);

// Texte source
$text = "L'élève a acheté un ordinateur à 1000€ ! 😊";

// 1. Normalisation simple
$normalized = $normalizer->normalize($text);
echo "Normalisé: $normalized\n";
// Sortie: "eleve a achete un ordinateur a 1000 euro"

// 2. Extraction des mots
$words = $normalizer->extractWords($text);
echo "Mots: " . implode(', ', $words) . "\n";
// Sortie: "eleve, a, achete, un, ordinateur, a, 1000, euro"

// 3. Traitement complet
$processed = $normalizer->processText($text, true, true);
echo "Traités: " . implode(', ', $processed) . "\n";
// Sortie: "eleve, achete, ordinateur, 1000, euro"

// 4. Suppression des stop-words
$stopWordsRemoved = $normalizer->removeStopWords($words);
echo "Sans stop-words: " . implode(', ', $stopWordsRemoved) . "\n";
// Sortie: "eleve, achete, ordinateur, 1000, euro"

// 5. Suppression des mots courts
$shortWordsRemoved = $normalizer->removeShortWords($words, 4);
echo "Sans mots courts: " . implode(', ', $shortWordsRemoved) . "\n";
// Sortie: "ordinateur, 1000"

// 6. Validation du cache
$first = $normalizer->normalize($text);
$second = $normalizer->normalize($text);
echo "Cache utilisé: " . ($first === $second ? 'Oui' : 'Non') . "\n";

// 7. Nettoyage du cache
$normalizer->clearCache();
echo "Cache vidé\n";
```

## Voir aussi

- `TextNormalizerConfig` - Configuration du normaliseur
- `TextNormalizerConfigInterface` - Interface de configuration
- `TextNormalizerInterface` - Interface du service