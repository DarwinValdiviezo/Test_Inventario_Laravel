# Sistema de Inventario Laravel 11+ (con Spatie Roles y Permisos)

## 🚀 Sistema Completo de Gestión de Inventario y Facturación

Sistema profesional de inventario con módulos de Clientes, Productos, Facturación, Auditoría, gestión de roles y usuarios. Incluye funcionalidades avanzadas como generación de PDF, envío de emails, control de stock, soft deletes, auditoría completa, gestión de estados de usuario y seguridad en tiempo real.

## ✨ Características Principales

### 🔐 **Sistema de Autenticación y Roles**
- Autenticación con Laravel Breeze
- Gestión de roles con Spatie Laravel Permission
- Roles: Administrador, Secretario, Bodega, Ventas, Cliente
- Middleware de autorización por roles
- Contraseñas de aplicación para acciones críticas
- **Gestión completa de usuarios** con estados avanzados
- **Sincronización bidireccional** cliente-usuario
- **Perfil de usuario profesional** con gestión de contraseñas
- **Ojos para ver contraseñas** en formularios de cambio

### 👥 **Módulo de Clientes**
- CRUD completo con validaciones avanzadas
- Creación sincronizada con tabla de usuarios
- Asignación automática de rol "cliente"
- Soft deletes con restauración y eliminación definitiva
- Auditoría completa de todas las acciones
- Filtros avanzados y búsqueda
- Validación de cambios reales antes de actualizar
- **Sincronización automática** de estado con usuario
- **Acceso al sistema** para clientes
- **Estados dinámicos**: Activo, Inactivo, Eliminado

### 👤 **Módulo de Gestión de Usuarios**
- CRUD completo de usuarios del sistema
- **Estados avanzados**: Activo, Inactivo, Pendiente de eliminación, Eliminado
- Activación/desactivación de usuarios
- Soft deletes con restauración y eliminación definitiva
- **"Borrar mi cuenta"** con periodo de gracia configurable
- **Cancelación de eliminación** antes del plazo
- **Sincronización automática** con clientes
- **Auditoría completa** de cambios de estado
- **Filtros por estado** y búsqueda avanzada
- **Panel de reportes** para usuarios eliminados/restaurados

### 🎭 **Módulo de Gestión de Roles**
- Visualización de todos los roles del sistema
- Creación de nuevos roles
- Eliminación de roles (solo si no tienen usuarios)
- **Protección de roles críticos** (no se pueden eliminar)
- **Auditoría de cambios** en roles
- **Sincronización automática** con auditoría
- **Validación de contraseña** para acciones críticas

### 📦 **Módulo de Productos**
- CRUD completo con soporte para imágenes
- Control de stock automático
- Categorización de productos
- Filtros avanzados por categoría, stock, precio
- Soft deletes con restauración y eliminación definitiva
- Auditoría completa con filtros y paginación
- Validación de stock en tiempo real
- **Estados dinámicos**: Activo, Inactivo, Eliminado

### 🧾 **Módulo de Facturación**
- **Creación dinámica** de facturas con múltiples productos
- **Validación de stock** en tiempo real
- **Transacciones seguras** con rollback automático
- **Generación de PDF** profesional
- **Envío por email** con plantilla personalizada
- **Control de estado** (activa/anulada)
- **Reversión automática de stock** al anular
- **Auditoría completa** de todas las transacciones
- **Descarga de PDF** con método `downloadPDF()`
- **Vista previa de PDF** antes de crear factura

### 📊 **Sistema de Auditoría**
- Registro automático de todas las acciones CRUD
- Filtros avanzados por acción, usuario, fecha
- Paginación y búsqueda en logs
- Información detallada de cambios (old/new values)
- Observaciones personalizadas para acciones críticas
- **Auditoría de gestión** de roles y usuarios
- **Reportes de eliminación/restauración** por módulo

### 🛡️ **Sistema de Seguridad Avanzado**
- **Middleware de verificación de estado** de usuario
- **Control de acceso por estados** de cuenta
- **Redirección inteligente** al perfil para cuentas pendientes
- **Cálculo preciso de tiempo** restante para eliminación
- **Mensajes informativos** con días y horas exactos
- **Protección de rutas** basada en estado de usuario
- **Validación de contraseña** para acciones críticas
- **Logs de depuración** para seguimiento de problemas

### 🎨 **Interfaz de Usuario Profesional**
- Diseño moderno con AdminLTE 3
- Responsive design para móviles y tablets
- **Modales profesionales** para confirmaciones
- **Validación en tiempo real** en formularios
- Iconografía consistente con Bootstrap Icons
- **Animaciones suaves** y transiciones
- **Notificaciones automáticas** con auto-cierre
- **Tooltips informativos** en elementos interactivos
- **Estados visuales claros** para usuarios
- **Formularios con ojos** para ver contraseñas

## ⚙️ Configuración del Sistema

### 🔧 **Configuración de Días de Eliminación**

El sistema permite configurar el periodo de gracia para la eliminación de cuentas. Por defecto está configurado en **3 días**, pero puedes cambiarlo editando estos archivos:

#### **1. Middleware de Verificación de Estado**
**Archivo:** `app/Http/Middleware/CheckUserStatus.php` - Línea 26
```php
$fechaEliminacion = \Carbon\Carbon::parse($user->pending_delete_at)->addDays(3);
// Cambiar el número 3 por los días que desees
```

#### **2. Controlador de Autenticación**
**Archivo:** `app/Http/Controllers/Auth/AuthenticatedSessionController.php` - Línea 34
```php
$fechaEliminacion = \Carbon\Carbon::parse($user->pending_delete_at)->addDays(3);
// Cambiar el número 3 por los días que desees
```

#### **3. Vista del Perfil**
**Archivo:** `resources/views/profile/edit.blade.php` - Línea 109
```php
$fechaEliminacion = \Carbon\Carbon::parse($user->pending_delete_at)->addDays(3);
// Cambiar el número 3 por los días que desees
```

#### **4. Textos Informativos**
También debes actualizar los textos que mencionan "3 días":
- **Línea 267:** `Su cuenta se eliminará en 3 días`
- **Línea 289:** `antes de que se cumplan los 3 días`
- **Modal de eliminación:** Textos que mencionen el periodo

### 🎯 **Ejemplo de Configuración para 7 días:**
```php
// Cambiar todas las instancias de addDays(3) por:
$fechaEliminacion = \Carbon\Carbon::parse($user->pending_delete_at)->addDays(7);
```

## 📋 Requisitos
- PHP 8.2+
- Composer
- Node.js y npm (para assets)
- PostgreSQL o MySQL
- Extensión GD para PHP (para imágenes)

## 🛠️ Instalación y Configuración

### 1. Crear el proyecto Laravel
```bash
composer create-project laravel/laravel nombre-proyecto
cd nombre-proyecto
```

### 2. Instalar dependencias principales
```bash
composer require spatie/laravel-permission
composer require barryvdh/laravel-dompdf
composer require laravel/breeze --dev
```

### 3. Publicar archivos de configuración
```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan breeze:install
```

### 4. Configurar la base de datos en `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventario_laravel
DB_USERNAME=root
DB_PASSWORD=

# Configuración de Email
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-contraseña-de-aplicación
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu-email@gmail.com
MAIL_FROM_NAME="Sistema de Inventario"

# Zona horaria
APP_TIMEZONE=America/Guayaquil
```

### 5. Configurar middlewares en Laravel 11+
Edita `bootstrap/app.php`:
```php
->withMiddleware(function (\Illuminate\Foundation\Configuration\Middleware $middleware) {
    $middleware->alias([
        'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
        'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        'check.user.status' => \App\Http\Middleware\CheckUserStatus::class,
    ]);
})
```

### 6. Ejecutar migraciones y seeders
```bash
php artisan migrate
php artisan db:seed --class=RolesSeeder
php artisan db:seed --class=UsuariosSeeder
php artisan db:seed --class=CategoriasSeeder
php artisan db:seed --class=ClientesSeeder
php artisan db:seed --class=ProductosSeeder
```

### 7. Instalar y compilar assets
```bash
npm install
npm run dev
```

### 8. Crear enlaces simbólicos para storage
```bash
php artisan storage:link
```

## 🔧 Configuración de Email

### Configuración Gmail con Contraseña de Aplicación
El sistema está configurado para usar Gmail con contraseña de aplicación:

1. **Habilitar 2FA** en tu cuenta de Google
2. **Generar contraseña de aplicación** en configuración de seguridad
3. **Configurar en `.env`** con tus credenciales

### Plantillas de Email Personalizadas
- **Ubicación:** `resources/views/emails/factura.blade.php`
- **Diseño profesional** con gradientes y colores corporativos
- **Información completa** de la factura
- **PDF adjunto** automáticamente
- **Mensaje personalizable** por el usuario

## 📁 Estructura de Archivos

### Controladores
```
app/Http/Controllers/
├── Auth/
│   ├── AuthenticatedSessionController.php (NUEVO: redirección inteligente)
│   └── ... (otros controladores de Breeze)
├── ClientesController.php
├── ProductosController.php
├── FacturasController.php (NUEVO: método downloadPDF)
├── AuditoriaController.php
├── RolesController.php
├── UserController.php (NUEVO: gestión completa de usuarios)
├── ProfileController.php (NUEVO: gestión de perfil)
└── DashboardController.php
```

### Modelos
```
app/Models/
├── Cliente.php
├── Producto.php
├── Factura.php
├── FacturaDetalle.php
├── Categoria.php
├── Auditoria.php
└── User.php (NUEVO: estados avanzados)
```

### Middlewares
```
app/Http/Middleware/
└── CheckUserStatus.php (NUEVO: verificación de estado)
```

### Vistas
```
resources/views/
├── clientes/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── productos/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── facturas/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   ├── show.blade.php
│   └── pdf.blade.php
├── users/ (NUEVO)
│   ├── index.blade.php (NUEVO: gestión completa)
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── roles/ (NUEVO)
│   ├── index.blade.php
│   └── create.blade.php
├── profile/ (NUEVO)
│   ├── edit.blade.php (NUEVO: perfil profesional)
│   └── partials/
│       ├── update-profile-information-form.blade.php
│       ├── update-password-form.blade.php (NUEVO: ojos para contraseña)
│       └── delete-user-form.blade.php (NUEVO: modal profesional)
├── emails/
│   └── factura.blade.php
└── layouts/
    ├── app.blade.php
    ├── guest.blade.php
    ├── navigation.blade.php
    └── partials/
        ├── header.blade.php (NUEVO: menú desplegable profesional)
        ├── sidebar.blade.php
        └── footer.blade.php
```

## 🔐 Funcionalidades de Seguridad

### **Estados de Usuario**
- **Activo**: Acceso completo al sistema
- **Inactivo**: Cuenta suspendida, no puede acceder
- **Pendiente de eliminación**: Solo puede acceder al perfil para cancelar
- **Eliminado**: Cuenta eliminada permanentemente

### **Validación de Contraseña**
- **Acciones críticas** requieren contraseña de administrador
- **Eliminación de usuarios** con validación
- **Restauración de usuarios** con validación
- **Eliminación definitiva** con validación
- **Eliminación de cuenta propia** con validación

### **Middleware de Seguridad**
- **Verificación automática** del estado del usuario
- **Redirección inteligente** según el estado
- **Bloqueo de acceso** a usuarios suspendidos/eliminados
- **Logs de depuración** para seguimiento

## 🎨 Características de la Interfaz

### **Modales Profesionales**
- **Confirmación de eliminación** con información detallada
- **Validación de contraseña** integrada
- **Manejo de errores** sin cerrar el modal
- **Reapertura automática** en caso de errores
- **Diseño consistente** en todos los módulos

### **Formularios Avanzados**
- **Ojos para ver contraseñas** en cambio de contraseña
- **Validación en tiempo real** con feedback visual
- **Preservación de datos** en caso de errores
- **Notificaciones automáticas** con auto-cierre

### **Navegación Inteligente**
- **Menú desplegable** de usuario profesional
- **Estados visuales** claros para el usuario
- **Acceso rápido** al perfil y cerrar sesión
- **Responsive design** para móviles

## 📊 Reportes y Auditoría

### **Panel de Reportes**
- **Usuarios eliminados/restaurados** con filtros
- **Acciones de administradores** con observaciones
- **Fechas y motivos** de todas las acciones
- **Paginación y búsqueda** avanzada

### **Logs de Auditoría**
- **Registro automático** de todas las acciones
- **Información detallada** de cambios
- **Observaciones personalizadas** para acciones críticas
- **Filtros por módulo, acción, usuario y fecha**

## 🚀 Funcionalidades Avanzadas

### **Sincronización Cliente-Usuario**
- **Creación automática** de usuario al crear cliente
- **Sincronización de estados** entre cliente y usuario
- **Asignación automática** del rol "cliente"
- **Gestión unificada** de estados

### **Gestión de PDF**
- **Generación profesional** de facturas en PDF
- **Descarga directa** con método `downloadPDF()`
- **Vista previa** antes de crear factura
- **Envío por email** con PDF adjunto

### **Sistema de Notificaciones**
- **Notificaciones automáticas** con auto-cierre
- **Mensajes de éxito, error y advertencia**
- **Diseño profesional** con iconos
- **Responsive** para todos los dispositivos

## 🔧 Mantenimiento y Configuración

### **Comandos Útiles**
```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Regenerar autoload
composer dump-autoload

# Verificar rutas
php artisan route:list

# Verificar middlewares
php artisan route:list --middleware=check.user.status
```

### **Logs de Depuración**
- **Ubicación:** `storage/logs/laravel.log`
- **Middleware CheckUserStatus** incluye logs detallados
- **Información de rutas** y estados de usuario
- **Debug de redirecciones** y bloqueos

## 📝 Notas de Desarrollo

### **Cambios Recientes**
- ✅ **Sistema de estados de usuario** completamente implementado
- ✅ **Middleware de seguridad** con redirección inteligente
- ✅ **Gestión de usuarios** con CRUD completo
- ✅ **Perfil de usuario** profesional con funcionalidades avanzadas
- ✅ **Modales profesionales** con manejo de errores
- ✅ **Formularios con ojos** para ver contraseñas
- ✅ **Sincronización cliente-usuario** bidireccional
- ✅ **Auditoría completa** de todos los módulos
- ✅ **Configuración de días** de eliminación documentada

### **Próximas Mejoras**
- 🔄 **Dashboard con estadísticas** en tiempo real
- 🔄 **Notificaciones push** para cambios de estado
- 🔄 **API REST** para integración externa
- 🔄 **Backup automático** de base de datos
- 🔄 **Sistema de logs** más detallado

## 📞 Soporte

Para soporte técnico o consultas sobre el sistema, contacta al equipo de desarrollo.

---

**Versión:** 2.0.0  
**Última actualización:** Julio 2025  
**Laravel:** 11.x  
**PHP:** 8.2+
