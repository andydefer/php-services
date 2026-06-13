# FileSystemService - Référence Technique

## Description

Implémente l'interface `FileSystemInterface` en utilisant exclusivement les fonctions natives PHP pour manipuler le système de fichiers.

## Hiérarchie / Implémentations

```
FileSystemInterface
    └── FileSystemService
```

## Rôle principal

Fournit une abstraction portable et sans dépendance pour toutes les opérations courantes sur le système de fichiers : lecture, écriture, copie, déplacement, suppression et informations sur les fichiers et dossiers.

## Détails

[Voir la classe FileSystemService](https://github.com/andydefer/php-services/blob/main/src/Services/FileSystemService.php)

## API / Méthodes publiques

### `exists(string $path): bool`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$path` | `string` | Chemin à vérifier |

**Retourne :** `bool` - `true` si le fichier ou dossier existe, `false` sinon

**Exemple :**
```php
if ($filesystem->exists('/var/log/app.log')) {
    // Le fichier existe
}
```

### `get(string $path): string`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$path` | `string` | Chemin du fichier à lire |

**Retourne :** `string` - Contenu du fichier

**Exceptions :** `RuntimeException` - Si le fichier n'existe pas ou ne peut pas être lu

**Exemple :**
```php
$content = $filesystem->get('/var/log/app.log');
```

### `put(string $path, string $content): int|false`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$path` | `string` | Chemin de destination |
| `$content` | `string` | Contenu à écrire |

**Retourne :** `int|false` - Nombre d'octets écrits, ou `false` en cas d'échec

**Exemple :**
```php
$bytes = $filesystem->put('/tmp/config.json', '{"key": "value"}');
```

### `append(string $path, string $content): int|false`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$path` | `string` | Chemin du fichier |
| `$content` | `string` | Contenu à ajouter |

**Retourne :** `int|false` - Nombre d'octets écrits, ou `false` en cas d'échec

**Exemple :**
```php
$filesystem->append('/var/log/app.log', "Nouvelle ligne\n");
```

### `isDirectory(string $path): bool`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$path` | `string` | Chemin à vérifier |

**Retourne :** `bool` - `true` si le chemin est un dossier, `false` sinon

### `isFile(string $path): bool`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$path` | `string` | Chemin à vérifier |

**Retourne :** `bool` - `true` si le chemin est un fichier, `false` sinon

### `isReadable(string $path): bool`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$path` | `string` | Chemin à vérifier |

**Retourne :** `bool` - `true` si le chemin est accessible en lecture, `false` sinon

### `isWritable(string $path): bool`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$path` | `string` | Chemin à vérifier |

**Retourne :** `bool` - `true` si le chemin est accessible en écriture, `false` sinon

### `makeDirectory(string $path, PermissionMode $mode = PermissionMode::DIRECTORY, bool $recursive = true): bool`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$path` | `string` | Chemin du dossier à créer |
| `$mode` | `PermissionMode` | Permissions (0755 par défaut) |
| `$recursive` | `bool` | Créer les dossiers parents si nécessaire |

**Retourne :** `bool` - `true` en cas de succès, `false` en cas d'échec

**Exemple :**
```php
$filesystem->makeDirectory('/var/log/myapp', PermissionMode::DIRECTORY, true);
```

### `ensureDirectoryExists(string $path): void`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$path` | `string` | Chemin du dossier à vérifier/créer |

**Exceptions :** `RuntimeException` - Si le dossier ne peut pas être créé

**Exemple :**
```php
// Crée automatiquement le dossier s'il n'existe pas
$filesystem->ensureDirectoryExists('/var/log/myapp');
```

### `copy(string $source, string $destination): bool`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$source` | `string` | Chemin source |
| `$destination` | `string` | Chemin destination |

**Retourne :** `bool` - `true` en cas de succès, `false` en cas d'échec

**Exemple :**
```php
$filesystem->copy('/tmp/source.txt', '/backup/dest.txt');
```

### `move(string $source, string $destination): bool`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$source` | `string` | Chemin source |
| `$destination` | `string` | Chemin destination |

**Retourne :** `bool` - `true` en cas de succès, `false` en cas d'échec

### `glob(string $pattern, int $flags = 0): array`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$pattern` | `string` | Pattern de recherche (syntaxe glob) |
| `$flags` | `int` | Options supplémentaires |

**Retourne :** `array<int, string>` - Liste des chemins correspondants, tableau vide si aucun

**Exemple :**
```php
$logs = $filesystem->glob('/var/log/*.log');
foreach ($logs as $log) {
    echo $log;
}
```

### `delete(string $path): bool`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$path` | `string` | Chemin à supprimer (fichier ou dossier) |

**Retourne :** `bool` - `true` en cas de succès, `false` en cas d'échec

**Exemple :**
```php
$filesystem->delete('/tmp/old_file.txt');
```

### `deleteDirectory(string $directory): bool`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$directory` | `string` | Chemin du dossier à supprimer |

**Retourne :** `bool` - `true` en cas de succès, `false` en cas d'échec

**Note :** Supprime récursivement tout le contenu du dossier

**Exemple :**
```php
$filesystem->deleteDirectory('/tmp/old_cache');
```

### `size(string $path): int`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$path` | `string` | Chemin du fichier |

**Retourne :** `int` - Taille du fichier en octets

**Exceptions :** `RuntimeException` - Si le fichier n'existe pas ou la taille ne peut être obtenue

### `lastModified(string $path): int`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$path` | `string` | Chemin du fichier |

**Retourne :** `int` - Timestamp Unix de la dernière modification

**Exceptions :** `RuntimeException` - Si le fichier n'existe pas

### `extension(string $path): string`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$path` | `string` | Chemin du fichier |

**Retourne :** `string` - Extension du fichier (sans le point), chaîne vide si aucune

**Exemple :**
```php
$ext = $filesystem->extension('/path/to/file.jpg'); // 'jpg'
```

### `basename(string $path): string`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$path` | `string` | Chemin du fichier |

**Retourne :** `string` - Nom de base du chemin

### `dirname(string $path): string`

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$path` | `string` | Chemin du fichier |

**Retourne :** `string` - Nom du dossier parent

## Cas d'utilisation

### Cas 1 : Sauvegarde automatique avant modification

```php
<?php

declare(strict_types=1);

use AndyDefer\PhpServices\Services\FileSystemService;

$filesystem = new FileSystemService();
$configFile = '/etc/app/config.json';
$backupFile = $configFile . '.backup';

// Créer une sauvegarde
if ($filesystem->exists($configFile)) {
    $filesystem->copy($configFile, $backupFile);
    
    // Lire et modifier
    $content = $filesystem->get($configFile);
    $modified = str_replace('old_value', 'new_value', $content);
    $filesystem->put($configFile, $modified);
}
```

### Cas 2 : Nettoyage récursif des logs

```php
<?php

declare(strict_types=1);

use AndyDefer\PhpServices\Services\FileSystemService;

$filesystem = new FileSystemService();
$logDir = '/var/log/myapp';

// Supprimer les logs de plus de 7 jours
$logs = $filesystem->glob($logDir . '/*.log');
$now = time();
$sevenDays = 7 * 24 * 3600;

foreach ($logs as $log) {
    if ($now - $filesystem->lastModified($log) > $sevenDays) {
        $filesystem->delete($log);
    }
}
```

### Cas 3 : Migration de fichiers avec structure

```php
<?php

declare(strict_types=1);

use AndyDefer\PhpServices\Services\FileSystemService;

$filesystem = new FileSystemService();
$sourceBase = '/data/imports';
$targetBase = '/data/processed';

// Lister tous les fichiers CSV
$csvFiles = $filesystem->glob($sourceBase . '/*/*.csv');

foreach ($csvFiles as $csv) {
    // Construire le chemin de destination
    $relative = str_replace($sourceBase, '', $csv);
    $target = $targetBase . $relative;
    
    // Créer le dossier de destination
    $filesystem->ensureDirectoryExists(dirname($target));
    
    // Déplacer le fichier
    $filesystem->move($csv, $target);
}
```

## Gestion des erreurs

| Situation | Exception | Message |
|-----------|-----------|---------|
| Lecture d'un fichier inexistant | `RuntimeException` | `File does not exist at path: {path}` |
| Lecture d'un fichier illisible | `RuntimeException` | `Cannot read file at path: {path}` |
| Création de dossier impossible | `RuntimeException` | `Cannot create directory: {path}` |
| Taille de fichier inaccessible | `RuntimeException` | `Cannot get file size: {path}` |
| Last modified inaccessible | `RuntimeException` | `Cannot get last modified time: {path}` |

## Intégration

`FileSystemService` peut être utilisé comme :
- Service autonome dans n'importe quelle application PHP
- Alternative portable aux fichiersystem spécifiques à un framework
- Dépendance injectée via l'interface `FileSystemInterface`

```php
class LogManager {
    public function __construct(
        private FileSystemInterface $filesystem
    ) {}
}
```

## Performance

- **Complexité** : O(1) pour la plupart des opérations (délégation directe aux fonctions PHP)
- **Suppression récursive** : O(n) où n est le nombre de fichiers/dossiers dans l'arborescence
- **`glob()`** : Dépend du nombre de fichiers correspondant au pattern
- **Aucun cache interne** : Chaque appel interroge directement le système de fichiers

## Compatibilité

| Version PHP | Support |
|-------------|---------|
| PHP 8.1+ | ✅ Complet |
| PHP 8.0 | ✅ Complet |
| PHP 7.4 | ⚠️ Non testé (type hints compatibles) |

**Systèmes d'exploitation :** ✅ Linux, ✅ macOS, ✅ Windows (avec limitations sur les permissions)

## Exemple complet

```php
<?php

declare(strict_types=1);

use AndyDefer\PhpServices\Services\FileSystemService;
use AndyDefer\PhpServices\Enums\PermissionMode;

$fs = new FileSystemService();

// 1. Créer une structure de dossiers
$fs->makeDirectory('/tmp/demo/data', PermissionMode::DIRECTORY, true);

// 2. Écrire un fichier
$fs->put('/tmp/demo/data/users.json', json_encode([
    ['id' => 1, 'name' => 'Alice'],
    ['id' => 2, 'name' => 'Bob']
]));

// 3. Vérifier et lire
if ($fs->isFile('/tmp/demo/data/users.json')) {
    $content = $fs->get('/tmp/demo/data/users.json');
    echo "Fichier lu : " . strlen($content) . " bytes\n";
}

// 4. Copier et modifier
$fs->copy('/tmp/demo/data/users.json', '/tmp/demo/data/users.backup.json');
$fs->append('/tmp/demo/data/users.json', "\n// Fin du fichier");

// 5. Informations
$size = $fs->size('/tmp/demo/data/users.json');
$mtime = $fs->lastModified('/tmp/demo/data/users.json');
echo "Taille : {$size} bytes, Modifié le : " . date('Y-m-d H:i:s', $mtime) . "\n";

// 6. Lister et nettoyer
$files = $fs->glob('/tmp/demo/data/*.json');
echo "Fichiers JSON : " . count($files) . "\n";

$fs->deleteDirectory('/tmp/demo');
```

## Voir aussi

- `FileSystemInterface` - Contrat implémenté par cette classe
- `PermissionMode` - Enumération pour les permissions des dossiers
- `VfsStreamService` - Alternative pour les tests avec système de fichiers virtuel
```
---