# Diagrama de Entidad-Relación (ERD) - Sistema de Reservas de Canchas de Voleibol

## Tablas Principales

### users
- id (PK)
- nombre
- email (unique)
- password_hash
- rol_id (FK -> roles.id)
- telefono (nullable)
- email_verified_at (nullable)
- remember_token
- created_at
- updated_at

### roles
- id (PK)
- nombre
- created_at
- updated_at

### courts
- id (PK)
- nombre
- descripcion (nullable)
- precio_por_hora (decimal)
- estado (enum: activo, inactivo)
- created_at
- updated_at

### reservations
- id (PK)
- user_id (FK -> users.id)
- court_id (FK -> courts.id)
- fecha (date)
- hora_inicio (time)
- duracion_horas (int)
- estado (enum: pendiente, confirmada, cancelada)
- total_estimado (decimal, nullable)
- pagado_bool (boolean, default false)
- created_at
- updated_at

### reservation_items
- id (PK)
- reservation_id (FK -> reservations.id, onDelete cascade)
- descripcion
- cantidad (int)
- precio_unitario (decimal)
- created_at
- updated_at

### products
- id (PK)
- nombre
- categoria
- precio (decimal)
- stock (int)
- created_at
- updated_at

### orders
- id (PK)
- user_id (FK -> users.id)
- reservation_id (FK -> reservations.id, nullable)
- total (decimal)
- estado_pago (enum: pendiente, pagado, cancelado)
- created_at
- updated_at

### order_items
- id (PK)
- order_id (FK -> orders.id, onDelete cascade)
- product_id (FK -> products.id)
- cantidad (int)
- precio_unitario (decimal)
- created_at
- updated_at

### payments
- id (PK)
- order_id (FK -> orders.id, onDelete cascade)
- amount (decimal)
- metodo (string)
- referencia (string, nullable)
- fecha (datetime)
- created_at
- updated_at

### audit_logs
- id (PK)
- usuario (string)
- accion (string)
- detalle (text)
- timestamp (datetime)
- created_at
- updated_at

## Relaciones

- users -> roles (many-to-one)
- reservations -> users (many-to-one)
- reservations -> courts (many-to-one)
- reservation_items -> reservations (many-to-one)
- orders -> users (many-to-one)
- orders -> reservations (many-to-one, nullable)
- order_items -> orders (many-to-one)
- order_items -> products (many-to-one)
- payments -> orders (many-to-one)

## Notas
- Todas las tablas tienen timestamps (created_at, updated_at)
- Las claves foráneas tienen restricciones de integridad referencial
- Algunos campos permiten null según el modelo de negocio