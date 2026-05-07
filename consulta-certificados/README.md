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

## Gestión de Certificados

### Modificar o Eliminar Información de Certificados

Los certificados se almacenan en un archivo CSV ubicado en `includes/certificado/listado.csv`. Para modificar o eliminar información:

1. **Modificar un certificado:**
   - Edita directamente el archivo `listado.csv` con un editor de texto o Excel.
   - Asegúrate de mantener el formato correcto: RAZON SOCIAL, NIT, RESOLUCION, FECHA RESOLUCION, APELLIDO1, APELLIDO2, NOMBRE1, NOMBRE2, CEDULA, NUMERODIPLOMA, CURSO, ACTA, FECHA_ACTA.
   - Guarda los cambios y la tabla se actualizará automáticamente.

2. **Eliminar un certificado:**
   - Abre el archivo `listado.csv`.
   - Elimina la fila correspondiente al certificado que deseas remover.
   - Guarda el archivo. Los cambios serán reflejados en la interfaz administrativa.

**Nota:** No hay interfaz gráfica para editar individualmente los certificados. Todas las modificaciones se realizan directamente en el archivo CSV. Se recomienda hacer una copia de respaldo antes de editar.
