# PHP Services

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-10%2F11%2F12%2F13%2F14%2F15-ff2d20.svg)](https://laravel.com)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)

## Installation

```bash
composer require andydefer/php-services
```

## Philosophie du package

Ce package est une collection de services réutilisables pour PHP et Laravel.

Il s'inspire de plusieurs principes du génie logiciel qui servent de **boussole**, pas de **carcan** :

| Principe | Ce qu'il encourage | Ce qu'il n'impose pas |
|----------|-------------------|----------------------|
| **Composition Over Inheritance** | Préférer l'injection de dépendances à l'héritage | L'héritage reste possible quand il est pertinent |
| **Dependency Inversion** | Dépendre des interfaces plutôt que des classes concrètes | Les DTOs et Value Objects peuvent être concrets |
| **Capability-Based Design** | Exposer des capacités spécifiques plutôt que des services fourre-tout | Un service peut avoir plusieurs méthodes cohésives |
| **Domain-Driven Design** | Organiser le code par domaine fonctionnel | La structure peut évoluer librement |

L'objectif est d'obtenir un code **testable**, **découplé** et **maintenable**, sans tomber dans l'extrémisme architectural.

---

## Ce qu'un Service est

```php
// ✅ Un service = un conteneur de méthodes qui partagent un même domaine
class OrderCalculatorService
{
    // ✅ Des dépendances injectées dans le constructeur
    public function __construct(
        private readonly TaxService $taxService,
        private readonly OrderConfig $config,
    ) {}
    
    // ✅ Des méthodes qui reçoivent leurs données en paramètres
    public function calculateTotal(OrderRecord $order): float
    {
        $subtotal = $this->calculateSubtotal($order);
        $tax = $this->taxService->calculate($subtotal);
        
        return $subtotal + $tax;
    }
    
    // ✅ Pas d'état interne, pas de mémoire entre les appels
    private function calculateSubtotal(OrderRecord $order): float
    {
        return array_reduce($order->items, fn($c, $i) => $c + ($i->price * $i->quantity), 0);
    }
}
```

---

## Pourquoi cette philosophie ?

### Problème : Les traits (anti-pattern)

```php
// ❌ Un trait : impossible à tester isolément
trait FileCreator
{
    private Filesystem $files;
    
    public function createFile(string $path, string $content): bool
    {
        $this->files = new Filesystem();  // Dépendance cachée
        return $this->files->put($path, $content);
    }
}

class TaskDirective extends AbstractDirective
{
    use FileCreator;  // ❌ Couplage implicite, test impossible
}
```

### Solution : Le service

```php
// ✅ Un service : testable, injectable, découplé
class FileCreatorService
{
    public function __construct(
        private readonly Filesystem $files,  // ✅ Injection explicite
    ) {}
    
    public function createFile(string $path, string $content): bool
    {
        return $this->files->put($path, $content);
    }
}

class TaskDirective extends AbstractDirective
{
    public function __construct(
        private readonly FileCreatorService $fileCreator,  // ✅ Dépendance claire
    ) {}
}
```

---

## Exemple complet : La testabilité en action

```php
// Le service
class UserService
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly LoggerInterface $logger,
    ) {}
    
    public function findActiveUser(int $id): ?User
    {
        $this->logger->info('Searching for active user', ['id' => $id]);
        
        $user = $this->repository->findActive($id);
        
        if (!$user) {
            $this->logger->warning('Active user not found', ['id' => $id]);
            return null;
        }
        
        return $user;
    }
}

// Le test
class UserServiceTest extends TestCase
{
    public function test_findActiveUser_returns_user_when_exists(): void
    {
        // ✅ Toutes les dépendances sont mockables
        $repository = $this->createMock(UserRepository::class);
        $repository->method('findActive')->willReturn($user);
        
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info');
        
        $service = new UserService($repository, $logger);
        $result = $service->findActiveUser(1);
        
        $this->assertSame($user, $result);
        
        // ✅ Aucune base de données réelle
        // ✅ Aucun fichier log réel
        // ✅ Test rapide, isolé, fiable
    }
}
```

---

## Quand déroger aux principes ?

La philosophie de ce package est **pragmatique** :

| Règle | Peut-on déroger ? | Exemple |
|-------|-------------------|---------|
| Pas d'état interne | ⚠️ Exception rare | Cache interne avec TTL court |
| Pas de `final` | ✅ Oui | Classe utilitaire sans dépendances |
| Dépendre des interfaces | ✅ Oui | Value Objects, DTOs, Configs |
| Une capacité par service | ✅ Oui | `OrderCalculatorService` a 5 méthodes liées |

**Le critère ultime** : Est-ce que mon code reste **testable** ?

---

## Services disponibles

| Service | Description |
|---------|-------------|
| `ModelTransformableService` | Convertit les modèles Eloquent en Data DTOs typés |

[Voir la documentation complète](docs/api-references/SERVICES.md)

## License

MIT © [Andy Defer](https://github.com/andydefer)
