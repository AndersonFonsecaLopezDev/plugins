# Bolsa de Empleo
Plugin para gestionar la bolsa de empleo, registro de usuarios, login personalizado y consulta de hojas de vida.

## Autor
Anderson Fonseca López

## Detalles
- Bootstrap: v5.3
- Sistema de roles: Estudiante, Secretario, Talento Humano
- Autenticación personalizada con JWT
- Gestión de permisos por rol

## Shortcodes Disponibles

### 1. **[login-usuarios]**
Muestra el formulario de login personalizado para estudiantes.
- **Ubicación**: `/pagina-login` (configurable en ajustes)
- **Acceso**: Solo estudiantes pueden iniciar sesión aquí
- **Secretario y Talento Humano**: Se redirigen a WP-admin

### 2. **[registro-usuarios]**
Muestra el formulario de registro para nuevos usuarios.
- **Ubicación**: `/pagina-registro` (configurable en ajustes)
- **Campos**: Email, contraseña, nombre, apellido, cédula, teléfono, etc.

### 3. **[lu_forgot_password]**
Muestra el formulario para recuperar/cambiar contraseña.
- **Ubicación**: `/recuperacion-contrasena` (configurable en ajustes)
- **Proceso**: 
  1. Usuario ingresa su email
  2. Recibe código de recuperación por email
  3. Ingresa el código y nueva contraseña
  4. Contraseña se actualiza

### 4. **[mi_hoja_de_vida]**
Panel para que estudiantes gestionen su hoja de vida y CV.
- **Acceso**: Solo estudiantes registrados y logueados
- **Funciones**: Subir CV, actualizar información profesional

## Configuración

### Ajustes disponibles en `/wp-admin/admin.php?page=be-menu-ajustes`:
- **URLs Importantes**: Configurar endpoints de login, registro, recuperación de contraseña
- **Permisos de Acceso**: Controlar qué roles acceden a la bolsa de empleo
- **Configuración de Correo**: Email remitente, contraseña, nombre para notificaciones

## Permisos por Rol
- **Estudiante**: Acceso a login, registro, recuperación de contraseña, mi hoja de vida
- **Secretario**: Acceso solo a WP-admin, gestión de CVs y aprobaciones
- **Talento Humano**: Acceso solo a WP-admin, consulta de CVs aprobados
- **Administrador**: Acceso completo a todas las funciones

## Requisitos
- WordPress 5.0+
- PHP 7.4+
- Tabla `wp_aess_users` (se crea automáticamente al activar)

## Bases de Datos
- Tabla `wp_aess_users`: Almacena información de usuarios personalizados con roles

## Licencia
[MIT](https://choosealicense.com/licenses/mit/)
