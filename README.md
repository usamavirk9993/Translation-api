# Translation Management Service API

A high-performance Laravel-based Translation Management Service API designed for scalability and performance. This service provides comprehensive translation management capabilities with support for multiple locales, tagging system, and efficient data handling for over 100k records.

## Features

-Multi-locale Suppor: Built-in support for English (en), French (fr), and Spanish (es) with easy extensibility
-Tagging Syste: Context-aware tagging for translations (mobile, desktop, web, admin, user, etc.)
-High Performanc: Optimized for handling 100k+ records with sub-200ms response times
-Export Functionalit: Fast JSON export for frontend applications (Vue.js, React, etc.)
-Token-based Authenticatio: Secure API access using Laravel Sanctum
-Comprehensive AP: Full CRUD operations with search and filtering capabilities
-Performance Monitorin: Built-in performance tracking and validation
-Scalable Architectur: Follows SOLID principles with service layer and repository pattern

## Performance Requirements

-CRUD Operation: < 200ms response time
-Export Endpoin: < 500ms response time for 100k+ records
-Search Operation: < 200ms response time
-Database Querie: Optimized to avoid N+1 problems

## Technology Stack

-Framewor: Laravel 11.x
-Databas: MySQL/PostgreSQL with optimized queries
-Authenticatio: Laravel Sanctum
-Cachin: Redis/Memcached for performance optimization
-Testin: PHPUnit with comprehensive test coverage
-Code Qualit: Laravel Pint (PSR-12 compliance)

## Installation

### Prerequisites

- PHP 8.2+
- Composer
- MySQL 8.0+ or PostgreSQL 13+
- Redis (optional, for caching)

### Setup Instructions

1.Clone the repositor
   ```bash
   git clone <repository-url>
   cd translation-api
   ```

2.Install dependencie
   ```bash
   composer install
   ```

3.Environment configuratio
   ```bash
   cp .env.example .env
   ```
   
   Update `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=translation_api
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   
   SUPPORTED_LOCALES=en,fr,es
   ```

4.Generate application ke
   ```bash
   php artisan key:generate
   ```

5.Run database migration
   ```bash
   php artisan migrate
   ```

6.Seed the database (optional
   ```bash
   php artisan db:seed
   ```

7.Start the development serve
   ```bash
   php artisan serve
   ```

## Database Population

### Using the Custom Command

Populate the database with sample data for scalability testing:

```bash
# Create 100,000 translation records (default)
php artisan translations:populate

# Create custom number of records
php artisan translations:populate 50000

# Customize chunk size for better performance
php artisan translations:populate 100000 --chunk=500
```

### Using Factories

```bash
# Create sample translations
php artisan tinker
>>> \App\Models\Translation::factory()->count(1000)->create();

# Create translations with specific locale
>>> \App\Models\Translation::factory()->english()->count(500)->create();
>>> \App\Models\Translation::factory()->french()->count(500)->create();
```

## API Endpoints

### Authentication

All endpoints (except login) require authentication using Bearer tokens.

```bash
POST /api/login
```

### Translation Management

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/translations` | Create a new translation |
| PUT | `/api/translations/{id}` | Update an existing translation |
| GET | `/api/translations/{id}` | Get a specific translation |
| GET | `/api/translations/search` | Search translations by tag, key, or content |
| GET | `/api/translations/export/{locale}` | Export all translations for a locale |
| GET | `/api/translations/stats` | Get translation statistics |
| GET | `/api/translations/locales` | Get supported locales |

### Request Examples

#### Create Translation
```bash
curl -X POST /api/translations \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "locale": "en",
    "key": "welcome.message",
    "content": "Welcome to our application",
    "tags": ["web", "user"]
  }'
```

#### Search Translations
```bash
curl -X GET "/api/translations/search?tag=mobile&locale=en" \
  -H "Authorization: Bearer {token}"
```

#### Export Translations
```bash
curl -X GET /api/translations/export/en \
  -H "Authorization: Bearer {token}"
```

## Architecture Design

### SOLID Principles Implementation

1.Single Responsibility Principl: Each class has a single, well-defined responsibility
   - `TranslationService`: Business logic for translations
   - `TranslationRepository`: Data access operations
   - `TranslationController`: HTTP request handling

2.Open/Closed Principl: Extensible without modification
   - Service layer allows easy addition of new business logic
   - Repository pattern enables different data sources

3.Liskov Substitution Principl: Implemented through interfaces and abstract classes
   - Repository pattern allows different implementations

4.Interface Segregation Principl: Clients depend only on methods they use
   - Specific request classes for different operations

5.Dependency Inversion Principl: High-level modules don't depend on low-level modules
   - Controllers depend on service interfaces, not concrete implementations

### Performance Optimizations

1.Database Query Optimizatio
   - Eager loading to prevent N+1 queries
   - Selective field retrieval for export operations
   - Chunked inserts for bulk operations

2.Caching Strateg
   - Redis caching for export data
   - Cache invalidation on data updates
   - Configurable cache TTL

3.Memory Managemen
   - Chunked processing for large datasets
   - Efficient data structures
   - Garbage collection optimization

### Scalability Features

1.Horizontal Scalin
   - Stateless API design
   - Database connection pooling
   - Load balancer ready

2.Vertical Scalin
   - Optimized database indexes
   - Efficient query patterns
   - Resource usage monitoring

3.Data Managemen
   - Partitioning strategies for large tables
   - Archive policies for old data
   - Backup and recovery procedures

## Testing

### Run Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/TranslationApiTest.php

# Run tests with coverage
php artisan test --coverage
```

### Test Coverage

-Feature Test: API endpoint testing with authentication
-Performance Test: Response time validation
-Validation Test: Request validation and error handling
-Integration Test: Service layer and repository testing

## Code Quality

### PSR-12 Compliance

```bash
# Check code style
./vendor/bin/pint --test

# Fix code style issues
./vendor/bin/pint
```

### Static Analysis

```bash
# Run PHPStan
./vendor/bin/phpstan analyse

# Run PHP CS Fixer
./vendor/bin/php-cs-fixer fix --dry-run
```

## Monitoring and Logging

### Performance Monitoring

The API includes built-in performance monitoring that:
- Tracks response times for all endpoints
- Logs performance metrics
- Validates performance requirements
- Provides performance headers in responses

### Logging

- API performance metrics
- Error logging with context
- User activity tracking
- Database query performance

## Deployment

### Production Considerations

1.Environment Variable
   - Secure database credentials
   - Production cache configuration
   - Log level settings

2.Performance Tunin
   - OPcache configuration
   - Database connection pooling
   - Redis persistence settings

3.Securit
   - HTTPS enforcement
   - Rate limiting
   - Input validation
   - SQL injection prevention

### Docker Support

```dockerfile
FROM php:8.2-fpm
# ... Docker configuration
```

## Contributing

1. Follow PSR-12 coding standards
2. Write comprehensive tests
3. Update documentation
4. Follow SOLID principles
5. Ensure performance requirements are met

