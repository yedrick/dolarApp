# DólarApp

> Plataforma de conversión y mercado de cambio de divisas para economías con múltiples tipos de cambio (Bolivia).
>
> **Nuevas funcionalidades**: Chat entre usuarios, sistema de reserva de montos, diseño mejorado con glassmorphism y animaciones suaves.

[![CI/CD](https://github.com/tu-usuario/dolarapp/actions/workflows/ci-cd.yml/badge.svg)](https://github.com/tu-usuario/dolarapp/actions)
[![PHP](https://img.shields.io/badge/PHP-8.2-blue)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-11.x-red)](https://laravel.com)

---

## Instalación paso a paso

### Requisitos previos

- PHP 8.2+ con extensiones: `pdo`, `pdo_mysql` (o `pdo_sqlite`), `mbstring`, `openssl`
- Composer 2.x
- MySQL 8.0+ **o** SQLite (para desarrollo)
- Node.js (opcional, solo si usas Vite/npm)

### 1. Clonar e instalar dependencias

```bash
# Clonar el repositorio
git clone https://github.com/yedrick/dolarApp.git
cd dolarapp

# Instalar dependencias PHP
composer install
```

### 2. Configurar entorno

```bash
# Copiar archivo de entorno
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

Editar `.env` con tu configuración de base de datos:

```env
# Para MySQL (recomendado en producción):
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dolarapp
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password

# Para SQLite (desarrollo rápido):
# DB_CONNECTION=sqlite
# DB_DATABASE=/ruta/absoluta/al/proyecto/database/database.sqlite
```

### 3. Preparar base de datos

```bash
# Crear la base de datos en MySQL:
mysql -u root -p -e "CREATE DATABASE dolarapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Para SQLite (alternativa):
touch database/database.sqlite

# Ejecutar migraciones
php artisan migrate

# Sembrar datos iniciales (tipos de cambio)
php artisan db:seed
```

### 4. Iniciar servidor

```bash
php artisan serve
```

Visita: **http://localhost:8000**

---

## Estructura del proyecto

```
app/
├── Domain/                      ← Núcleo de negocio (sin dependencias externas)
│   ├── ExchangeRate/
│   │   ├── Entities/            ExchangeRate (lógica de conversión)
│   │   ├── ValueObjects/        Money, ExchangeRateType
│   │   └── Repositories/        Interfaz (contrato — DIP)
│   ├── Offer/
│   │   ├── Entities/            Offer (Aggregate Root)
│   │   ├── ValueObjects/        OfferType, OfferStatus
│   │   ├── Events/              OfferPublished
│   │   └── Repositories/        Interfaz (contrato)
│   └── Message/
│       ├── Entities/            Conversation, Message
│       └── Repositories/        Interfaz (contrato)
│
├── Application/                 ← Casos de uso y orquestación
│   ├── ExchangeRate/UseCases/   GetExchangeRates, ConvertCurrency
│   ├── Offer/UseCases/          CreateOffer, ListOffers
│   └── Message/UseCases/        SendMessage, GetConversations, GetMessages
│
├── Infrastructure/              ← Implementaciones concretas
│   ├── Repositories/            Eloquent (implementa interfaces de dominio)
│   ├── Models/                  Modelos Eloquent (solo persistencia)
│   └── Http/Controllers/        Api/ y Web/ (controllers delgados)
│
└── Shared/Exceptions/           Excepciones de dominio
```

---

## API REST

### Endpoints públicos

| Método | Ruta                             | Descripción                    |
| ------ | -------------------------------- | ------------------------------ |
| `GET`  | `/api/v1/exchange-rates`         | Listar tipos de cambio         |
| `POST` | `/api/v1/exchange-rates/convert` | Convertir moneda               |
| `GET`  | `/api/v1/offers`                 | Ofertas activas                |
| `POST` | `/api/v1/register`               | Registrar usuario              |
| `POST` | `/api/v1/login`                  | Iniciar sesión (obtener token) |

### Endpoints protegidos (Bearer Token)

| Método   | Ruta                         | Descripción               |
| -------- | ---------------------------- | ------------------------- |
| `POST`   | `/api/v1/offers`             | Publicar oferta           |
| `GET`    | `/api/v1/offers/my`          | Mis ofertas               |
| `DELETE` | `/api/v1/offers/{id}`        | Cerrar oferta             |
| `POST`   | `/api/v1/logout`             | Cerrar sesión             |
| `GET`    | `/api/v1/chat/conversations` | Listar mis conversaciones |
| `POST`   | `/api/v1/chat/start`         | Iniciar conversación      |
| `GET`    | `/api/v1/chat/{id}/messages` | Obtener mensajes          |
| `POST`   | `/api/v1/chat/{id}/messages` | Enviar mensaje            |

### Ejemplo de uso

```bash
# Obtener tipos de cambio
curl http://localhost:8000/api/v1/exchange-rates

# Convertir moneda
curl -X POST http://localhost:8000/api/v1/exchange-rates/convert \
  -H "Content-Type: application/json" \
  -d '{"amount": 690, "from": "BOB", "rate_type": "paralelo"}'

# Registrarse
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Juan","email":"juan@test.com","password":"Pass1234!","password_confirmation":"Pass1234!"}'

# Publicar oferta (con token)
curl -X POST http://localhost:8000/api/v1/offers \
  -H "Authorization: Bearer TU_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"type":"venta","price":6.97,"amount":500,"contact_info":"77001122"}'
```

---

## Rutas web (vistas Blade)

| Ruta              | Descripción                                  |
| ----------------- | -------------------------------------------- |
| `/`               | Inicio con hero, conversor y últimas ofertas |
| `/offers`         | Mercado de ofertas (público)                 |
| `/exchange-rates` | Tipos de cambio y conversor completo         |
| `/login`          | Iniciar sesión                               |
| `/register`       | Crear cuenta                                 |
| `/dashboard`      | Panel personal (requiere auth)               |
| `/offers/create`  | Publicar nueva oferta (requiere auth)        |
| `/profile`        | Perfil y configuración (requiere auth)       |
| `/chat`           | Mis conversaciones (requiere auth)           |
| `/chat/{id}`      | Ver conversación (requiere auth)             |

## Ejecutar pruebas

```bash
# Todas las pruebas (89 tests)
php artisan test

# Por suite
php artisan test --testsuite=Unit        # Pruebas unitarias (dominio y casos de uso)
php artisan test --testsuite=Feature     # Integración API + Web
php artisan test --testsuite=Web         # Solo vistas Blade
php artisan test --testsuite=Acceptance  # Flujos de negocio completos

# Con cobertura (requiere Xdebug o PCOV)
php artisan test --coverage --min=70
```

### Descripción de suites

| Suite            | Tests | Qué valida                                                        |
| ---------------- | ----- | ----------------------------------------------------------------- |
| Unit/Domain      | 28    | Money, ExchangeRateType, conversiones, creación de Offer, eventos |
| Unit/Application | 9     | Use Cases con repositorios mockeados                              |
| Feature/Api      | 10    | Flujo completo API REST con BD en memoria                         |
| Feature/Web      | 22    | Vistas Blade, autenticación sesión, formularios                   |
| Acceptance       | 5     | Escenarios de negocio narrados (Carlos publica, María visualiza)  |

---

## Comandos Artisan personalizados

```bash
# Expirar ofertas con más de 72 horas (ejecutar en cron diario)
php artisan offers:expire

# Actualizar tipos de cambio desde API externa (DolarApi)
php artisan rates:seed --api

# Actualizar tipos de cambio de forma interactiva
php artisan rates:seed --interactive

# Seed de datos de prueba (usuarios, ofertas, tipos de cambio)
php artisan db:seed
```

---

## Pipeline CI/CD

El archivo `.github/workflows/ci-cd.yml` implementa:

```
Commit → composer install → Pint (lint) → PHPStan →
Unit Tests → Integration Tests → Web Tests → Acceptance Tests →
Deploy automático (solo en rama main)
```

---

## Principios SOLID aplicados

| Principio | Ejemplo en el código                                                                     |
| --------- | ---------------------------------------------------------------------------------------- |
| **SRP**   | `ExchangeRate` solo calcula conversiones; `EloquentExchangeRateRepository` solo persiste |
| **OCP**   | Nuevos tipos de cambio sin modificar `ExchangeRateType` (agregar constante)              |
| **LSP**   | `EloquentOfferRepository` es intercambiable con cualquier otra implementación            |
| **ISP**   | `OfferRepositoryInterface` solo define métodos necesarios para ofertas                   |
| **DIP**   | Domain depende de interfaces; Infrastructure las implementa                              |

---

## Calidad ISO/IEC 25010

| Característica | Implementación                                            |
| -------------- | --------------------------------------------------------- |
| Funcionalidad  | Casos de uso con validaciones de dominio                  |
| Fiabilidad     | 89 pruebas en 3 niveles con BD en memoria                 |
| Usabilidad     | API REST con mensajes en español, vistas Blade responsive |
| Seguridad      | Sanctum tokens, Form Requests, CSRF, sesión cifrada       |
| Mantenibilidad | DDD, SOLID, separación estricta de capas                  |
| Portabilidad   | SQLite/MySQL/PostgreSQL configurables en `.env`           |

---

## Tecnologías

- **Laravel 11** — Framework PHP
- **Laravel Sanctum** — Autenticación API con tokens Bearer
- **PHPUnit 11** — Pruebas unitarias, integración y aceptación
- **Laravel Pint** — Formateo de código (PSR-12)
- **PHPStan** — Análisis estático nivel 5
- **GitHub Actions** — CI/CD automatizado
- **DM Sans + DM Mono** — Tipografía del frontend
- **CSS3** — Glassmorphism, animaciones, gradientes
- **JavaScript Vanilla** — Interacciones del modal y navbar scroll
- **DolarApi** — API externa para tipos de cambio actualizados

---


**Desarrollado para facilitar el cambio de divisas en Bolivia**
