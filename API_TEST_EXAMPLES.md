# Ejemplos de Prueba de la API de Facturas

## 1. Login y Obtención de Token

### Login como usuario de Ventas:
```bash
curl -X POST http://192.168.100.123:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "vendedor@ejemplo.com",
    "password": "password123",
    "device_name": "Android App Test"
  }'
```

**Respuesta esperada:**
```json
{
  "success": true,
  "message": "Login exitoso",
  "data": {
    "user": {
      "id": 2,
      "name": "Usuario Ventas",
      "email": "vendedor@ejemplo.com",
      "estado": "activo",
      "roles": ["Ventas"]
    },
    "token": "2|abc123def456...",
    "token_type": "Bearer"
  }
}
```

## 2. Obtener Facturas

### Obtener todas las facturas:
```bash
curl -X GET http://192.168.100.123:8000/api/facturas \
  -H "Authorization: Bearer 2|abc123def456..." \
  -H "Accept: application/json"
```

### Obtener facturas con filtros:
```bash
curl -X GET "http://192.168.100.123:8000/api/facturas?estado=activa&fecha_desde=2024-01-01&per_page=10" \
  -H "Authorization: Bearer 2|abc123def456..." \
  -H "Accept: application/json"
```

### Obtener facturas por cliente:
```bash
curl -X GET "http://192.168.100.123:8000/api/facturas?cliente_id=1&orden=total&direccion=desc" \
  -H "Authorization: Bearer 2|abc123def456..." \
  -H "Accept: application/json"
```

## 3. Obtener Factura Específica

```bash
curl -X GET http://192.168.100.123:8000/api/facturas/1 \
  -H "Authorization: Bearer 2|abc123def456..." \
  -H "Accept: application/json"
```

## 4. Obtener Clientes

```bash
curl -X GET http://192.168.100.123:8000/api/clientes \
  -H "Authorization: Bearer 2|abc123def456..." \
  -H "Accept: application/json"
```

## 5. Obtener Estadísticas de Facturas

```bash
curl -X GET http://192.168.100.123:8000/api/facturas/estadisticas \
  -H "Authorization: Bearer 2|abc123def456..." \
  -H "Accept: application/json"
```

## 6. Información del Usuario

```bash
curl -X GET http://192.168.100.123:8000/api/auth/me \
  -H "Authorization: Bearer 2|abc123def456..." \
  -H "Accept: application/json"
```

## 7. Cambiar Contraseña

```bash
curl -X POST http://192.168.100.123:8000/api/auth/cambiar-password \
  -H "Authorization: Bearer 2|abc123def456..." \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "current_password": "password123",
    "new_password": "nuevaPassword456",
    "new_password_confirmation": "nuevaPassword456"
  }'
```

## 8. Logout

```bash
curl -X POST http://192.168.100.123:8000/api/auth/logout \
  -H "Authorization: Bearer 2|abc123def456..." \
  -H "Accept: application/json"
```

## Ejemplos con JavaScript/Fetch

### Login:
```javascript
const loginData = {
  email: 'vendedor@ejemplo.com',
  password: 'password123',
  device_name: 'Web App'
};

fetch('http://192.168.100.123:8000/api/auth/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify(loginData)
})
.then(response => response.json())
.then(data => {
  if (data.success) {
    // Guardar el token
    localStorage.setItem('api_token', data.data.token);
    console.log('Login exitoso:', data.data.user);
  } else {
    console.error('Error de login:', data.message);
  }
})
.catch(error => console.error('Error:', error));
```

### Obtener facturas:
```javascript
const token = localStorage.getItem('api_token');

fetch('http://192.168.100.123:8000/api/facturas?per_page=20', {
  method: 'GET',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  }
})
.then(response => response.json())
.then(data => {
  if (data.success) {
    console.log('Facturas:', data.data);
    console.log('Paginación:', data.pagination);
  } else {
    console.error('Error:', data.message);
  }
})
.catch(error => console.error('Error:', error));
```

### Obtener factura específica:
```javascript
const token = localStorage.getItem('api_token');
const facturaId = 1;

fetch(`http://192.168.100.123:8000/api/facturas/${facturaId}`, {
  method: 'GET',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  }
})
.then(response => response.json())
.then(data => {
  if (data.success) {
    console.log('Factura:', data.data);
    console.log('Detalles:', data.data.detalles);
  } else {
    console.error('Error:', data.message);
  }
})
.catch(error => console.error('Error:', error));
```

### Obtener estadísticas:
```javascript
const token = localStorage.getItem('api_token');

fetch('http://192.168.100.123:8000/api/facturas/estadisticas', {
  method: 'GET',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  }
})
.then(response => response.json())
.then(data => {
  if (data.success) {
    console.log('Total facturas:', data.data.total_facturas);
    console.log('Total ventas:', data.data.total_ventas);
    console.log('Facturas por mes:', data.data.facturas_por_mes);
  } else {
    console.error('Error:', data.message);
  }
})
.catch(error => console.error('Error:', error));
```

## Ejemplos con Postman

### 1. Login
- **Method**: POST
- **URL**: `http://192.168.100.123:8000/api/auth/login`
- **Headers**:
  - `Content-Type: application/json`
  - `Accept: application/json`
- **Body** (raw JSON):
```json
{
  "email": "vendedor@ejemplo.com",
  "password": "password123",
  "device_name": "Postman Test"
}
```

### 2. Obtener Facturas
- **Method**: GET
- **URL**: `http://192.168.100.123:8000/api/facturas`
- **Headers**:
  - `Authorization: Bearer {token}`
  - `Accept: application/json`

### 3. Obtener Facturas con Filtros
- **Method**: GET
- **URL**: `http://192.168.100.123:8000/api/facturas?estado=activa&fecha_desde=2024-01-01&per_page=10`
- **Headers**:
  - `Authorization: Bearer {token}`
  - `Accept: application/json`

### 4. Obtener Clientes
- **Method**: GET
- **URL**: `http://192.168.100.123:8000/api/clientes`
- **Headers**:
  - `Authorization: Bearer {token}`
  - `Accept: application/json`

### 5. Obtener Estadísticas
- **Method**: GET
- **URL**: `http://192.168.100.123:8000/api/facturas/estadisticas`
- **Headers**:
  - `Authorization: Bearer {token}`
  - `Accept: application/json`

## Manejo de Errores

### Error de autenticación (401):
```json
{
  "success": false,
  "message": "No autorizado. Token requerido."
}
```

### Error de permisos (403):
```json
{
  "success": false,
  "message": "No tienes permisos para ver facturas."
}
```

### Error de validación (422):
```json
{
  "success": false,
  "message": "Datos de entrada inválidos",
  "errors": {
    "email": ["El email es obligatorio"],
    "password": ["La contraseña es obligatoria"]
  }
}
```

### Error de servidor (500):
```json
{
  "success": false,
  "message": "Error interno del servidor",
  "error": "Detalles del error (solo en modo debug)"
}
```

## Notas para Pruebas

1. **Reemplazar el token**: En todos los ejemplos, reemplaza `{token}` con el token real obtenido del login.

2. **Base URL**: Cambia `http://192.168.100.123:8000` por tu URL real.

3. **Credenciales**: Usa credenciales reales de usuarios existentes en tu base de datos.

4. **Roles**: Asegúrate de que el usuario tenga uno de los roles permitidos: Administrador o Ventas.

5. **Estado del usuario**: El usuario debe estar en estado "activo".

6. **Facturas**: Asegúrate de tener facturas creadas en la base de datos para ver resultados.

## Script de Prueba Completo

```bash
#!/bin/bash

# Variables
BASE_URL="http://192.168.100.123:8000/api"
EMAIL="vendedor@ejemplo.com"
PASSWORD="password123"
DEVICE_NAME="Test Script"

echo "=== Prueba de API de Facturas ==="

# 1. Login
echo "1. Haciendo login..."
LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/auth/login" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{
    \"email\": \"$EMAIL\",
    \"password\": \"$PASSWORD\",
    \"device_name\": \"$DEVICE_NAME\"
  }")

echo "Respuesta de login: $LOGIN_RESPONSE"

# Extraer token
TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"token":"[^"]*"' | cut -d'"' -f4)

if [ -z "$TOKEN" ]; then
    echo "Error: No se pudo obtener el token"
    exit 1
fi

echo "Token obtenido: $TOKEN"

# 2. Obtener información del usuario
echo "2. Obteniendo información del usuario..."
curl -s -X GET "$BASE_URL/auth/me" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq '.'

# 3. Obtener facturas
echo "3. Obteniendo facturas..."
curl -s -X GET "$BASE_URL/facturas?per_page=5" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq '.'

# 4. Obtener clientes
echo "4. Obteniendo clientes..."
curl -s -X GET "$BASE_URL/clientes" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq '.'

# 5. Obtener estadísticas
echo "5. Obteniendo estadísticas..."
curl -s -X GET "$BASE_URL/facturas/estadisticas" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq '.'

# 6. Logout
echo "6. Haciendo logout..."
curl -s -X POST "$BASE_URL/auth/logout" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq '.'

echo "=== Prueba completada ==="
```

Para ejecutar este script:
```bash
chmod +x test_api_facturas.sh
./test_api_facturas.sh
```

## API de Productos (Adicional)

Si también necesitas probar la API de productos:

### Obtener productos:
```bash
curl -X GET http://192.168.100.123:8000/api/productos \
  -H "Authorization: Bearer 2|abc123def456..." \
  -H "Accept: application/json"
```

### Obtener categorías:
```bash
curl -X GET http://192.168.100.123:8000/api/categorias \
  -H "Authorization: Bearer 2|abc123def456..." \
  -H "Accept: application/json"
```

### Obtener estadísticas de productos:
```bash
curl -X GET http://192.168.100.123:8000/api/productos/estadisticas \
  -H "Authorization: Bearer 2|abc123def456..." \
  -H "Accept: application/json"
``` 