# Documentación de la API - Inventario Laravel

## Base URL

```
http://10.40.25.245:8000/api
```

---

## Autenticación

La API utiliza autenticación con tokens vía Sanctum. Debes iniciar sesión para obtener un token y enviarlo en el header `Authorization` en las rutas protegidas.

### 1. Login

- **POST** `/auth/login`
- **Body (JSON):**
```json
{
  "email": "usuario@ejemplo.com",
  "password": "tu_password"
}
```
- **Respuesta exitosa:**
```json
{
  "token": "TOKEN_AQUI",
  "user": { ... }
}
```

### 2. Usar el token

En Thunder Client/Postman, agrega el header:
```
Authorization: Bearer TOKEN_AQUI
```

### 3. Logout
- **POST** `/auth/logout`

---

## Endpoints principales

### Usuarios (solo Administrador)

- **GET** `/users` — Listar usuarios
- **POST** `/users` — Crear usuario
- **GET** `/users/{id}` — Ver usuario
- **PUT** `/users/{id}` — Actualizar usuario
- **DELETE** `/users/{id}` — Eliminar usuario (soft delete)
- **POST** `/users/{id}/restore` — Restaurar usuario
- **DELETE** `/users/{id}/force-delete` — Eliminar usuario permanentemente
- **POST** `/users/{user}/toggle-estado` — Activar/desactivar usuario
- **POST** `/users/crear-token` — Crear token de acceso manual

### Clientes (Administrador y Secretario)

- **GET** `/clientes` — Listar clientes
- **POST** `/clientes` — Crear cliente
- **GET** `/clientes/{id}` — Ver cliente
- **PUT** `/clientes/{id}` — Actualizar cliente
- **DELETE** `/clientes/{id}` — Eliminar cliente (soft delete)
- **POST** `/clientes/{id}/restore` — Restaurar cliente
- **DELETE** `/clientes/{id}/force-delete` — Eliminar cliente permanentemente

### Productos (Administrador y Bodega)

- **GET** `/productos` — Listar productos
- **POST** `/productos` — Crear producto
- **GET** `/productos/{id}` — Ver producto
- **PUT** `/productos/{id}` — Actualizar producto
- **DELETE** `/productos/{id}` — Eliminar producto (soft delete)
- **POST** `/productos/{id}/restore` — Restaurar producto
- **POST** `/productos/{id}/forceDelete` — Eliminar producto permanentemente
- **GET** `/productos/export/{type}` — Exportar productos (type: xlsx, csv, etc.)
- **GET** `/productos/reporte` — Reporte de productos
- **GET** `/productos/estadisticas` — Estadísticas de productos
- **GET** `/categorias` — Listar categorías

### Facturas (Administrador y Ventas)

- **GET** `/facturas` — Listar facturas
- **POST** `/facturas` — Crear factura
- **GET** `/facturas/{id}` — Ver factura
- **PUT** `/facturas/{id}` — Actualizar factura
- **DELETE** `/facturas/{id}` — Eliminar factura (soft delete)
- **POST** `/facturas/{id}/restore` — Restaurar factura
- **POST** `/facturas/{id}/force-delete` — Eliminar factura permanentemente
- **GET** `/facturas/{id}/pdf` — Descargar PDF
- **POST** `/facturas/{id}/send-email` — Enviar factura por email
- **POST** `/facturas/{id}/firmar` — Firmar factura
- **POST** `/facturas/{id}/emitir` — Emitir factura
- **GET** `/facturas/{id}/estado` — Estado de la factura
- **GET** `/facturas/estadisticas` — Estadísticas de facturas
- **GET** `/clientes` — Listar clientes para facturación

### Auditoría (solo Administrador)

- **GET** `/auditorias` — Listar auditorías
- **GET** `/auditorias/export` — Exportar auditorías

### Roles (solo Administrador)

- **GET** `/roles` — Listar roles
- **POST** `/roles` — Crear rol
- **DELETE** `/roles/{id}` — Eliminar rol

---

## Ejemplo de uso en Thunder Client/Postman

1. Haz una petición POST a `/auth/login` con tu email y contraseña.
2. Copia el token de la respuesta.
3. En cada petición protegida, añade el header:
   - `Authorization: Bearer TU_TOKEN`
4. Prueba los endpoints según tu rol.

---

## Notas sobre roles y permisos
- **Administrador:** Acceso total a todos los endpoints.
- **Secretario:** Acceso a clientes.
- **Bodega:** Acceso a productos.
- **Ventas:** Acceso a facturas.

Si intentas acceder a un endpoint sin el rol adecuado, recibirás un error 403.

---

## Ejemplo de petición protegida (Thunder Client/Postman)

**GET** `/api/users`
- Header:
  - `Authorization: Bearer TU_TOKEN`

**Respuesta:**
```json
[
  {
    "id": 1,
    "name": "Admin",
    "email": "admin@ejemplo.com",
    ...
  },
  ...
]
```

---

## Consejos
- Usa la URL base: `http://10.40.25.245:8000/api`
- Siempre inicia sesión primero para obtener el token.
- Usa el token en todas las rutas protegidas.
- Si tienes dudas sobre los parámetros, revisa el código de los controladores o pregunta por aquí.

---

¿Dudas? ¡Pregunta! 🎉 