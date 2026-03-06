# API Endpoints Documentation

## Base URL

```
https://backend-test.test/api
```

## Authentication

Include the API token in the Authorization header:

```bash
curl -H "Authorization: Bearer {token}" \
     https://backend-test.test/api/me
```

**Postman:**
1. Go to the "Authorization" tab
2. Select "Bearer Token"
3. Paste your token

---

## Authentication (Public)

### Register User

```http
POST /api/register
```

Register a new user account.

**Request:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "phone": "+1234567890"
}
```

**Response:** `201 Created`
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "customer"
  },
  "token": "1|abc123xyz..."
}
```

### Login

```http
POST /api/login
```

Authenticate with email and password.

**Request:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:** `200 OK`
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "customer"
  },
  "token": "1|abc123xyz..."
}
```

---

## User (Authenticated)

### Get Current User

```http
GET /api/me
Authorization: Bearer {token}
```

Get authenticated user information.

**Response:** `200 OK`
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "role": "customer",
  "created_at": "2026-03-06T15:00:00Z",
  "updated_at": "2026-03-06T15:00:00Z"
}
```

### Logout

```http
POST /api/logout
Authorization: Bearer {token}
```

Invalidate the current API token.

**Response:** `200 OK`
```json
{
  "message": "Logged out"
}
```

---

## Events

### List Events

```http
GET /api/events?date=2026-06-15&search=rock&location=Madrid
```

List all events with optional filters. Results are paginated (10 per page).

**Query Parameters:**
- `date=YYYY-MM-DD` - Filter by exact date
- `search=string` - Search by event title
- `location=string` - Filter by location
- `page=number` - Pagination

**Response:** `200 OK`
```json
{
  "data": [
    {
      "id": 1,
      "title": "Summer Concert",
      "description": "Annual summer music festival",
      "date": "2026-06-15 18:00",
      "location": "Central Park",
      "organizer": "Jane Smith",
      "tickets": [
        {
          "id": 5,
          "type": "VIP",
          "price": "99.99",
          "quantity": 50
        }
      ],
      "created_at": "2026-03-06T15:00:00Z"
    }
  ],
  "links": { "first": "...", "last": "...", "next": "..." },
  "meta": { "current_page": 1, "last_page": 5, "total": 50 }
}
```

### Get Single Event

```http
GET /api/events/{id}
```

Get detailed information about a specific event including all ticket types.

**Response:** `200 OK`
```json
{
  "id": 1,
  "title": "Summer Concert",
  "description": "Annual summer music festival",
  "date": "2026-06-15 18:00",
  "location": "Central Park",
  "organizer": "Jane Smith",
  "tickets": [
    {
      "id": 5,
      "type": "VIP",
      "price": "99.99",
      "quantity": 50,
      "event_id": 1
    },
    {
      "id": 6,
      "type": "General",
      "price": "49.99",
      "quantity": 200,
      "event_id": 1
    }
  ],
  "created_at": "2026-03-06T15:00:00Z"
}
```

### Create Event

```http
POST /api/events
Authorization: Bearer {token}
Role: admin, organizer
```

Create a new event. Only admins and organizers can create events.

**Request:**
```json
{
  "title": "Summer Concert",
  "description": "Annual summer music festival",
  "date": "2026-06-15 18:00:00",
  "location": "Central Park"
}
```

**Response:** `201 Created`
```json
{
  "id": 1,
  "title": "Summer Concert",
  "description": "Annual summer music festival",
  "date": "2026-06-15 18:00",
  "location": "Central Park",
  "organizer": "Jane Smith",
  "tickets": [],
  "created_at": "2026-03-06T15:00:00Z"
}
```

### Update Event

```http
PUT /api/events/{id}
Authorization: Bearer {token}
Role: admin, organizer (owner)
```

Update an event. Organizers can only update their own events. Admins can update any event.

**Request:**
```json
{
  "title": "Summer Concert 2026",
  "date": "2026-06-20 19:00:00",
  "location": "Hyde Park"
}
```

**Response:** `200 OK`
```json
{
  "id": 1,
  "title": "Summer Concert 2026",
  "description": "Annual summer music festival",
  "date": "2026-06-20 19:00",
  "location": "Hyde Park",
  "organizer": "Jane Smith",
  "tickets": [...],
  "created_at": "2026-03-06T15:00:00Z"
}
```

### Delete Event

```http
DELETE /api/events/{id}
Authorization: Bearer {token}
Role: admin, organizer (owner)
```

Delete an event and all associated tickets and bookings.

**Response:** `200 OK`
```json
{
  "message": "Event deleted"
}
```

---

## Tickets

### Create Ticket

```http
POST /api/events/{event_id}/tickets
Authorization: Bearer {token}
Role: admin, organizer
```

Create a new ticket type for an event.

**Request:**
```json
{
  "type": "VIP",
  "price": 99.99,
  "quantity": 50
}
```

**Response:** `201 Created`
```json
{
  "id": 5,
  "type": "VIP",
  "price": "99.99",
  "quantity": 50,
  "event_id": 1
}
```

### Update Ticket

```http
PUT /api/tickets/{id}
Authorization: Bearer {token}
Role: admin, organizer
```

Update ticket type, price, or quantity.

**Request:**
```json
{
  "price": 109.99,
  "quantity": 75
}
```

**Response:** `200 OK`
```json
{
  "id": 5,
  "type": "VIP",
  "price": "109.99",
  "quantity": 75,
  "event_id": 1
}
```

### Delete Ticket

```http
DELETE /api/tickets/{id}
Authorization: Bearer {token}
Role: admin, organizer
```

Delete a ticket type. This cancels all pending bookings for this ticket.

**Response:** `200 OK`
```json
{
  "message": "Ticket deleted"
}
```

---

## Bookings

### Create Booking

```http
POST /api/tickets/{ticket_id}/bookings
Authorization: Bearer {token}
Role: customer
Middleware: prevent.double.booking
```

Create a new booking for a ticket. Prevents double-booking the same ticket.

**Request:**
```json
{
  "quantity": 2
}
```

**Response:** `201 Created`
```json
{
  "id": 10,
  "quantity": 2,
  "status": "pending",
  "ticket": {
    "id": 5,
    "type": "VIP",
    "price": "99.99",
    "quantity": 50,
    "event_id": 1
  },
  "payment": null,
  "created_at": "2026-03-06T15:30:00Z"
}
```

**Error Responses:**
- `409 Conflict` - User already has an active booking for this ticket
- `422 Unprocessable` - Not enough tickets available

### List Bookings

```http
GET /api/bookings
Authorization: Bearer {token}
Role: customer
```

List all bookings for the authenticated user (paginated, 10 per page).

**Response:** `200 OK`
```json
{
  "data": [
    {
      "id": 10,
      "quantity": 2,
      "status": "pending",
      "ticket": {
        "id": 5,
        "type": "VIP",
        "price": "99.99",
        "quantity": 50,
        "event_id": 1
      },
      "payment": null,
      "created_at": "2026-03-06T15:30:00Z"
    }
  ],
  "links": {...},
  "meta": {...}
}
```

### Cancel Booking

```http
PUT /api/bookings/{id}/cancel
Authorization: Bearer {token}
Role: customer (owner)
```

Cancel a booking. Can only cancel bookings you own.

**Response:** `200 OK`
```json
{
  "message": "Booking cancelled"
}
```

**Error Responses:**
- `403 Forbidden` - Booking belongs to another user
- `422 Unprocessable` - Booking is already cancelled

---

## Payments

### Process Payment

```http
POST /api/bookings/{booking_id}/payment
Authorization: Bearer {token}
```

Process payment for a booking. Changes booking status to `confirmed` on success.

**Request:**
```json
{}
```

**Response:** `201 Created`
```json
{
  "id": 1,
  "booking_id": 10,
  "amount": "199.98",
  "status": "success",
  "created_at": "2026-03-06T15:35:00Z",
  "updated_at": "2026-03-06T15:35:00Z"
}
```

**Payment Statuses:**
- `success` - Payment approved, booking confirmed
- `failed` - Payment declined, booking stays pending
- `refunded` - Payment refunded (future enhancement)

**Error Responses:**
- `403 Forbidden` - Booking belongs to another user
- `422 Unprocessable` - Booking is not in pending status

### Get Payment Details

```http
GET /api/payments/{id}
Authorization: Bearer {token}
```

Get detailed information about a payment.

**Response:** `200 OK`
```json
{
  "id": 1,
  "booking_id": 10,
  "amount": "199.98",
  "status": "success",
  "created_at": "2026-03-06T15:35:00Z",
  "updated_at": "2026-03-06T15:35:00Z"
}
```

---

## HTTP Status Codes

| Code | Meaning |
|------|---------|
| 200 | Success |
| 201 | Created |
| 401 | Unauthorized (invalid/missing token) |
| 403 | Forbidden (insufficient permissions) |
| 404 | Not found |
| 409 | Conflict (e.g., double booking attempt) |
| 422 | Unprocessable (validation error) |

## Error Response Format

All errors follow this format:

```json
{
  "message": "Error description"
}
```

Example:
```json
{
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required"]
  }
}
```
