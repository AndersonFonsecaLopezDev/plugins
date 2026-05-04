# Consulta Certificados
Plugin para buscar y consultar certificados y cursos de usuarios en la base de datos.

## Autor
Anderson Fonseca López

## Detalles
- Bootstrap: v5.3
- Sistema de búsqueda por número de cédula o diploma
- Control de acceso por roles
- Importación desde Excel/CSV

## Shortcodes Disponibles

### 1. **[cc_buscar_certificados]**
Muestra el formulario de búsqueda de certificados.
- **Campos de búsqueda**: 
  - Número de cédula
  - Número de diploma
- **Componentes requeridos**:
  - Input con `id="document_text_box"` para ingresar el documento
  - Botón con `id="search_certificate_button"` para ejecutar la búsqueda
- **Resultado**: Muestra información del certificado (nombre, curso, fecha, etc.)

### 2. **[cc_consulta_certificados]**
Muestra el panel completo de consulta de certificados.
- **Acceso**: Controlado por roles (Estudiante, Secretario, Talento Humano)
- **Funciones**: Búsqueda y visualización de datos del certificado

## Configuración

### Ajustes disponibles en `/wp-admin/admin.php?page=cc-menu-ajustes`:
- **Roles con acceso**: Configurar qué roles pueden consultar certificados
  - Estudiante
  - Secretario
  - Talento Humano

## Permisos por Rol
- **Estudiante**: Acceso a búsqueda de propios certificados
- **Secretario**: Acceso completo a consulta de certificados
- **Talento Humano**: Acceso completo a consulta de certificados
- **Administrador**: Acceso completo a todas las funciones

## Bases de Datos
- Tabla `wp_cc_certificados`: Almacena información de certificados
  - Campos: ID, razon_social, NIT, resolución, fecha, nombres, apellidos, cédula, número diploma, curso, acta, fecha acta

## Importación de Datos
Los certificados se cargan desde archivos Excel/CSV con la siguiente estructura:
- Razón Social
- NIT
- Resolución
- Fecha Resolución
- Apellidos (1 y 2)
- Nombres (1 y 2)
- Cédula
- Número Diploma
- Curso
- Acta
- Fecha Acta

## Requisitos
- WordPress 5.0+
- PHP 7.4+
- Tabla `wp_cc_certificados` (se crea automáticamente al activar)

## Licencia
[MIT](https://choosealicense.com/licenses/mit/)
