# Guía de estilos – Arena Sport C.B

Objetivo: **diseño centrado, sin gradientes ni colores “tipo IA” (púrpura/índigo)**. Una sola paleta neutra y un acento para acciones positivas.

---

## Layouts

- **`layouts/app.blade.php`**: páginas con sesión. Incluye navbar y contenido centrado (`max-w-6xl mx-auto`).
- **`layouts/guest.blade.php`**: login y registro. Fondo gris claro, card centrada, sin navbar.

Todas las vistas internas (admin, cliente, POS) deben usar `@extends('layouts.app')` y `@section('content')` para tener navbar único y contenido centrado.

---

## Paleta

| Uso              | Clase / color                          |
|------------------|----------------------------------------|
| Fondo página     | `bg-slate-50`                          |
| Fondo navbar     | `bg-white` + `border-b border-slate-200`|
| Cards / paneles  | `bg-white border border-slate-200 rounded-lg shadow-sm` |
| Títulos          | `text-slate-800` o `text-2xl font-bold text-slate-800` |
| Texto secundario | `text-slate-600`                       |
| Texto muted      | `text-slate-500`                       |
| Botón primario   | `bg-slate-700 text-white ... hover:bg-slate-800` |
| Botón secundario | `border border-slate-300 text-slate-700 hover:bg-slate-50` |
| Acción positiva  | `bg-emerald-600 hover:bg-emerald-700` (solo cuando quieras destacar, ej. “Guardar”, “Confirmar”) |
| Estados (badges) | Confirmada: `bg-emerald-100 text-emerald-800`, Pendiente: `bg-amber-100 text-amber-800`, Cancelada: `bg-slate-100 text-slate-700` |
| Enlaces          | `text-slate-600 hover:text-slate-800`  |
| Inputs           | `border-slate-300 focus:ring-slate-400 focus:border-slate-400` |

Evitar: gradientes, `purple-*`, `indigo-*` (salvo que se decida un acento distinto más adelante).

---

## Navbar

El navbar está en **`partials/navbar.blade.php`** y se muestra según el rol:

- **Admin**: Dashboard, POS, Ventas, Reservas, Productos, Reportes (dropdown con exportar ventas/reservas).
- **Cajero**: POS.
- **Cliente**: Reservar cancha, Mis reservas.

No hace falta repetir navbar en cada vista si usan `layouts.app`.

---

## Contenido centrado

En el layout, el `<main>` ya lleva `max-w-6xl mx-auto px-4 sm:px-6 py-8`. El contenido debe ir dentro de `@section('content')` sin añadir otro `container` que duplique el ancho máximo.

---

## Vistas pendientes de migrar

Para que todo se vea igual:

1. Cambiar a `@extends('layouts.app')` y eliminar el `<html>`, `<head>`, navbar y wrapper duplicados.
2. Sustituir colores:
   - `gray-*` → `slate-*`
   - Botón primario → `bg-sky-600 hover:bg-sky-700`
   - `green-500/600` (éxito) → `emerald-600` / `emerald-700`
   - Quitar `bg-indigo-*`, `bg-purple-*` y gradientes.
3. Cards/tablas: usar `bg-white border border-slate-200 rounded-lg shadow-sm` y cabeceras `bg-slate-50`.

Archivos a actualizar cuando puedas:

- `admin/pos/index.blade.php`
- `admin/products/*` (index, create, edit, show)
- `admin/sales/*` (index, edit, show)
- `admin/reservations/create.blade.php`, `edit.blade.php`, `show.blade.php`
- `calendar/index.blade.php`
- `client/reservations.blade.php`
- `pos/simple.blade.php`

Si quieres, en el siguiente paso se puede ir vista por vista aplicando esta guía.
