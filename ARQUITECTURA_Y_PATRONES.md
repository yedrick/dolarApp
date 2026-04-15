# DólarApp - Guía de Arquitectura, Patrones y Principios

## 1. Arquitectura General: Domain-Driven Design (DDD) + Clean Architecture

### Estructura de Capas

```
app/
├── Domain/              ← Capa más interna (reglas de negocio)
│   ├── ExchangeRate/    
│   │   ├── Entities/     (ExchangeRate)
│   │   ├── Repositories/ (Interfaces)
│   │   └── ValueObjects/ (ExchangeRateType, Money)
│   ├── Offer/
│   │   ├── Entities/     (Offer)
│   │   └── Repositories/
│   └── Message/
│       ├── Entities/
│       └── Repositories/
│
├── Application/         ← Casos de uso (orquestación)
│   ├── ExchangeRate/UseCases/
│   ├── Offer/UseCases/
│   └── Message/UseCases/
│
└── Infrastructure/      ← Frameworks, DB, HTTP, UI
    ├── Http/Controllers/
    ├── Repositories/    (Implementaciones Eloquent)
    ├── Models/          (Eloquent Models)
    └── Console/Commands/
```

### Flujo de Dependencias (Dependency Rule)
```
Infrastructure → Application → Domain
     (Externo)      (Casos de uso)   (Núcleo)
```

**Principio**: Las capas externas dependen de las internas, NUNCA al revés.

---

## 2. Principios SOLID Aplicados

### S - Single Responsibility Principle (SRP)

```php
// ❌ ANTES (mala práctica)
class OfferController {
    public function create(Request $request) {
        // Valida, crea oferta, envía email, actualiza estadísticas, etc.
    }
}

// ✅ DESPUÉS (SRP)
class CreateOfferUseCase {
    public function execute(CreateOfferDTO $dto): Offer {
        // Solo crea la oferta
    }
}

class OfferEmailService {
    public function sendNewOfferNotification(Offer $offer): void {
        // Solo envía emails
    }
}
```

**Ejemplos en el proyecto:**
- `CreateOfferUseCase`: Solo crea ofertas
- `DolarApiClient`: Solo consume API externa
- `ExchangeRateRepository`: Solo persiste tasas

---

### O - Open/Closed Principle (OCP)

```php
// ✅ ABIERTO para extensión, CERRADO para modificación

interface ExchangeRateProviderInterface {
    public function fetchRate(): ExchangeRate;
}

// Proveedor 1: DolarApi
class DolarApiProvider implements ExchangeRateProviderInterface {
    public function fetchRate(): ExchangeRate { /* ... */ }
}

// Proveedor 2: Otro servicio (fácil de agregar)
class AnotherApiProvider implements ExchangeRateProviderInterface {
    public function fetchRate(): ExchangeRate { /* ... */ }
}

// UseCase no se modifica, solo recibe la nueva implementación
class FetchExchangeRatesFromApiUseCase {
    public function __construct(
        private readonly ExchangeRateProviderInterface $provider // Inyectado
    ) {}
}
```

**Ejemplo real:** Agregué `image_path` a mensajes sin modificar código existente, usando parámetro opcional.

---

### L - Liskov Substitution Principle (LSP)

```php
// ✅ Cualquier implementación de la interfaz es intercambiable

interface MessageRepositoryInterface {
    public function sendMessage(int $conversationId, int $senderId, string $content): Message;
}

// Eloquent implementa la interfaz
class EloquentMessageRepository implements MessageRepositoryInterface {
    public function sendMessage(...): Message { /* usa Eloquent */ }
}

// Mock para tests
class InMemoryMessageRepository implements MessageRepositoryInterface {
    public function sendMessage(...): Message { /* usa array */ }
}

// El UseCase no sabe/care cuál implementación recibe
class SendMessageUseCase {
    public function __construct(
        private readonly MessageRepositoryInterface $repository
    ) {}
    
    public function execute(...) {
        $message = $this->repository->sendMessage(...); // Funciona igual
    }
}
```

---

### I - Interface Segregation Principle (ISP)

```php
// ❌ ANTES: Interfaz grande
interface RepositoryInterface {
    public function find($id);
    public function save($entity);
    public function delete($id);
    public function paginate($perPage);
    public function search($query);
    public function export($format);
    public function import($data);
}

// ✅ DESPUÉS: Interfaces pequeñas y específicas
interface ReadableRepositoryInterface {
    public function find($id): ?Entity;
}

interface WritableRepositoryInterface {
    public function save($entity): void;
    public function delete($id): void;
}

interface PaginableRepositoryInterface {
    public function paginate(int $perPage): Paginator;
}

// Cada repositorio implementa solo lo que necesita
class ExchangeRateRepository implements ReadableRepositoryInterface, WritableRepositoryInterface {
    // Solo 3 métodos, no 7
}
```

**En el proyecto:** `MessageRepositoryInterface` solo tiene métodos de mensajes, no métodos genéricos.

---

### D - Dependency Inversion Principle (DIP)

```php
// ❌ ANTES: Dependencia directa (acoplado)
class SendMessageUseCase {
    private $repository;
    
    public function __construct() {
        $this->repository = new EloquentMessageRepository(); // Concreto
    }
}

// ✅ DESPUÉS: Dependencia de abstracción (desacoplado)
class SendMessageUseCase {
    public function __construct(
        private readonly MessageRepositoryInterface $repository // Interfaz
    ) {}
}

// Inyección de dependencias en Laravel
// app/Providers/AppServiceProvider.php
class AppServiceProvider extends ServiceProvider {
    public function register(): void {
        $this->app->bind(
            MessageRepositoryInterface::class,
            EloquentMessageRepository::class
        );
    }
}
```

---

## 3. Patrones de Diseño Utilizados

### 3.1 Repository Pattern

```php
// Domain: Contrato (interfaz)
interface ExchangeRateRepositoryInterface {
    public function updateOrCreateByType(ExchangeRate $rate): void;
    public function getCurrentRate(string $type): ?ExchangeRate;
}

// Infrastructure: Implementación concreta
class EloquentExchangeRateRepository implements ExchangeRateRepositoryInterface {
    public function updateOrCreateByType(ExchangeRate $rate): void {
        ExchangeRateModel::updateOrCreate(
            ['type' => $rate->getType()->value],
            [
                'buy_price' => $rate->getBuyPrice(),
                'sell_price' => $rate->getSellPrice(),
                'source' => $rate->getSource(),
            ]
        );
    }
}

// Application: Uso
class FetchExchangeRatesFromApiUseCase {
    public function __construct(
        private readonly ExchangeRateRepositoryInterface $repository
    ) {}
    
    private function saveToDatabase(string $type, float $buy, float $sell, string $source): void {
        $rate = ExchangeRate::create(...);
        $this->repository->updateOrCreateByType($rate); // Usa interfaz
    }
}
```

**Beneficio**: Podemos cambiar Eloquent por MongoDB, PostgreSQL, o API sin tocar Use Cases.

---

### 3.2 Use Case / Application Service Pattern

```php
// Cada caso de uso es una clase independiente
final class CreateOfferUseCase {
    public function __construct(
        private readonly OfferRepositoryInterface $offerRepository,
        private readonly ExchangeRateRepositoryInterface $rateRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {}
    
    public function execute(CreateOfferDTO $dto): Offer {
        // 1. Validar reglas de negocio
        $rate = $this->rateRepository->getCurrentRate($dto->currency);
        
        // 2. Crear entidad
        $offer = Offer::create(
            userId: $dto->userId,
            type: OfferType::from($dto->type),
            amount: $dto->amount,
            price: $dto->price,
        );
        
        // 3. Persistir
        $this->offerRepository->save($offer);
        
        // 4. Disparar eventos
        $this->eventDispatcher->dispatch(new OfferCreatedEvent($offer));
        
        return $offer;
    }
}
```

**Beneficio**: Cada operación de negocio está encapsulada, testeable independientemente.

---

### 3.3 DTO (Data Transfer Object) Pattern

```php
// DTO: Transporta datos entre capas sin lógica
final class DolarApiResponseDTO {
    public function __construct(
        public readonly string $moneda,
        public readonly string $casa,
        public readonly string $nombre,
        public readonly float $compra,
        public readonly float $venta,
        public readonly string $fechaActualizacion,
    ) {}
    
    // Factory method para crear desde API
    public static function fromApiResponse(array $data): self {
        return new self(
            moneda: $data['moneda'],
            casa: $data['casa'],
            nombre: $data['nombre'],
            compra: (float) $data['compra'],
            venta: (float) $data['venta'],
            fechaActualizacion: $data['fechaActualizacion'],
        );
    }
}
```

**Uso**:
```php
// Cliente API retorna DTO
$dto = $this->apiClient->getOfficialRate();

// UseCase usa los datos tipados
$this->saveToDatabase('oficial', $dto->compra, $dto->venta, 'DolarApi');
```

---

### 3.4 Value Object Pattern

```php
// Value Object: Inmutable, sin identidad, se compara por valor
final class ExchangeRateType {
    private function __construct(
        private readonly string $value
    ) {
        if (!in_array($value, ['oficial', 'paralelo', 'librecambista'])) {
            throw new InvalidArgumentException("Tipo inválido: {$value}");
        }
    }
    
    public static function oficial(): self {
        return new self('oficial');
    }
    
    public static function paralelo(): self {
        return new self('paralelo');
    }
    
    public static function from(string $value): self {
        return new self($value);
    }
    
    public function value(): string {
        return $this->value;
    }
    
    // Dos Value Objects con mismo valor son iguales
    public function equals(self $other): bool {
        return $this->value === $other->value;
    }
}

// Uso
$type = ExchangeRateType::oficial();
$rate = ExchangeRate::create(type: $type, ...);
```

---

### 3.5 Entity Pattern

```php
// Entity: Tiene identidad única y puede cambiar de estado
final class ExchangeRate {
    private ?int $id = null; // Identidad
    
    private function __construct(
        private readonly ExchangeRateType $type,
        private readonly float $buyPrice,
        private readonly float $sellPrice,
        private readonly string $source,
        private readonly DateTimeImmutable $createdAt,
    ) {}
    
    // Factory method
    public static function create(
        ExchangeRateType $type,
        float $buyPrice,
        float $sellPrice,
        string $source,
    ): self {
        return new self(
            type: $type,
            buyPrice: $buyPrice,
            sellPrice: $sellPrice,
            source: $source,
            createdAt: new DateTimeImmutable(),
        );
    }
    
    // Getters (sin setters, inmutable)
    public function getType(): ExchangeRateType { return $this->type; }
    public function getBuyPrice(): float { return $this->buyPrice; }
    public function getSellPrice(): float { return $this->sellPrice; }
}
```

---

### 3.6 Strategy Pattern (para APIs de tasas)

```php
interface ExchangeRateProviderStrategy {
    public function fetch(): ExchangeRate;
    public function supports(string $source): bool;
}

class DolarApiStrategy implements ExchangeRateProviderStrategy {
    public function fetch(): ExchangeRate {
        $response = Http::get('https://bo.dolarapi.com/v1/dolares/oficial');
        $dto = DolarApiResponseDTO::fromApiResponse($response->json());
        return ExchangeRate::create(...);
    }
    
    public function supports(string $source): bool {
        return $source === 'dolarapi';
    }
}

// Selector dinámico
class ExchangeRateProviderSelector {
    public function __construct(
        private readonly iterable $strategies // Inyecta todas las estrategias
    ) {}
    
    public function select(string $source): ExchangeRateProviderStrategy {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($source)) {
                return $strategy;
            }
        }
        throw new InvalidArgumentException("No hay estrategia para: {$source}");
    }
}
```

---

## 4. Arquitectura del Chat (Ejemplo Completo)

### Flujo de envío de mensaje:

```
1. Usuario envía mensaje
   ↓
2. ChatWebController.send(Request)
   - Valida request HTTP (validation)
   - Maneja subida de imagen
   - Obtiene usuario destinatario
   - Delega a UseCase
   ↓
3. SendMessageUseCase.execute(...)
   - Busca o crea conversación (findConversation/createConversation)
   - Si es nueva y hay monto → reserva en oferta
   - Persiste mensaje (sendMessage)
   - Retorna resultado
   ↓
4. EloquentMessageRepository (implementación)
   - Usa Eloquent para guardar en DB
   - Retorna MessageModel
   ↓
5. ChatWebController retorna redirect
   - Usuario ve mensaje enviado
```

### Código del flujo:

```php
// 1. Controller (Infrastructure/Web)
class ChatWebController extends BaseController {
    public function send(Request $request, int $id): RedirectResponse {
        // HTTP-specific logic
        $request->validate(['message' => 'nullable|string', 'image' => 'nullable|image']);
        
        $conversation = ConversationModel::findOrFail($id);
        $otherUser = $conversation->otherUser(auth()->id());
        
        // Subida de archivo (HTTP concern)
        $imagePath = $request->hasFile('image') 
            ? $request->file('image')->store('chat-images', 'public')
            : null;
        
        // Delegar al caso de uso (Clean)
        $this->sendMessage->execute(
            senderId: auth()->id(),
            receiverId: $otherUser->id,
            offerId: $conversation->offer_id,
            content: $request->input('message', ''),
            imagePath: $imagePath
        );
        
        return back()->with('success', 'Mensaje enviado');
    }
}

// 2. Use Case (Application)
class SendMessageUseCase {
    public function __construct(
        private readonly MessageRepositoryInterface $messageRepository,
        private readonly OfferRepositoryInterface $offerRepository,
    ) {}
    
    public function execute(
        int $senderId,
        int $receiverId,
        int $offerId,
        string $content,
        ?float $amount = null,
        ?string $imagePath = null,
    ): array {
        // Buscar o crear conversación
        $conversation = $this->messageRepository->findConversation($senderId, $receiverId, $offerId);
        $isNew = !$conversation;
        
        if (!$conversation) {
            $conversation = $this->createConversation($senderId, $receiverId, $offerId);
        }
        
        // Reservar monto si es nueva conversación
        if ($isNew && $amount !== null && $amount > 0) {
            $this->reserveAmount($offerId, $amount);
        }
        
        // Enviar mensaje
        $message = $this->messageRepository->sendMessage(
            $conversation->id,
            $senderId,
            $content,
            $imagePath
        );
        
        return ['conversation_id' => $conversation->id, 'message' => [...]];
    }
}

// 3. Repository Interface (Domain)
interface MessageRepositoryInterface {
    public function findConversation(int $userId, int $otherUserId, int $offerId): ?Conversation;
    public function createConversation(int $userOneId, int $userTwoId, int $offerId): Conversation;
    public function sendMessage(int $conversationId, int $senderId, string $content, ?string $imagePath): Message;
}

// 4. Repository Implementation (Infrastructure)
class EloquentMessageRepository implements MessageRepositoryInterface {
    public function sendMessage(...): Message {
        $model = MessageModel::create([
            'conversation_id' => $conversationId,
            'sender_id' => $senderId,
            'content' => $content,
            'image_path' => $imagePath,
            'is_read' => false,
        ]);
        
        // Actualizar timestamp de conversación
        ConversationModel::where('id', $conversationId)
            ->update(['last_message_at' => now()]);
        
        return $this->toEntity($model);
    }
}
```

---

## 5. Testing

```php
// Unit Test: Solo lógica de negocio
class SendMessageUseCaseTest extends TestCase {
    public function test_creates_conversation_if_not_exists(): void {
        $messageRepo = $this->createMock(MessageRepositoryInterface::class);
        $offerRepo = $this->createMock(OfferRepositoryInterface::class);
        
        $messageRepo->expects($this->once())
            ->method('findConversation')
            ->willReturn(null);
        
        $messageRepo->expects($this->once())
            ->method('createConversation')
            ->with(1, 2, 5);
        
        $useCase = new SendMessageUseCase($messageRepo, $offerRepo);
        $useCase->execute(senderId: 1, receiverId: 2, offerId: 5, content: 'Hola');
    }
}

// Feature Test: Flujo completo HTTP
class ChatFeatureTest extends TestCase {
    public function test_user_can_send_message(): void {
        $user = User::factory()->create();
        $conversation = Conversation::factory()->create(['user_one_id' => $user->id]);
        
        $response = $this->actingAs($user)
            ->post("/chat/{$conversation->id}/send", [
                'message' => 'Hola mundo'
            ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('messages', [
            'content' => 'Hola mundo',
            'sender_id' => $user->id,
        ]);
    }
}
```

---

## 6. Comandos Artisan (Scheduled Tasks)

```php
// Comando con propósito único
class FetchExchangeRatesCommand extends Command {
    protected $signature = 'rates:fetch';
    protected $description = 'Obtiene tasas desde DolarApi';
    
    public function handle(FetchExchangeRatesFromApiUseCase $useCase): int {
        $results = $useCase->execute();
        
        foreach ($results as $type => $data) {
            $this->info("{$type}: {$data['compra']} / {$data['venta']}");
        }
        
        return self::SUCCESS;
    }
}

// Programación automática
// routes/console.php
Schedule::command('rates:fetch')->everyThirtyMinutes();
Schedule::command('offers:expire')->dailyAt('00:00');
```

---

## 7. Resumen de Buenas Prácticas

| Práctica | Implementación |
|----------|----------------|
| **Dependency Injection** | Constructor injection en todas las clases |
| **Inmutabilidad** | Entidades y Value Objects sin setters |
| **Type Safety** | `declare(strict_types=1)` + type hints |
| **Final classes** | `final class` para evitar herencia |
| **Readonly properties** | `private readonly` donde aplique |
| **Named arguments** | `execute(senderId: 1, receiverId: 2)` |
| **Null safety** | `?string` para valores opcionales |
| **Error handling** | Try-catch en APIs externas, report() para logs |

---

## 8. Flujo de Datos del Sistema

```
┌─────────────────────────────────────────────────────────┐
│                      PRESENTATION                        │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │   Web UI     │  │    API       │  │   Console    │  │
│  │  (Blade)     │  │  (JSON)      │  │  (Commands)  │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
└─────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────┐
│                     APPLICATION                        │
│  ┌──────────────────────────────────────────────────┐ │
│  │              Use Cases / Services                 │ │
│  │  CreateOfferUseCase | SendMessageUseCase | ...   │ │
│  └──────────────────────────────────────────────────┘ │
│  ┌──────────────────────────────────────────────────┐ │
│  │              DTOs / Commands / Queries            │ │
│  │  CreateOfferDTO | GetMessagesQuery | ...         │ │
│  └──────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────┐
│                       DOMAIN                           │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │  Entities    │  │ ValueObjects │  │   Events     │  │
│  │  (Offer)     │  │ (Money)      │  │ (OfferCreated)│  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
│  ┌──────────────────────────────────────────────────┐ │
│  │           Repository Interfaces                  │ │
│  │  OfferRepositoryInterface | MessageRepository... │ │
│  └──────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────┐
│                   INFRASTRUCTURE                       │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │  Eloquent    │  │   HTTP       │  │   Console    │  │
│  │  Models      │  │   Clients    │  │   Kernel     │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
│  ┌──────────────────────────────────────────────────┐ │
│  │         Repository Implementations               │ │
│  │  EloquentOfferRepository | EloquentMessage...   │ │
│  └──────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────┘
```

---

## 9. Configuración de Ejecución

### Desarrollo local:
```bash
# Servidor web
php artisan serve

# Worker de queue (para procesos en background)
php artisan queue:work

# Schedule (tareas programadas)
php artisan schedule:work
```

### Producción:
```bash
# Cron job cada minuto
* * * * * cd /var/www/dolarapp && php artisan schedule:run >> /dev/null 2>&1

# Worker de queue (supervisor)
php artisan queue:work --sleep=3 --tries=3
```

---

## 10. Checklist de Calidad de Código

- [x] Cada clase tiene una sola responsabilidad (SRP)
- [x] Interfaces pequeñas y específicas (ISP)
- [x] Dependencias inyectadas, no instanciadas (DIP)
- [x] Código abierto para extensión, cerrado para modificación (OCP)
- [x] Implementaciones sustituibles (LSP)
- [x] Capas bien separadas (Domain → Application → Infrastructure)
- [x] Tipos estrictos en todas las clases
- [x] Clases marcadas como `final`
- [x] Propiedades `readonly` donde aplique
- [x] DTOs para transferencia de datos
- [x] Value Objects para tipos de dominio
- [x] Repositories con interfaces
- [x] Use Cases independientes
- [x] Tests unitarios y de integración

---

## Conclusión

Este proyecto implementa una **arquitectura limpia** basada en **DDD** con:

1. **Separación clara de responsabilidades** entre capas
2. **Independencia del framework** (Laravel solo en Infrastructure)
3. **Testabilidad** gracias a interfaces e inyección de dependencias
4. **Extensibilidad** sin modificar código existente
5. **Mantenibilidad** con código explícito y tipado

Las tasas de cambio se actualizan automáticamente cada 30 minutos, las ofertas expiran después de 72 horas, y todo el sistema sigue principios SOLID para garantizar calidad y escalabilidad.
