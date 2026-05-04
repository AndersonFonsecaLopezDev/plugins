# Asistencia Diaria
Plugin para registrar y gestionar la asistencia diaria de los estudiantes. Proporciona un panel de admin intuitivo con control de estado de asistencia por estudiante.

## Autor
Juan Camilo Cuenca Spulveda

## Detalles
- Bootstrap: v5.3.3
- Sistema de registro de asistencia
- Gestión por grupos/cursos
- Estados: Asistió, Faltó, Tarde, Justificado
- Histórico de asistencias

## Funcionalidades

### Panel de Admin
- **Ubicación**: Menú de WordPress → Asistencia diaria
- **Acceso**: Solo para administradores
- **Funciones**:
  - Registro de asistencia por fecha
  - Cambio de estado individual o en lote
  - Búsqueda de estudiantes por nombre o cédula
  - Filtro de asistencias por año y mes
  - Histórico de asistencias por estudiante

## Endpoints AJAX Disponibles

### 1. **ad_guardar_estado_asistencia**
Guarda el estado de una asistencia individual.
- **Parámetros**: 
  - `asistencia_id` (int): ID de la asistencia
  - `nuevo_estado` (string): Estado válido (asistió, faltó, tarde, justificado)

### 2. **ad_get_estudiantes_historico**
Obtiene la lista de estudiantes filtrados para el histórico.
- **Parámetros**:
  - `grupo_id` (int): ID del grupo
  - `filtro_nombre` (string): Búsqueda por nombre o cédula

### 3. **ad_get_asistencias_estudiante**
Obtiene el histórico de asistencias de un estudiante.
- **Parámetros**:
  - `user_id` (int): ID del usuario
  - `grupo_id` (int): ID del grupo
  - `filtro_anio` (string): Filtro por año
  - `filtro_mes` (string): Filtro por mes

### 4. **ad_guardar_estado_asistencia_lote**
Guarda múltiples cambios de estado de asistencia.
- **Parámetros**:
  - `cambios` (array): Array de cambios con estructura `{id, estado}`

## Bases de Datos
- Tabla `wp_aess_attendance`: Registra asistencia diaria
  - Campos: ID, user_id, group_id, attendance_date, status
- Tabla `wp_aess_enrollments`: Relación estudiante-grupo
- Tabla `wp_aess_users`: Información de usuarios

## Estados de Asistencia
- **Asistió**: Estudiante presente
- **Faltó**: Estudiante ausente
- **Tarde**: Estudiante llegó tarde
- **Justificado**: Ausencia justificada

## Requisitos
- WordPress 5.0+
- PHP 7.4+
- Tabla `wp_aess_attendance` (se crea automáticamente)
- Bootstrap 5.3.3 (se carga desde CDN)

## Licencia
[MIT](https://choosealicense.com/licenses/mit/)
