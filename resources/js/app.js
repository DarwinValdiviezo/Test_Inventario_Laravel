import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Importar funcionalidades de auditoría
import './audit';

// Importar funcionalidades de usuarios
import './users';

// Importar funcionalidades de creación de usuarios
import './user-create';

// Importar funcionalidades de edición de usuarios
import './user-edit';

// Importar funcionalidades de show de usuarios
import './user-show';

// Importar funcionalidades de roles
import './roles';

// Importar funcionalidades de facturas
import './facturas';
