# Event Booking System API

A Laravel 12 REST API for managing events, tickets, and bookings with role-based access control and payment processing.

## Tech Stack

- **Framework**: Laravel 12
- **Authentication**: Laravel Sanctum
- **Database**: SQLite (development) / MySQL (production)
- **Testing**: PHPUnit 11
- **Code Formatting**: Laravel Pint

## Prerequisites

- PHP 8.4+
- Composer
- Laravel Herd (or manual setup)

## Installation

### 1. Clone & Install Dependencies

```bash
cd C:\Users\luisj\Herd\backend_test
composer install
```

### 2. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Setup

```bash
# Run migrations and seed sample data
php artisan migrate:fresh --seed

# Or run migrations only (without seeders)
php artisan migrate
```

### 4. Seed Sample Data (Optional)

If you only ran migrations, populate the database with test data:

```bash
php artisan db:seed
```

The seeder creates:
- 1 admin user
- 3 organizer users
- 10 customer users
- 5 events
- 15 ticket types
- 20 sample bookings

### 5. Run the Application

The application is automatically served by Laravel Herd at:
```
https://backend-test.test
```

Or use the API directly:
```
https://backend-test.test/api
```

## Running Tests

Run all tests:
```bash
php artisan test --compact
```

Run specific test suite:
```bash
php artisan test --filter AuthTest      # Authentication tests
php artisan test --filter BookingTest   # Booking tests
php artisan test --testsuite=Unit      # Unit tests only
```

Test results: **17/17 passing**

## API Documentation

See [ENDPOINTS.md](./ENDPOINTS.md) for complete API endpoint documentation including:
- Authentication (register, login, logout)
- User endpoints (me)
- Events (CRUD + filters)
- Tickets (CRUD)
- Bookings (CRUD)
- Payments (process, get details)

Each endpoint includes:
- Request/response examples
- Query parameters
- Authentication requirements
- Error codes

## User Roles

The system has three user roles with different permissions:

### Admin
- Full access to all events and tickets
- Can manage any event or ticket
- Can view all bookings and payments

### Organizer
- Create and manage their own events
- Create and manage tickets for their events
- Cannot view other organizers' events
- Cannot make bookings (customers only)

### Customer
- View all public events and tickets
- Create and manage their own bookings
- Process payments for bookings
- Cancel their own bookings

## Business Logic

### Booking Flow

1. **Customer views events** → GET `/api/events`
2. **Customer creates booking** → POST `/api/tickets/{id}/bookings`
   - Status: `pending`
   - Middleware prevents double-booking same ticket
3. **Customer pays** → POST `/api/bookings/{id}/payment`
   - If successful: status changes to `confirmed`
   - Payment service simulates 80% success rate
4. **Customer cancels (optional)** → PUT `/api/bookings/{id}/cancel`
   - Status changes to `cancelled`

### Stock Management

- Each ticket has a `quantity` (total available)
- When creating a booking, system checks available stock
- Only `pending` and `confirmed` bookings count against stock
- Cancelled bookings free up stock

### Caching

- Event listings are cached for 5 minutes
- Cache is invalidated when events/tickets are created, updated, or deleted

## Project Structure

```
app/
├── Http/
│   ├── Controllers/       # API endpoints logic
│   ├── Middleware/        # Auth & role middleware
│   └── Resources/         # JSON response formatting
├── Models/                # Database models & relationships
└── Services/              # Business logic (e.g., PaymentService)

database/
├── migrations/            # Database schema
├── factories/             # Test data factories
└── seeders/               # Sample data seeders

routes/
└── api.php                # API route definitions

tests/
├── Feature/               # HTTP endpoint tests
└── Unit/                  # Unit tests
```

## Development Tips

### Clear Cache
```bash
php artisan cache:clear
```

### Reset Database
```bash
php artisan migrate:fresh --seed
```

### Run Code Formatter
```bash
php artisan pint
```

### Access Database CLI
```bash
php artisan tinker
```

## License

MIT
