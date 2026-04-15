# Guion de Video: Arquitectura DólarApp
## Duración estimada: 15-20 minutos

---

## INTRODUCCIÓN (1 minuto)

**[SLIDE: Logo DólarApp + título]**

**LOCUTOR:**
"Hola, en este video vamos a explicar la arquitectura de DólarApp, una aplicación de intercambio de divisas construida con Laravel pero aplicando patrones enterprise. No es un Laravel típico... aquí aplicamos DDD, Clean Architecture y principios SOLID. Vamos a ver cómo está organizado y por qué."

---

## SECCIÓN 1: ¿POR QUÉ ESTA ARQUITECTURA? (2 minutos)

**[SLIDE: Diagrama tradicional MVC vs Nuestra arquitectura]**

**LOCUTOR:**
"Primero, ¿por qué no usamos el MVC tradicional de Laravel?

En MVC tradicional, los Controllers hacen TODO: validan, llaman a la base de datos, envían emails, todo en un mismo archivo. Eso funciona para proyectos pequeños, pero cuando creces...

**[SLIDE: Código spagueti vs Código limpio]**

- Es difícil de testear
- El código está acoplado al framework
- Un cambio rompe todo
- No se puede reemplazar la base de datos sin reescribir todo

Nuestra solución: separar en 3 capas bien definidas."

---

## SECCIÓN 2: LAS 3 CAPAS (4 minutos)

**[SLIDE: Pirámide con 3 niveles]**

### CAPA 1: DOMAIN (Núcleo) - 1 minuto
**[SLIDE: Carpeta Domain con subcarpetas]**

**LOCUTOR:**
"La capa más interna es DOMAIN. Aquí van las reglas de negocio puras. No depende de Laravel, no depende de la base de datos, no depende de nada externo.

**[SLIDE: Código de ExchangeRate Entity]**

Por ejemplo, la entidad ExchangeRate define qué es una tasa de cambio: tiene tipo, precio de compra, precio de venta. Esta clase no sabe de dónde vienen los datos ni a dónde van.

En Domain también tenemos:
- **Entities**: objetos con identidad (Offer, ExchangeRate)
- **Value Objects**: tipos inmutables (ExchangeRateType: oficial/paralelo/librecambista)
- **Repository Interfaces**: contratos, solo interfaces, no implementaciones"

### CAPA 2: APPLICATION (Casos de Uso) - 1.5 minutos
**[SLIDE: Carpeta UseCases]**

**LOCUTOR:**
"La segunda capa es APPLICATION. Aquí están los Casos de Uso: cada operación de negocio es una clase independiente.

**[SLIDE: Código de CreateOfferUseCase]**

Por ejemplo, CreateOfferUseCase:
- Recibe un DTO con los datos
- Valida reglas de negocio
- Crea la entidad
- La persiste usando el repositorio
- Dispara eventos si es necesario

**[SLIDE: Lista de UseCases]**

Otros ejemplos:
- SendMessageUseCase: envía mensaje, crea conversación si no existe
- FetchExchangeRatesFromApiUseCase: obtiene tasas de API y las guarda
- ConvertCurrencyUseCase: convierte divisas usando tasas actuales

Caso de uso = una acción que hace el usuario en el sistema."

### CAPA 3: INFRASTRUCTURE (Framework) - 1.5 minutos
**[SLIDE: Carpeta Infrastructure]**

**LOCUTOR:**
"La capa externa es INFRASTRUCTURE. Aquí vive Laravel, Eloquent, HTTP, la base de datos, todo lo que es "técnico".

**[SLIDE: Código de EloquentExchangeRateRepository]**

Los Repositories en Infrastructure implementan las interfaces de Domain. Por ejemplo, EloquentExchangeRateRepository usa Eloquent para guardar en MySQL, pero el UseCase no sabe eso, solo ve la interfaz.

**[SLIDE: Código de ChatWebController]**

Los Controllers también están aquí. Son DELGADOS: solo reciben el Request HTTP, validan, y llaman al UseCase correspondiente. No tienen lógica de negocio."

---

## SECCIÓN 3: PRINCIPIOS SOLID EN ACCIÓN (4 minutos)

### S - Single Responsibility (SRP) - 1 minuto
**[SLIDE: Comparación antes/después]**

**LOCUTOR:**
"Principio SRP: cada clase tiene una sola razón para cambiar.

**[SLIDE: Código malo - Controller gigante]**

Antes: Controller de 200 líneas que valida, crea, envía email, actualiza estadísticas.

**[SLIDE: Código bueno - UseCase pequeño]**

Ahora: CreateOfferUseCase solo crea ofertas. OfferEmailService solo envía emails. Cada uno es pequeño y testeable.

Ejemplo real: DolarApiClient solo consume API, no hace otra cosa."

### D - Dependency Inversion (DIP) - 1.5 minutos
**[SLIDE: Diagrama de dependencias]**

**LOCUTOR:**
"Principio DIP: depende de abstracciones, no de concreciones.

**[SLIDE: Código acoplado]**

Código malo: el UseCase crea directamente EloquentMessageRepository.

**[SLIDE: Código desacoplado]**

Código bueno: el UseCase recibe MessageRepositoryInterface. No sabe si es Eloquent, MongoDB, o un mock para tests.

**[SLIDE: AppServiceProvider]**

En AppServiceProvider configuramos el binding:
```php
$this->app->bind(
    MessageRepositoryInterface::class,
    EloquentMessageRepository::class
);
```

El UseCase dice 'necesito un repository', Laravel le inyecta la implementación. Podemos cambiar a MongoDB cambiando solo esta línea, sin tocar ningún UseCase."

### O - Open/Closed (OCP) - 1.5 minutos
**[SLIDE: Extensión sin modificar]**

**LOCUTOR:**
"Principio OCP: abierto para extensión, cerrado para modificación.

Ejemplo real: agregamos imágenes al chat.

**[SLIDE: Código del UseCase original]**

El SendMessageUseCase original recibía sender, receiver, content.

**[SLIDE: Código extendido]**

Para agregar imágenes, añadimos un parámetro opcional imagePath = null. El código existente sigue funcionando, y el nuevo código usa el parámetro. No modificamos, extendemos.

**[SLIDE: MessageRepositoryInterface]**

La interfaz también añade imagePath opcional. Las implementaciones que no lo usan ignoran el parámetro. Las nuevas lo usan. Todo funciona."

---

## SECCIÓN 4: PATRONES DE DISEÑO (3 minutos)

### Repository Pattern - 1 minuto
**[SLIDE: Diagrama Repository]**

**LOCUTOR:**
"Patrón Repository: aislamos el acceso a datos.

**[SLIDE: Interface vs Implementación]**

Domain define la interfaz: qué operaciones existen (save, find, update).
Infrastructure provee la implementación: cómo se hace (Eloquent, Query Builder).

**[SLIDE: Cambio de base de datos]**

Beneficio: si mañana cambiamos MySQL por PostgreSQL o MongoDB, solo reescribimos EloquentMessageRepository. Los 15 UseCases que usan mensajes ni se enteran."

### DTO Pattern - 1 minuto
**[SLIDE: Código DTO]**

**LOCUTOR:**
"Patrón DTO: objetos que transportan datos tipados entre capas.

**[SLIDE: DolarApiResponseDTO]**

Por ejemplo, cuando consumimos la API de tasas, recibimos un JSON. Lo convertimos a DolarApiResponseDTO con campos tipados: compra es float, fecha es string ISO.

**[SLIDE: Uso del DTO]**

El UseCase recibe el DTO y sabe exactamente qué datos tiene disponibles. No hay arrays mágicos ni strings sueltos."

### Use Case Pattern - 1 minuto
**[SLIDE: Flujo del UseCase]**

**LOCUTOR:**
"Patrón Use Case: cada operación de usuario es una clase.

**[SLIDE: Lista de acciones]**

- Usuario quiere crear oferta → CreateOfferUseCase
- Usuario quiere enviar mensaje → SendMessageUseCase
- Usuario quiere ver conversaciones → GetConversationsUseCase

**[SLIDE: Estructura del UseCase]**

Todos siguen la misma estructura:
1. Constructor con dependencias (repositories)
2. Método execute() con parámetros nombrados
3. Retorna resultado o void
4. Lanza excepciones de dominio si falla

Esto hace que el código sea predecible. Sabes dónde está cada operación."

---

## SECCIÓN 5: EJEMPLO COMPLETO - FLUJO DEL CHAT (4 minutos)

**[SLIDE: Diagrama de secuencia del chat]**

**LOCUTOR:**
"Vamos a ver un ejemplo completo: enviar un mensaje con imagen.

**[SLIDE: Paso 1 - Usuario envía mensaje]**

Paso 1: El usuario selecciona una imagen y escribe en el chat.

**[SLIDE: Paso 2 - Controller recibe]**

Paso 2: ChatWebController.send() recibe el Request HTTP. Aquí:
- Valida que haya mensaje o imagen
- Sube la imagen a storage si existe
- Obtiene el usuario destinatario de la conversación
- Delega al UseCase

**[SLIDE: Código del Controller]**
```php
$imagePath = $request->hasFile('image') 
    ? $request->file('image')->store('chat-images', 'public')
    : null;

$this->sendMessage->execute(
    senderId: auth()->id(),
    receiverId: $otherUser->id,
    content: $request->input('message', ''),
    imagePath: $imagePath
);
```

**[SLIDE: Paso 3 - UseCase orquesta]**

Paso 3: SendMessageUseCase.execute() hace la lógica:
- Busca conversación existente
- Si no existe, la crea (con lógica de quién es user_one/user_two)
- Si es nueva y hay monto, reserva en la oferta
- Envía el mensaje al repository

**[SLIDE: Código del UseCase]**
```php
$conversation = $this->messageRepository->findConversation(...);
if (!$conversation) {
    $conversation = $this->messageRepository->createConversation(...);
    $this->reserveAmount($offerId, $amount); // Lógica de negocio
}
$this->messageRepository->sendMessage($conversation->id, ..., $imagePath);
```

**[SLIDE: Paso 4 - Repository persiste]**

Paso 4: EloquentMessageRepository guarda en MySQL usando Eloquent. También actualiza el timestamp de la conversación.

**[SLIDE: Paso 5 - Respuesta al usuario]**

Paso 5: El Controller redirige de vuelta al chat. El usuario ve su mensaje con la imagen.

**[SLIDE: Auto-refresh con JavaScript]**

Además, hay un JavaScript que cada 5 segundos pide mensajes nuevos al endpoint /chat/{id}/messages, así el chat se siente en tiempo real sin WebSockets complejos."

---

## SECCIÓN 6: COMANDOS Y AUTOMATIZACIÓN (2 minutos)

**[SLIDE: Terminal con comandos]**

**LOCUTOR:**
"La aplicación también tiene tareas automáticas usando Artisan.

**[SLIDE: FetchExchangeRatesCommand]**

Comando: php artisan rates:fetch
- Consume la API de DolarApi (oficial y Binance)
- Guarda las tasas actualizadas
- Se ejecuta cada 30 minutos automáticamente

**[SLIDE: ExpireOffersCommand]**

Comando: php artisan offers:expire
- Marca como expiradas las ofertas con más de 72 horas
- Se ejecuta diario a medianoche

**[SLIDE: console.php]**

En routes/console.php configuramos:
```php
Schedule::command('rates:fetch')->everyThirtyMinutes();
Schedule::command('offers:expire')->dailyAt('00:00');
```

En producción, un cron job ejecuta 'php artisan schedule:run' cada minuto y Laravel decide si toca ejecutar cada comando."

---

## CONCLUSIÓN (1 minuto)

**[SLIDE: Resumen visual]**

**LOCUTOR:**
"Resumiendo, DólarApp usa:

1. **DDD**: Dominio puro sin dependencias externas
2. **Clean Architecture**: 3 capas bien separadas
3. **SOLID**: Código mantenible y testeable
4. **Patrones**: Repository, Use Case, DTO, Value Object

**[SLIDE: Beneficios]**

Beneficios que conseguimos:
- Tests unitarios sin base de datos (usamos mocks)
- Podemos cambiar la base de datos sin tocar lógica de negocio
- Cada clase es pequeña y entendible
- Nuevo desarrollador sabe dónde encontrar cada cosa
- El código "vive" 5 años, no se rompe con cada cambio

**[SLIDE: Código QR o link al repo]**

El código está en GitHub. Puedes revisar la estructura de carpetas, ver cómo están organizados los UseCases, y entender por qué cada archivo está donde está.

Gracias por ver este video. Si tienes dudas, déjalas en comentarios."

---

## NOTAS PARA EDICIÓN

### Recursos visuales necesarios:
1. Logo DólarApp
2. Diagrama de 3 capas (pirámide)
3. Comparativas de código (split screen)
4. Diagramas de flujo (chat, arquitectura)
5. Terminal animada (comandos)
6. Íconos para cada patrón

### Música/Pacing:
- Intro: 10 segundos con logo
- Transiciones entre secciones: 3 segundos
- Código en pantalla: mostrar 5-10 segundos mínimo
- Pausas después de conceptos importantes (2 segundos)

### Callouts/texto en pantalla:
- Resaltar líneas clave de código con recuadros amarillos
- Mostrar nombres de archivos en esquina superior
- Flechas animadas mostrando flujo de datos
