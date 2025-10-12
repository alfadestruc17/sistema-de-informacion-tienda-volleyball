# Documentación de la API - Sistema de Reservas de Canchas de Voleibol

## Base URL
`http://localhost:8000/api`

## Autenticación
La API utiliza autenticación basada en sesiones. Los endpoints protegidos requieren que el usuario esté autenticado.

## Endpoints

### Autenticación

#### POST /api/auth/register
Registra un nuevo usuario.

**Request Body:**
```json
{
  "nombre": "Juan Pérez",
  "email": "juan@example.com",
  "password": "password123",
  "telefono": "3001234567",
  "rol_id": 3
}
```

**Response (201):**
```json
{
  "user": {
    "id": 1,
    "nombre": "Juan Pérez",
    "email": "juan@example.com",
    "rol_id": 3,
    "telefono": "3001234567",
    "created_at": "2025-10-03T14:00:00.000000Z"
  },
  "message": "Usuario registrado"
}
```

#### POST /api/auth/login
Inicia sesión de usuario.

**Request Body:**
```json
{
  "email": "juan@example.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "user": {
    "id": 1,
    "nombre": "Juan Pérez",
    "email": "juan@example.com",
    "rol_id": 3
  },
  "message": "Login exitoso"
}
```

### Canchas

#### GET /api/courts
Obtiene todas las canchas. (Requiere rol: admin)

**Response (200):**
```json
[
  {
    "id": 1,
    "nombre": "Cancha 1",
    "descripcion": "Cancha principal",
    "precio_por_hora": 25.00,
    "estado": "activo",
    "created_at": "2025-10-03T14:00:00.000000Z"
  }
]
```

#### POST /api/courts
Crea una nueva cancha. (Requiere rol: admin)

**Request Body:**
```json
{
  "nombre": "Cancha 2",
  "descripcion": "Cancha secundaria",
  "precio_por_hora": 20.00,
  "estado": "activo"
}
```

**Response (201):**
```json
{
  "id": 2,
  "nombre": "Cancha 2",
  "descripcion": "Cancha secundaria",
  "precio_por_hora": 20.00,
  "estado": "activo",
  "created_at": "2025-10-03T14:00:00.000000Z",
  "updated_at": "2025-10-03T14:00:00.000000Z"
}
```

#### GET /api/courts/{id}
Obtiene una cancha específica. (Requiere rol: admin)

#### PUT/PATCH /api/courts/{id}
Actualiza una cancha. (Requiere rol: admin)

#### DELETE /api/courts/{id}
Elimina una cancha. (Requiere rol: admin)

### Reservas

#### GET /api/reservations
Obtiene todas las reservas del usuario autenticado o todas si es admin.

#### POST /api/reservations
Crea una nueva reserva.

**Request Body:**
```json
{
  "court_id": 1,
  "fecha": "2025-10-05",
  "hora_inicio": "14:00",
  "duracion_horas": 2,
  "user_id": 1
}
```

#### GET /api/reservations/{id}
Obtiene detalles de una reserva específica.

#### PATCH /api/reservations/{id}
Actualiza una reserva (cambiar estado, etc.).

#### DELETE /api/reservations/{id}
Cancela una reserva.

### Disponibilidad

#### GET /api/availability
Obtiene disponibilidad de canchas por fecha.

**Query Parameters:**
- court_id (opcional)
- date_from
- date_to

**Response (200):**
```json
{
  "court_id": 1,
  "available_slots": [
    {
      "date": "2025-10-05",
      "start_time": "08:00",
      "end_time": "10:00"
    }
  ],
  "occupied_slots": [
    {
      "date": "2025-10-05",
      "start_time": "14:00",
      "end_time": "16:00",
      "reservation_id": 1
    }
  ]
}
```

### Órdenes (Ventas)

#### GET /api/orders
Obtiene órdenes del usuario o todas si es admin/cajero.

#### POST /api/orders
Crea una nueva orden/venta.

**Request Body:**
```json
{
  "user_id": 1,
  "reservation_id": 1,
  "estado_pago": "pendiente"
}
```

#### POST /api/orders/{id}/items
Agrega un item a una orden.

**Request Body:**
```json
{
  "product_id": 1,
  "cantidad": 2,
  "precio_unitario": 3.50
}
```

#### PATCH /api/orders/{id}/close
Cierra la orden y marca como pagada.

### Productos

#### GET /api/products
Obtiene todos los productos disponibles.

**Response (200):**
```json
[
  {
    "id": 1,
    "nombre": "Coca Cola 350ml",
    "categoria": "Bebidas",
    "precio": 3.50,
    "stock": 45,
    "created_at": "2025-10-03T14:00:00.000000Z",
    "updated_at": "2025-10-03T14:00:00.000000Z"
  }
]
```

#### POST /api/products
Crea un nuevo producto. (Requiere rol: admin)

**Request Body:**
```json
{
  "nombre": "Coca Cola 350ml",
  "categoria": "Bebidas",
  "precio": 3.50,
  "stock": 50
}
```

**Response (201):**
```json
{
  "id": 1,
  "nombre": "Coca Cola 350ml",
  "categoria": "Bebidas",
  "precio": 3.50,
  "stock": 50,
  "created_at": "2025-10-03T14:00:00.000000Z",
  "updated_at": "2025-10-03T14:00:00.000000Z"
}
```

#### GET /api/products/{id}
Obtiene un producto específico.

#### PUT/PATCH /api/products/{id}
Actualiza un producto. (Requiere rol: admin)

**Request Body:**
```json
{
  "nombre": "Coca Cola 350ml",
  "categoria": "Bebidas",
  "precio": 4.00,
  "stock": 45
}
```

#### DELETE /api/products/{id}
Elimina un producto. (Requiere rol: admin)

### Gestión de Stock

#### Validaciones de Stock
- Los productos deben tener stock > 0 para ser vendidos
- El sistema valida stock disponible antes de procesar ventas
- Si no hay stock suficiente, la venta es rechazada con mensaje de error
- El stock se reduce automáticamente al procesar ventas exitosas
- Los administradores pueden actualizar stock desde la interfaz web

#### Estados de Stock
- **Disponible**: stock > 10 (verde)
- **Stock Bajo**: stock 1-10 (amarillo)
- **Agotado**: stock = 0 (rojo)

### Productos

#### GET /api/products
Obtiene todos los productos.

#### POST /api/products
Crea un nuevo producto. (Requiere rol: admin)

#### PUT /api/products/{id}
Actualiza un producto. (Requiere rol: admin)

#### DELETE /api/products/{id}
Elimina un producto. (Requiere rol: admin)

### Dashboard

#### GET /api/dashboard/weekly
Obtiene datos del dashboard semanal. (Requiere rol: admin)

**Query Parameters:**
- week_start (YYYY-MM-DD)

**Response (200):**
```json
{
  "week_start": "2025-10-01",
  "occupancy": {
    "monday": [
      {"court_id": 1, "start_time": "14:00", "end_time": "16:00", "status": "occupied"}
    ]
  },
  "reservations_today": [
    {
      "id": 1,
      "court_name": "Cancha 1",
      "user_name": "Juan Pérez",
      "start_time": "14:00",
      "end_time": "16:00"
    }
  ],
  "sales_today": 125.50,
  "active_reservations": 5
}
```

### Reportes

#### GET /api/reports/sales
Obtiene reporte de ventas. (Requiere rol: admin)

**Query Parameters:**
- from (YYYY-MM-DD)
- to (YYYY-MM-DD)

**Response (200):**
```json
{
  "period": {
    "from": "2025-10-01",
    "to": "2025-10-31"
  },
  "total_sales": 2500.00,
  "total_reservations": 45,
  "top_products": [
    {"name": "Coca Cola", "quantity": 120, "revenue": 420.00}
  ],
  "court_utilization": [
    {"court_name": "Cancha 1", "hours_used": 180, "revenue": 4500.00}
  ]
}
```

## Códigos de Estado HTTP

- 200: OK
- 201: Created
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 422: Unprocessable Entity
- 500: Internal Server Error

## Manejo de Errores

Los errores se devuelven en el siguiente formato:

```json
{
  "message": "Descripción del error",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

## Notas

- Todos los endpoints requieren autenticación excepto `/api/auth/register` y `/api/auth/login`
- Los endpoints marcados con roles específicos requieren middleware de autorización
- Las fechas están en formato YYYY-MM-DD
- Las horas están en formato HH:MM
- Los precios están en la moneda local (COP)