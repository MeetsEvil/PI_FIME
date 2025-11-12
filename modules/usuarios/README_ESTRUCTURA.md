# Módulo de Usuarios - Documentación

## Estructura de Tablas

### Situación Actual
El sistema mantiene dos tablas relacionadas:

1. **`usuarios_login`**: Credenciales de acceso
   - `id`: ID único del usuario
   - `usuario`: Nombre de usuario para login
   - `contrasena`: Contraseña
   - `rol`: Administrador, Profesional o Académico

2. **`profesionales`**: Información detallada del profesional
   - `id_profesional`: ID único del profesional
   - `nombre`, `apellido_paterno`, `apellido_materno`
   - `correo_institucional`, `telefono`
   - `especialidad`
   - `estado`: Activo/Inactivo
   - `usuario`, `contrasena` (duplicados de usuarios_login)
   - `permiso_beneficiario`, `permiso_diagnostico`, `permiso_adaptacion`, `permiso_intervencion`

### Relación entre Tablas
Ambas tablas se relacionan mediante el campo `usuario` (nombre de usuario).

## Flujo de Creación de Usuario

### Formulario (crear_usuarios.php)
El formulario está dividido en 2 páginas:

**Página 1: Datos Personales**
- Nombre
- Apellido Paterno
- Apellido Materno
- Correo Institucional
- Teléfono
- Especialidad

**Página 2: Datos de Acceso**
- Usuario
- Contraseña
- Confirmar Contraseña
- Rol (Administrador/Profesional/Académico)
- Estado (Activo/Inactivo)
- Permisos (4 checkboxes):
  - Beneficiarios
  - Seguimiento
  - Adaptaciones
  - Intervenciones

### Proceso de Guardado (guardar_usuario.php)

1. **Validación de datos**
   - Verifica que todos los campos requeridos estén completos
   - Valida que el usuario no exista en `usuarios_login`
   - Valida que el correo no exista en `profesionales`

2. **Transacción de base de datos**
   - Inicia una transacción para garantizar integridad
   - Inserta en `profesionales` (con todos los datos)
   - Inserta en `usuarios_login` (solo credenciales y rol)
   - Si todo es exitoso, confirma la transacción
   - Si hay error, revierte todos los cambios

3. **Respuesta**
   - Retorna JSON con el resultado
   - Muestra modal de éxito
   - Redirige a la lista de usuarios

## Visualización de Datos (get_usuarios.php)

La consulta combina ambas tablas usando LEFT JOIN:

```sql
SELECT 
    ul.id AS id_usuario,
    CONCAT(p.nombre, ' ', p.apellido_paterno, ' ', p.apellido_materno) AS nombre_usuario,
    ul.rol,
    p.correo_institucional AS correo,
    p.especialidad
FROM usuarios_login ul
LEFT JOIN profesionales p ON ul.usuario = p.usuario
```

## Recomendaciones para Mejorar la Estructura

### Opción 1: Eliminar Duplicación (RECOMENDADO)

Modificar la estructura para evitar duplicación de datos:

```sql
-- 1. Agregar relación directa
ALTER TABLE usuarios_login 
ADD COLUMN id_profesional INT(11) DEFAULT NULL AFTER id,
ADD CONSTRAINT fk_usuario_profesional 
FOREIGN KEY (id_profesional) REFERENCES profesionales(id_profesional) 
ON DELETE CASCADE;

-- 2. Eliminar campos duplicados de profesionales
ALTER TABLE profesionales 
DROP COLUMN usuario,
DROP COLUMN contrasena;

-- 3. Migrar datos existentes (ejecutar antes del paso 2)
UPDATE usuarios_login ul
INNER JOIN profesionales p ON ul.usuario = p.usuario
SET ul.id_profesional = p.id_profesional;
```

**Ventajas:**
- Elimina duplicación de datos
- Mejora integridad referencial
- Facilita mantenimiento
- Evita inconsistencias

### Opción 2: Mantener Estructura Actual (IMPLEMENTADO)

Mantener ambas tablas sincronizadas mediante transacciones:

**Ventajas:**
- No requiere cambios en BD existente
- Compatibilidad con código legacy
- Implementación inmediata

**Desventajas:**
- Duplicación de datos (usuario, contraseña)
- Riesgo de inconsistencias
- Mayor complejidad en actualizaciones

## Archivos Creados

1. **modules/usuarios/index_usuarios.php** - Lista de usuarios con DataTables
2. **modules/usuarios/get_usuarios.php** - API para obtener datos de usuarios
3. **modules/usuarios/crear_usuarios.php** - Formulario de creación (2 páginas)
4. **modules/usuarios/guardar_usuario.php** - Procesamiento y guardado
5. **assets/js/main_usuarios.js** - Lógica de paginación y validación

## Próximos Pasos

Para completar el módulo, se necesitan:

1. **editar_usuarios.php** - Formulario de edición
2. **actualizar_usuario.php** - Procesamiento de actualización
3. **ver_usuarios.php** - Vista detallada de usuario
4. **eliminar_usuario.php** - Eliminación (opcional)

## Notas Importantes

- Solo usuarios con rol "Administrador" pueden acceder al módulo
- Las contraseñas se guardan en texto plano (RECOMENDACIÓN: implementar hash)
- Los permisos se almacenan como booleanos (0/1) en la tabla profesionales
- La transacción garantiza que ambas tablas se actualicen o ninguna
