# RecordTransformableService - Référence Technique

## Description

Convertit automatiquement les modèles Eloquent de Laravel en Records typés (AbstractRecord), en gérant les casts et les relations chargées.

## Hiérarchie

```
RecordTransformableInterface
    └── RecordTransformableService
```

## Rôle principal

Assure la transformation type-safe entre les modèles Eloquent (qui n'implémentent pas `Transformable`) et les Records de l'architecture. Il extrait les attributs (avec leurs casts : JSON, array, enum, datetime) et peut inclure les relations chargées.

## Détails

[Voir la classe RecordTransformableService](https://github.com/andydefer/php-services/blob/main/src/Services/RecordTransformableService.php)

Le package s'enregistre automatiquement via Laravel auto-discovery.

## API / Méthodes publiques

### `toRecord(Model $model, string $recordClass): AbstractRecord`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$model` | `Model` | Instance du modèle Eloquent à convertir |
| `$recordClass` | `class-string<AbstractRecord>` | Classe Record cible (doit étendre `AbstractRecord`) |

**Retourne :** `AbstractRecord` - Instance du Record avec les données du modèle

**Exceptions :** 
- `RuntimeException` - Si la classe Record cible n'existe pas
- `InvalidArgumentException` - Si les données ne peuvent pas être hydratées

**Exemple :**
```php
$service = new RecordTransformableService();
$user = User::find(1);
$userRecord = $service->toRecord($user, UserRecord::class);
```

---

### `toRecordCollection(Collection $models, string $collectionClass): AbstractTypedCollection`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$models` | `Collection<int, Model>` | Collection de modèles Eloquent |
| `$collectionClass` | `class-string<AbstractTypedCollection>` | Classe de collection cible (doit étendre `AbstractTypedCollection`) |

**Retourne :** `AbstractTypedCollection` - Collection typée d'instances Record

**Exemple :**
```php
$users = User::all();
$usersRecord = $service->toRecordCollection($users, UserRecordCollection::class);

foreach ($usersRecord as $userRecord) {
    echo $userRecord->name;
}
```

---

## Cas d'utilisation

### Cas 1 : Conversion simple d'un modèle

```php
$service = new RecordTransformableService();
$user = User::find(1);
$userRecord = $service->toRecord($user, UserRecord::class);

// Résultat : UserRecord avec id, name, email convertis
```

### Cas 2 : Conversion avec relations Eloquent

```php
$user = User::with('posts', 'profile')->find(1);
$userRecord = $service->toRecord($user, UserRecord::class);

// Les relations chargées sont automatiquement incluses
// $userRecord->posts est une RecordCollection de PostRecord
// $userRecord->profile est une instance de ProfileRecord
```

### Cas 3 : Conversion de collection avec collection typée

```php
$activeUsers = User::where('status', 'active')->get();
$usersRecord = $service->toRecordCollection($activeUsers, UserRecordCollection::class);

$adminUsers = $usersRecord->filter(fn($record) => $record->role === 'admin');
```

---

## Gestion des erreurs

| Situation | Exception | Message |
|-----------|-----------|---------|
| Classe Record non trouvée | `RuntimeException` | `Record class not found for model {modelClass}. Tried: {recordClass} and {fallbackClass}` |
| Hydratation échoue | `InvalidArgumentException` | (selon la classe Record cible) |

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
use AndyDefer\PhpServices\Contracts\RecordTransformableInterface;

final class ShowUserAction extends AbstractAction
{
    public function __construct(
        private readonly RecordTransformableInterface $transformer,
    ) {}
    
    protected function handle(AbstractRecord $request): ResponseFactory
    {
        $user = User::find($request->id);
        $userRecord = $this->transformer->toRecord($user, UserRecord::class);
        
        // Utilisation du Record...
    }
}
```

---

## Performance

| Opération | Complexité | Notes |
|-----------|------------|-------|
| `toRecord()` | O(n) | n = nombre d'attributs + relations |
| `toRecordCollection()` | O(n × m) | n = nombre de modèles, m = attributs par modèle |

**Recommandations :**
- Utiliser `with()` pour charger les relations nécessaires (évite N+1)
- Pour les collections > 1000 éléments, traiter par lots
- Définir des collections typées concrètes (ex: `UserRecordCollection`)

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
// 1. Définir le Record
final class UserRecord extends AbstractRecord
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?PostRecordCollection $posts = null,
    ) {}
}

// 2. Définir la collection typée
final class UserRecordCollection extends RecordCollection
{
    public function __construct()
    {
        parent::__construct(UserRecord::class);
    }
}

// 3. Utiliser le service
$service = new RecordTransformableService();

$user = User::with('posts')->find(1);
$userRecord = $service->toRecord($user, UserRecord::class);

// 4. Collection typée
$users = User::where('active', true)->get();
$usersRecord = $service->toRecordCollection($users, UserRecordCollection::class);
```

---

## Voir aussi

- `AbstractRecord` - Classe de base pour les Records
- `RecordCollection` - Collection typée pour Records
- `ModelTransformableService` - Transformation modèle → Data DTO
- `PhpServiceServiceProvider` - Service Provider
---