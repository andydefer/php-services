# PrimitiveTypeConverterService - Référence Technique

## Description

Convertit les valeurs PHP entre les types primitifs (bool, string, int, float, null) de manière explicite et sécurisée.

## Hiérarchie / Implémentations

```
PrimitiveTypeConverterService (classe autonome)
```

## Rôle principal

Fournit une abstraction pour les conversions de types primitifs, évitant les cast implicites dangereux et standardisant la détection et conversion entre les types scalaires PHP.

## Détails

[Voir la classe PrimitiveTypeConverterService](https://github.com/andydefer/php-services/blob/main/src/Services/PrimitiveTypeConverterService.php)

## API / Méthodes publiques

### `convert(mixed $value, PrimitiveType $targetType): mixed`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$value` | `mixed` | Valeur à convertir |
| `$targetType` | `PrimitiveType` | Type cible (BOOL, STRING, INT, FLOAT, NULL) |

**Retourne :** `mixed` - Valeur convertie dans le type cible

**Exemple :**
```php
$converter = new PrimitiveTypeConverterService();
$result = $converter->convert('123', PrimitiveType::INT);
// $result = 123 (int)
```

### `convertOrDefault(mixed $value, PrimitiveType $targetType, mixed $default = null): mixed`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$value` | `mixed` | Valeur à convertir |
| `$targetType` | `PrimitiveType` | Type cible |
| `$default` | `mixed` | Valeur par défaut en cas d'échec (défaut: null) |

**Retourne :** `mixed` - Valeur convertie ou valeur par défaut

**Exemple :**
```php
$result = $converter->convertOrDefault('abc', PrimitiveType::INT, 0);
// $result = 0 (int)
```

### `detectType(mixed $value): PrimitiveType`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$value` | `mixed` | Valeur à analyser |

**Retourne :** `PrimitiveType` - Type primitif détecté

**Exceptions :** `InvalidArgumentException` - Si la valeur n'est pas un type primitif PHP (array, object, resource)

**Exemple :**
```php
$type = $converter->detectType('hello');
// $type = PrimitiveType::STRING
```

## Cas d'utilisation

### Cas 1 : Normalisation de données utilisateur

```php
<?php

declare(strict_types=1);

use AndyDefer\PhpServices\Services\PrimitiveTypeConverterService;
use AndyDefer\PhpServices\Enums\PrimitiveType;

$converter = new PrimitiveTypeConverterService();

// Données utilisateur non fiables
$userInputs = [
    'age' => '25',
    'is_active' => 'true',
    'score' => '3.14'
];

// Normalisation
$age = $converter->convert($userInputs['age'], PrimitiveType::INT);
$isActive = $converter->convert($userInputs['is_active'], PrimitiveType::BOOL);
$score = $converter->convert($userInputs['score'], PrimitiveType::FLOAT);
```

### Cas 2 : Valeurs optionnelles avec fallback

```php
<?php

declare(strict_types=1);

use AndyDefer\PhpServices\Services\PrimitiveTypeConverterService;
use AndyDefer\PhpServices\Enums\PrimitiveType;

$converter = new PrimitiveTypeConverterService();

// Valeur optionnelle avec défaut sécurisé
$limit = $converter->convertOrDefault($_GET['limit'] ?? null, PrimitiveType::INT, 50);

// Conversion booléenne avec fallback
$debug = $converter->convertOrDefault($_ENV['DEBUG'] ?? null, PrimitiveType::BOOL, false);
```

### Cas 3 : Validation et routage par type

```php
<?php

declare(strict_types=1);

use AndyDefer\PhpServices\Services\PrimitiveTypeConverterService;
use AndyDefer\PhpServices\Enums\PrimitiveType;

$converter = new PrimitiveTypeConverterService();

function processValue(mixed $value): string
{
    $type = $converter->detectType($value);
    
    return match($type) {
        PrimitiveType::NULL => 'Valeur nulle',
        PrimitiveType::BOOL => $value ? 'Vrai' : 'Faux',
        PrimitiveType::INT => "Entier: $value",
        PrimitiveType::FLOAT => "Flottant: $value",
        PrimitiveType::STRING => "Chaîne: $value",
    };
}
```

## Gestion des erreurs

| Situation | Exception | Message |
|-----------|-----------|---------|
| Détection de type array | `InvalidArgumentException` | `Unable to detect type for value of type: array {...}` |
| Détection de type object | `InvalidArgumentException` | `Unable to detect type for value of type: object {...}` |
| Détection de type resource | `InvalidArgumentException` | `Unable to detect type for value of type: resource {...}` |

**Note :** La méthode `convert()` n'émet pas d'exception. Le cast forcé PHP est utilisé (ex: (int) 'abc' → 0).

## Intégration

Service autonome sans dépendances externes. Peut être utilisé comme :

```php
// Injection simple
class UserService {
    public function __construct(
        private PrimitiveTypeConverterService $typeConverter
    ) {}
}
```

## Performance

- **Complexité :** O(1) - match statements optimisés
- **Pas de cache** : Chaque appel est indépendant
- **Cast natifs PHP** : Utilisation des opérateurs `(bool)`, `(int)`, etc.

## Compatibilité

| Version PHP | Support |
|-------------|---------|
| PHP 8.1+ | ✅ Complet |
| PHP 8.0 | ✅ Complet |

## Exemple complet

```php
<?php

declare(strict_types=1);

use AndyDefer\PhpServices\Services\PrimitiveTypeConverterService;
use AndyDefer\PhpServices\Enums\PrimitiveType;

$converter = new PrimitiveTypeConverterService();

// 1. Convertir une chaîne en entier
$intValue = $converter->convert('42', PrimitiveType::INT);        // 42

// 2. Convertir en booléen
$boolValue = $converter->convert('true', PrimitiveType::BOOL);    // true

// 3. Détecter le type
$type = $converter->detectType('hello');                          // PrimitiveType::STRING

// 4. Conversion avec fallback
$safeInt = $converter->convertOrDefault('abc', PrimitiveType::INT, 0); // 0

// 5. Valeur null
$nullValue = $converter->convert(null, PrimitiveType::NULL);      // null
```

## Voir aussi

- `PrimitiveType` - Énumération des types primitifs supportés
- `ScalarConverter` - Alternative pour les conversions plus complexes avec validation
```
---