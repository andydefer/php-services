# ModelTransformableService - Référence Technique

## Description

Convertit automatiquement les modèles Eloquent de Laravel en Data DTO typés (AbstractData), en gérant les casts, les relations et les types imbriqués.

## Hiérarchie

```
ModelTransformableInterface
    └── ModelTransformableService
```

## Rôle principal

Assure la transformation type-safe entre les modèles Eloquent (qui n'implémentent pas `Transformable`) et les Data DTOs de l'architecture. Il extrait les attributs (avec leurs casts : JSON, array, enum, datetime) et les relations (en les transformant récursivement via `NormalizerChain`).

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

### `toDataCollection(Collection $models, string $collectionClass): AbstractTypedCollection`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$models` | `Collection<int, Model>` | Collection de modèles Eloquent |
| `$collectionClass` | `class-string<AbstractTypedCollection>` | Classe de collection cible (doit étendre `AbstractTypedCollection`) |

**Retourne :** `AbstractTypedCollection` - Collection typée d'instances Data DTO

**Exemple :**
```php
$users = User::all();
$usersData = $service->toDataCollection($users, UserDataCollection::class);

foreach ($usersData as $userData) {
    echo $userData->name;
}
```

---

## Cas d'utilisation

### Cas 1 : Conversion simple d'un modèle

```php
$service = new ModelTransformableService();
$user = User::find(1);
$userData = $service->toData($user, UserData::class);
```

### Cas 2 : Conversion avec relations Eloquent

```php
$user = User::with('posts', 'profile')->find(1);
$userData = $service->toData($user, UserData::class);

// Les relations chargées sont automatiquement converties
// $userData->posts est une DataCollection de PostData
// $userData->profile est une instance de ProfileData
```

### Cas 3 : Conversion de collection avec collection typée

```php
$activeUsers = User::where('status', 'active')->get();
$usersData = $service->toDataCollection($activeUsers, UserDataCollection::class);

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
| `toData()` | O(n) | n = nombre d'attributs + relations |
| `toDataCollection()` | O(n × m) | n = nombre de modèles, m = attributs par modèle |

**Recommandations :**
- Utiliser `with()` pour charger les relations nécessaires (évite N+1)
- Pour les collections > 1000 éléments, traiter par lots
- Définir des collections typées concrètes (ex: `UserDataCollection`)

---

## Compatibilité

| Version | Support |
|---------|---------|
| PHP 8.2+ | ✅ Complet |
| Laravel 10.x | ✅ Complet |
| Laravel 11.x | ✅ Complet |
| Laravel 12.x | ✅ Complet |
| Laravel 13.x | ⚠️ Non testé |

---

## Exemple complet

```php
// 1. Définir le Data DTO
final class UserData extends AbstractData
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ProfileData $profile,
        public readonly PostDataCollection $posts,
    ) {}
}

// 2. Définir la collection typée
final class UserDataCollection extends DataCollection
{
    public function __construct()
    {
        parent::__construct(UserData::class);
    }
}

// 3. Utiliser le service
$service = new ModelTransformableService();

$user = User::with('profile', 'posts')->find(1);
$userData = $service->toData($user, UserData::class);

// 4. Collection typée
$users = User::where('active', true)->get();
$usersData = $service->toDataCollection($users, UserDataCollection::class);
```

---

## Voir aussi

- `AbstractData` - Classe de base pour les Data DTOs
- `DataCollection` - Collection typée pour Data DTOs
- `RecordTransformableService` - Transformation modèle → Record
- `PhpServiceServiceProvider` - Service Provider
---