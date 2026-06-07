```markdown
# ModelTransformableService - Référence Technique

## Description

Convertit automatiquement les modèles Eloquent de Laravel en Data DTO typés (AbstractData), en gérant les casts, les relations et les types imbriqués.

## Hiérarchie

```
ModelTransformableInterface
    └── ModelTransformableService
```

## Rôle principal

Assure la transformation type-safe entre les modèles Eloquent (qui n'implémentent pas `Transformable`) et les Data DTOs de l'architecture. Il extrait les attributs (avec leurs casts : JSON, array, enum, datetime) et les relations (en les transformant récursivement).

## Détails

[Voir la classe ModelTransformableService](https://github.com/andydefer/php-services/blob/main/src/Services/ModelTransformableService.php)

Le package s'enregistre automatiquement via Laravel auto-discovery.

## API / Méthodes publiques

### `toData(Model $model, string $dataClass): AbstractData`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$model` | `Model` | Instance du modèle Eloquent à convertir |
| `$dataClass` | `class-string<AbstractData>` | Classe Data DTO cible (doit étendre `AbstractData`) |

**Retourne :** `AbstractData` - Instance du Data DTO avec les données du modèle

**Exceptions :** 
- `RuntimeException` - Si la classe Data cible n'existe pas
- `InvalidArgumentException` - Si les données ne peuvent pas être hydratées

**Exemple :**
```php
$service = new ModelTransformableService();
$user = User::find(1);
$userData = $service->toData($user, UserData::class);
```

---

### `toDataCollection(Collection $models, string $dataClass): DataCollection`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$models` | `Collection<int, Model>` | Collection de modèles Eloquent |
| `$dataClass` | `class-string<AbstractData>` | Classe Data DTO cible |

**Retourne :** `DataCollection` - Collection typée d'instances Data DTO

**Exemple :**
```php
$users = User::all();
$usersData = $service->toDataCollection($users, UserData::class);

// Parcourir la collection
foreach ($usersData as $userData) {
    echo $userData->name;
}
```

---

## Cas d'utilisation

### Cas 1 : Conversion simple d'un modèle

```php
<?php

declare(strict_types=1);

use AndyDefer\PhpServices\Services\ModelTransformableService;
use App\Models\User;
use App\Data\UserData;

$service = new ModelTransformableService();
$user = User::find(1);

$userData = $service->toData($user, UserData::class);

// Résultat : UserData avec id, name, email convertis
```

### Cas 2 : Conversion avec relations Eloquent

```php
<?php

$user = User::with('posts', 'profile')->find(1);
$userData = $service->toData($user, UserData::class);

// Les relations sont automatiquement converties en Data DTOs
// $userData->posts est une DataCollection de PostData
// $userData->profile est une instance de ProfileData
```

### Cas 3 : Conversion de collection avec filtrage

```php
<?php

$activeUsers = User::where('status', 'active')->get();
$usersData = $service->toDataCollection($activeUsers, UserData::class);

$adminUsers = $usersData->filter(fn($data) => $data->role === 'admin');
```

---

## Gestion des erreurs

| Situation | Exception | Message |
|-----------|-----------|---------|
| Classe Data non trouvée | `RuntimeException` | `Data class not found for model {modelClass}. Tried: {dataClass} and {fallbackClass}` |
| Hydratation échoue | `InvalidArgumentException` | (selon la classe Data cible) |

---

## Intégration

### Avec Laravel (Service Provider)

```php
// config/app.php
'providers' => [
    // ...
    AndyDefer\PhpServices\PhpServiceServiceProvider::class,
];
```

### Injection dans une Action

```php
use AndyDefer\PhpServices\Contracts\ModelTransformableInterface;

final class ShowUserAction extends AbstractAction
{
    public function __construct(
        private readonly ModelTransformableInterface $transformer,
    ) {}
    
    protected function handle(AbstractRecord $request): ResponseFactory
    {
        $user = User::find($request->id);
        $userData = $this->transformer->toData($user, UserData::class);
        
        return ResponseFactory::json($userData);
    }
}
```

---

## Performance

| Opération | Complexité | Notes |
|-----------|------------|-------|
| `toData()` | O(n) avec n = nombre d'attributs + relations | Chaque attribut est traité une fois |
| `toDataCollection()` | O(n × m) avec n = nombre de modèles, m = attributs par modèle | Peut être lourd pour de grandes collections |
| Transformation JSON | O(k) avec k = taille du JSON | `json_decode` est linéaire |

**Recommandations :**
- Utiliser `with()` pour charger les relations nécessaires (évite N+1)
- Pour les collections > 1000 éléments, traiter par lots
- Le cache des Data DTOs n'est pas géré par ce service

---

## Compatibilité

| Version | Support | Notes |
|---------|---------|-------|
| PHP 8.2+ | ✅ Complet | Requis par le package |
| PHP 8.1 | ❌ Non | Non testé |
| Laravel 10.x | ✅ Complet | Testé |
| Laravel 11.x | ✅ Complet | Testé |
| Laravel 12.x | ✅ Complet | Testé |
| Laravel 13.x | ⚠️ Non testé | Devrait fonctionner |

---

## Exemple complet

```php
<?php

declare(strict_types=1);

use AndyDefer\PhpServices\Services\ModelTransformableService;
use App\Models\User;
use App\Data\UserData;
use App\Data\PostData;
use App\Data\ProfileData;

// 1. Définir le Data DTO
final class UserData extends AbstractData
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ProfileData $profile,
        public readonly DataCollection $posts,
    ) {}
}

// 2. Utiliser le service
$service = new ModelTransformableService();

$user = User::with('profile', 'posts')->find(1);
$userData = $service->toData($user, UserData::class);

// 3. Résultat
echo $userData->name;           // 'John Doe'
echo $userData->profile->bio;   // 'Developer'
foreach ($userData->posts as $post) {
    echo $post->title;
}

// 4. Collection
$users = User::where('active', true)->get();
$usersData = $service->toDataCollection($users, UserData::class);
```

---

## Voir aussi

- `AbstractData` - Classe de base pour les Data DTOs
- `StrictDataObject` - Objet pour les données JSON/array
- `DataCollection` - Collection typée pour Data DTOs
- `PhpServiceServiceProvider` - Service Provider pour l'intégration Laravel
```