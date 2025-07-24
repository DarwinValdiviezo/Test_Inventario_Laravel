# API de Facturas - Documentación

## Base URL
```
http://192.168.100.123:8000/api
```

## Autenticación

La API utiliza Laravel Sanctum para la autenticación. Todas las rutas protegidas requieren un token Bearer en el header `Authorization`.

### Login
```http
POST /api/auth/login
```

**Parámetros:**
- `email` (string, requerido): Email del usuario
- `password` (string, requerido): Contraseña del usuario
- `device_name` (string, requerido): Nombre del dispositivo (ej: "iPhone 15", "Android App")

**Ejemplo de respuesta exitosa:**
```json
{
  "success": true,
  "message": "Login exitoso",
  "data": {
    "user": {
      "id": 1,
      "name": "Juan Pérez",
      "email": "juan@ejemplo.com",
      "estado": "activo",
      "roles": ["Ventas"]
    },
    "token": "1|abc123...",
    "token_type": "Bearer"
  }
}
```

### Logout
```http
POST /api/auth/logout
```

**Headers requeridos:**
- `Authorization: Bearer {token}`

### Obtener información del usuario
```http
GET /api/auth/me
```

**Headers requeridos:**
- `Authorization: Bearer {token}`

### Cambiar contraseña
```http
POST /api/auth/cambiar-password
```

**Headers requeridos:**
- `Authorization: Bearer {token}`

**Parámetros:**
- `current_password` (string, requerido): Contraseña actual
- `new_password` (string, requerido): Nueva contraseña (mínimo 8 caracteres)
- `new_password_confirmation` (string, requerido): Confirmación de la nueva contraseña

## Facturas

### Obtener todas las facturas
```http
GET /api/facturas
```

**Headers requeridos:**
- `Authorization: Bearer {token}`

**Parámetros de consulta (opcionales):**
- `estado` (string): Filtrar por estado ('activa', 'anulada')
- `cliente_id` (integer): Filtrar por cliente
- `usuario_id` (integer): Filtrar por usuario que creó la factura
- `fecha_desde` (date): Fecha desde (YYYY-MM-DD)
- `fecha_hasta` (date): Fecha hasta (YYYY-MM-DD)
- `total_min` (decimal): Total mínimo
- `total_max` (decimal): Total máximo
- `orden` (string): Campo para ordenar (default: 'created_at')
- `direccion` (string): 'asc' o 'desc' (default: 'desc')
- `per_page` (integer): Facturas por página (default: 15)

**Ejemplo de respuesta:**
```json
{
  "success": true,
  "message": "Facturas obtenidas exitosamente",
  "data": [
    {
      "id": 1,
      "numero_secuencial": "001-001-000000001",
      "cua": "20241201-1728167857001-001001000000001",
      "subtotal": 1000.00,
      "iva": 150.00,
      "total": 1150.00,
      "estado": "activa",
      "estado_firma": "FIRMADA",
      "estado_emision": "EMITIDA",
      "forma_pago": "EFECTIVO",
      "fecha_emision": "2024-12-01",
      "fecha_firma": "2024-12-01 10:30:00",
      "fecha_emision_email": "2024-12-01 10:35:00",
      "cliente": {
        "id": 1,
        "nombre": "Cliente Ejemplo",
        "email": "cliente@ejemplo.com",
        "telefono": "0991234567"
      },
      "usuario": {
        "id": 1,
        "name": "Vendedor",
        "email": "vendedor@ejemplo.com"
      },
      "detalles_count": 3,
      "created_at": "2024-12-01 10:25:00",
      "updated_at": "2024-12-01 10:35:00"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 75,
    "from": 1,
    "to": 15
  }
}
```

### Obtener una factura específica
```http
GET /api/facturas/{id}
```

**Headers requeridos:**
- `Authorization: Bearer {token}`

**Ejemplo de respuesta:**
```json
{
  "success": true,
  "message": "Factura obtenida exitosamente",
  "data": {
    "id": 1,
    "numero_secuencial": "001-001-000000001",
    "cua": "20241201-1728167857001-001001000000001",
    "subtotal": 1000.00,
    "iva": 150.00,
    "total": 1150.00,
    "estado": "activa",
    "estado_firma": "FIRMADA",
    "estado_emision": "EMITIDA",
    "forma_pago": "EFECTIVO",
    "fecha_emision": "2024-12-01",
    "fecha_firma": "2024-12-01 10:30:00",
    "fecha_emision_email": "2024-12-01 10:35:00",
    "cliente": {
      "id": 1,
      "nombre": "Cliente Ejemplo",
      "email": "cliente@ejemplo.com",
      "telefono": "0991234567",
      "direccion": "Quito, Ecuador"
    },
    "usuario": {
      "id": 1,
      "name": "Vendedor",
      "email": "vendedor@ejemplo.com"
    },
    "detalles": [
      {
        "id": 1,
        "cantidad": 2,
        "precio_unitario": 500.00,
        "subtotal": 1000.00,
        "producto": {
          "id": 1,
          "nombre": "Laptop HP",
          "descripcion": "Laptop HP 15 pulgadas",
          "imagen_url": "http://192.168.100.123:8000/storage/productos/prod_123.jpg"
        }
      }
    ],
    "created_at": "2024-12-01 10:25:00",
    "updated_at": "2024-12-01 10:35:00"
  }
}
```

### Obtener clientes
```http
GET /api/clientes
```

**Headers requeridos:**
- `Authorization: Bearer {token}`

**Ejemplo de respuesta:**
```json
{
  "success": true,
  "message": "Clientes obtenidos exitosamente",
  "data": [
    {
      "id": 1,
      "nombre": "Cliente Ejemplo",
      "email": "cliente@ejemplo.com",
      "telefono": "0991234567",
      "direccion": "Quito, Ecuador",
      "facturas_count": 5
    }
  ]
}
```

### Obtener estadísticas de facturas
```http
GET /api/facturas/estadisticas
```

**Headers requeridos:**
- `Authorization: Bearer {token}`

**Ejemplo de respuesta:**
```json
{
  "success": true,
  "message": "Estadísticas obtenidas exitosamente",
  "data": {
    "total_facturas": 150,
    "facturas_activas": 140,
    "facturas_anuladas": 10,
    "total_ventas": 25000.50,
    "total_iva": 3750.08,
    "facturas_firmadas": 135,
    "facturas_emitidas": 130,
    "clientes_activos": 25,
    "facturas_por_mes": [
      {
        "mes": 1,
        "cantidad": 12,
        "total_ventas": 2000.00
      },
      {
        "mes": 2,
        "cantidad": 15,
        "total_ventas": 2500.00
      }
    ]
  }
}
```

## Productos (API adicional)

### Obtener todos los productos
```http
GET /api/productos
```

**Headers requeridos:**
- `Authorization: Bearer {token}`

**Parámetros de consulta (opcionales):**
- `buscar` (string): Buscar por nombre o descripción
- `categoria_id` (integer): Filtrar por categoría
- `stock_min` (integer): Stock mínimo
- `precio_min` (decimal): Precio mínimo
- `precio_max` (decimal): Precio máximo
- `orden` (string): Campo para ordenar (default: 'nombre')
- `direccion` (string): 'asc' o 'desc' (default: 'asc')
- `per_page` (integer): Productos por página (default: 15)

### Obtener un producto específico
```http
GET /api/productos/{id}
```

### Obtener categorías
```http
GET /api/categorias
```

### Obtener estadísticas de productos
```http
GET /api/productos/estadisticas
```

## Roles y Permisos

### Roles permitidos para la API:
- **Administrador**: Acceso completo a todas las funcionalidades
- **Ventas**: Puede ver facturas, clientes, productos y estadísticas
- **Bodega**: Puede ver productos, categorías y estadísticas
- **Secretario**: Puede ver productos, categorías y estadísticas

### Códigos de estado HTTP:
- `200`: Éxito
- `201`: Creado exitosamente
- `400`: Error en la solicitud
- `401`: No autorizado (token requerido o inválido)
- `403`: Prohibido (sin permisos)
- `404`: No encontrado
- `422`: Error de validación
- `500`: Error interno del servidor

## Ejemplos de uso

### Login con cURL:
```bash
curl -X POST http://192.168.100.123:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "vendedor@ejemplo.com",
    "password": "password123",
    "device_name": "Android App"
  }'
```

### Obtener facturas con cURL:
```bash
curl -X GET http://192.168.100.123:8000/api/facturas \
  -H "Authorization: Bearer 1|abc123..." \
  -H "Accept: application/json"
```

### Obtener facturas con filtros:
```bash
curl -X GET "http://192.168.100.123:8000/api/facturas?estado=activa&fecha_desde=2024-01-01&per_page=10" \
  -H "Authorization: Bearer 1|abc123..." \
  -H "Accept: application/json"
```

## Notas importantes

1. **Autenticación**: Todas las rutas protegidas requieren un token válido en el header `Authorization: Bearer {token}`

2. **Roles**: Solo usuarios con roles específicos pueden acceder a la API

3. **Paginación**: Las respuestas de listas incluyen información de paginación

4. **Imágenes**: Las URLs de imágenes son absolutas y apuntan al storage público

5. **Auditoría**: Todas las acciones se registran en la tabla de auditoría

6. **Errores**: Los errores incluyen mensajes descriptivos en español

## Configuración del cliente móvil

### Headers requeridos para todas las peticiones:
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {token}
```

### Manejo de errores:
```json
{
  "success": false,
  "message": "Descripción del error",
  "errors": {
    "campo": ["Error específico del campo"]
  }
}
```

### Refrescar token:
Si el token expira, el usuario debe hacer login nuevamente para obtener un nuevo token. 