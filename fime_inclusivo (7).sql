-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-11-2025 a las 05:59:38
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `fime inclusivo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `adaptaciones`
--

CREATE TABLE `adaptaciones` (
  `id_adaptacion` int(11) NOT NULL,
  `beneficiario_id` int(11) NOT NULL,
  `profesional_id` int(11) DEFAULT NULL,
  `fecha_implementacion` date NOT NULL,
  `estado` enum('Pendiente','En progreso','Finalizada') DEFAULT 'Pendiente',
  `tipo_adaptacion` varchar(100) NOT NULL,
  `numero_adaptacion` int(10) UNSIGNED DEFAULT NULL,
  `descripcion` text NOT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `adaptaciones`
--

INSERT INTO `adaptaciones` (`id_adaptacion`, `beneficiario_id`, `profesional_id`, `fecha_implementacion`, `estado`, `tipo_adaptacion`, `numero_adaptacion`, `descripcion`, `observaciones`) VALUES
(1, 21, 107, '2025-09-10', 'Finalizada', 'Curricular', 1, 'Excepcion examen', ''),
(2, 21, 108, '2025-06-10', 'Finalizada', 'Curricular', 2, 'Apoyo en clase', NULL),
(3, 21, 109, '2025-04-10', 'Finalizada', 'Evaluativa', 3, 'Excepcion examen', NULL),
(5, 1, 107, '2025-10-14', 'Pendiente', 'Evaluativa', 1, 'wrawer', 'r');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `beneficiarios`
--

CREATE TABLE `beneficiarios` (
  `id_beneficiario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido_paterno` varchar(100) NOT NULL,
  `apellido_materno` varchar(100) DEFAULT NULL,
  `curp` char(18) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `genero` enum('Masculino','Femenino','Otro') NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `correo_institucional` varchar(100) DEFAULT NULL,
  `matricula` varchar(20) NOT NULL,
  `carrera` varchar(100) NOT NULL,
  `semestre` tinyint(4) NOT NULL CHECK (`semestre` between 1 and 12),
  `plan_de_estudio` smallint(6) NOT NULL,
  `estatus_academico` enum('Activo','Baja temporal','Egresado','Baja definitiva') NOT NULL,
  `tipo_discapacidad` varchar(100) DEFAULT NULL,
  `diagnostico` text DEFAULT NULL,
  `adaptaciones` text DEFAULT NULL,
  `recursos_asignados` text DEFAULT NULL,
  `profesional_asignado` int(11) DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `estado_inicial` varchar(100) DEFAULT NULL,
  `observaciones_iniciales` text DEFAULT NULL,
  `nombre_emergencia` varchar(100) DEFAULT NULL,
  `telefono_emergencia` varchar(10) DEFAULT NULL,
  `parentesco_emergencia` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `beneficiarios`
--

INSERT INTO `beneficiarios` (`id_beneficiario`, `nombre`, `apellido_paterno`, `apellido_materno`, `curp`, `fecha_nacimiento`, `genero`, `telefono`, `correo_institucional`, `matricula`, `carrera`, `semestre`, `plan_de_estudio`, `estatus_academico`, `tipo_discapacidad`, `diagnostico`, `adaptaciones`, `recursos_asignados`, `profesional_asignado`, `fecha_ingreso`, `estado_inicial`, `observaciones_iniciales`, `nombre_emergencia`, `telefono_emergencia`, `parentesco_emergencia`) VALUES
(1, 'Rodrigo', 'Pérez', 'Gómez', 'PEGA010101HDFRZN01', '2002-05-12', 'Masculino', '8112345678', 'juan.perez@fime.uanl.mx', '2029492', 'Ingeniería Mecánica y Administración Empresarial (modalidad dual)', 7, 135, 'Baja definitiva', 'Visual', 'Miopía moderada', 'Uso de lentes', 'Software lector de pantalla', 110, '2023-08-15', 'Estable', 'Se adapta bien a clases', 'Jose Luis', '8129486409', 'Tío(a)'),
(2, 'María', 'López', 'Hernández', 'LOHM020202MDFRZN02', '2002-08-25', 'Femenino', '8223456789', 'maria.lopez@fime.uanl.mx', '2039402', 'Ingeniería Biomédica', 5, 122, 'Egresado', 'Auditiva', 'Hipoacusia leve', 'Lenguaje de señas', 'Audífonos especializados', 109, '2023-09-01', 'Requiere apoyo', 'Presenta dificultades en exposiciones orales', 'Jose Luis', '8139304452', 'Tío(a)'),
(3, 'Carlos', 'Ramírez', 'Santos', 'RASC990303HDFRZN03', '1999-03-03', 'Masculino', '8334567890', 'carlos.ramirez@fime.uanl.mx', '2049492', 'Ingeniería Mecatrónica', 9, 123, 'Egresado', 'Motriz', 'Parálisis parcial', 'Rampas de acceso', 'Silla de ruedas', 110, '2020-01-20', 'Avanzado', 'Requiere movilidad asistida', 'Samuel García', '8304334939', 'Abuelo(a)'),
(4, 'Ana', 'Martínez', 'Torres', 'MATA030404MDFRZN04', '2003-11-10', 'Femenino', '8445678901', 'ana.martinez@fime.uanl.mx', '2244577', 'Ingeniería Aeronáutica', 3, 653, 'Activo', 'Visual', 'Ceguera total', 'Braille y software lector', 'Computadora adaptada', 111, '2024-01-10', 'Inicial', 'Se encuentra en proceso de adaptación', 'Samuel García', '8183294000', 'Padre'),
(5, 'José', 'García', 'Reyes', 'GARE950505HDFRZN05', '1995-12-01', 'Masculino', '8556789012', 'jose.garcia@fime.uanl.mx', '1030420', 'Ingeniería Biomédica', 9, 0, 'Baja temporal', 'Psicosocial', 'Trastorno de ansiedad', 'Tutorías académicas', 'Asesorías psicológicas', 110, '2022-05-05', 'Inestable', 'Se recomienda seguimiento médico', NULL, NULL, NULL),
(6, 'Sofía', 'Torres', 'Molina', 'SOTM940606MDFLLN06', '2002-06-06', 'Femenino', '5551110006', 'sofia.torres@uni.edu', '2134545', 'Ingeniería Biomédica', 3, 123, 'Baja temporal', 'Visual', 'Diagnóstico A', 'Adaptación A', 'Recursos A', 101, '2023-08-01', 'Bueno', 'Observación A', 'Jose Luis', '8115434712', 'Hermano(a)'),
(7, 'Miguel', 'Flores', 'Ramos', 'MIFR950707HDFLLN07', '2000-07-07', 'Masculino', '5551110007', 'miguel.flores@uni.edu', '2001073', 'Ingeniería en Electrónica y Comunicaciones', 9, 0, 'Activo', 'Auditiva', 'Diagnóstico B', 'Adaptación B', 'Recursos B', 102, '2023-08-01', 'Bueno', 'Observación B', NULL, NULL, NULL),
(8, 'Lucía', 'Santos', 'Pérez', 'LUSP960808MDFLLN08', '2001-08-08', 'Femenino', '5551110008', 'lucia.santos@uni.edu', '2001081', 'Ingeniero Administrador de Sistemas', 3, 0, 'Activo', 'Motora', 'Diagnóstico C', 'Adaptación C', 'Recursos C', 103, '2023-08-01', 'Bueno', 'Observación C', NULL, NULL, NULL),
(9, 'Pedro', 'Gómez', 'Ortiz', 'PEGO970909HDFLLN09', '2002-09-09', 'Masculino', '5551110009', 'pedro.gomez@uni.edu', '2001092', 'Ingeniería Mecánica y Administración', 2, 0, 'Activo', 'Visual', 'Diagnóstico D', 'Adaptación D', 'Recursos D', 104, '2023-08-01', 'Bueno', 'Observación D', NULL, NULL, NULL),
(10, 'Valeria', 'Jiménez', 'Castillo', 'VAJC980101MDFLLN10', '2000-10-10', 'Femenino', '5551110010', 'valeria.jimenez@uni.edu', '2001103', 'Ingeniería Mecánica y Administración Empresarial (modalidad dual)', 12, 0, 'Activo', 'Auditiva', 'Diagnóstico E', 'Adaptación E', 'Recursos E', 105, '2023-08-01', 'Bueno', 'Observación E', NULL, NULL, NULL),
(11, 'Ricardo', 'Vargas', 'Méndez', 'RIVM990202HDFLLN11', '2001-11-11', 'Masculino', '5551110011', 'ricardo.vargas@uni.edu', '2001113', 'Ingeniería en Electrónica y Automatización', 10, 0, 'Activo', 'Motora', 'Diagnóstico F', 'Adaptación F', 'Recursos F', 106, '2023-08-01', 'Bueno', 'Observación F', NULL, NULL, NULL),
(12, 'Camila', 'Rojas', 'Fuentes', 'CARF000303MDFLLN12', '2002-12-12', 'Femenino', '5551110012', 'camila.rojas@uni.edu', '2001122', 'Ingeniería Mecatrónica', 11, 0, 'Activo', 'Visual', 'Diagnóstico G', 'Adaptación G', 'Recursos G', 107, '2023-08-01', 'Bueno', 'Observación G', NULL, NULL, NULL),
(13, 'Fernando', 'Sánchez', 'Guzmán', 'FESG010404HDFLLN13', '2000-01-01', 'Masculino', '5551110013', 'fernando.sanchez@uni.edu', '2001132', 'Ingeniería Mecánica y Administración Empresarial (modalidad dual)', 7, 0, 'Activo', 'Auditiva', 'Diagnóstico H', 'Adaptación H', 'Recursos H', 108, '2023-08-01', 'Bueno', 'Observación H', NULL, NULL, NULL),
(14, 'Isabella', 'Mendoza', 'Lara', 'ISML020505MDFLLN14', '2001-02-02', 'Femenino', '5551110014', 'isabella.mendoza@uni.edu', '2001141', 'Ingeniería Mecánica y Administración', 5, 0, 'Activo', 'Motora', 'Diagnóstico I', 'Adaptación I', 'Recursos I', 109, '2023-08-01', 'Bueno', 'Observación I', NULL, NULL, NULL),
(15, 'Diego', 'Castro', 'Alvarado', 'DICA030606HDFLLN15', '2002-03-03', 'Masculino', '5551110015', 'diego.castro@uni.edu', '2001153', 'Ingeniería Mecánica y Eléctrica', 6, 0, 'Activo', 'Visual', 'Diagnóstico J', 'Adaptación J', 'Recursos J', 110, '2023-08-01', 'Bueno', 'Observación J', NULL, NULL, NULL),
(16, 'Alejandra', 'Vargas', 'Núñez', 'ALVN030707MDFLLN16', '2001-04-04', 'Femenino', '5551110016', 'alejandra.vargas@uni.edu', '2001163', 'Ingeniería Mecatrónica', 4, 0, 'Activo', 'Auditiva', 'Diagnóstico K', 'Adaptación K', 'Recursos K', 111, '2023-08-01', 'Bueno', 'Observación K', NULL, NULL, NULL),
(17, 'Jorge', 'Morales', 'Ríos', 'JOMR040808HDFLLN17', '2000-05-05', 'Masculino', '5551110017', 'jorge.morales@uni.edu', '2001173', 'Ingeniería en Electromovilidad', 6, 0, 'Activo', 'Motora', 'Diagnóstico L', 'Adaptación L', 'Recursos L', 112, '2023-08-01', 'Bueno', 'Observación L', NULL, NULL, NULL),
(18, 'Natalia', 'Cruz', 'Sosa', 'NACS050909MDFLLN18', '2002-06-06', 'Femenino', '5551110018', 'natalia.cruz@uni.edu', '2001184', 'Ingeniería Mecatrónica', 2, 0, 'Activo', 'Visual', 'Diagnóstico M', 'Adaptación M', 'Recursos M', 113, '2023-08-01', 'Bueno', 'Observación M', NULL, NULL, NULL),
(19, 'Andrés', 'Reyes', 'Díaz', 'ANRD061010HDFLLN19', '2001-07-07', 'Masculino', '5551110019', 'andres.reyes@uni.edu', '2001192', 'Ingeniería de Manufactura', 8, 0, 'Activo', 'Auditiva', 'Diagnóstico N', 'Adaptación N', 'Recursos N', 114, '2023-08-01', 'Bueno', 'Observación N', NULL, NULL, NULL),
(20, 'Fernanda', 'Pineda', 'Lopez', 'FEPL071111MDFLLN20', '2000-08-08', 'Femenino', '5551110020', 'fernanda.pineda@uni.edu', '2001234', 'Ingeniería Mecatrónica', 3, 0, 'Activo', 'Motora', 'Diagnóstico O', 'Adaptación O', 'Recursos O', 115, '2023-08-01', 'Bueno', 'Observación O', NULL, NULL, NULL),
(21, 'Orlando Jair', 'García', 'Puente', 'GAPO040515HNLRNRA0', '2004-03-12', 'Masculino', '8129486409', 'jair.garciapnt@uanl.edu.mx', '2028658', 'Ingeniero Administrador de Sistemas', 8, 0, 'Activo', 'Auditiva', 'Oido derecho', 'Clases especiales', 'Audifono especial', 110, '2025-04-23', 'Estable', 'Ninguna', NULL, NULL, NULL),
(22, 'Daniel', 'Perez', 'Castro', 'JEHS130410HNLRNRA0', '2005-03-12', 'Masculino', '8115434712', 'dani_pcastro@uanl.edu.mx', '2048939', 'Ingeniería Aeronáutica', 9, 0, 'Activo', 'Motriz', 'Dificultad al caminar', 'Rampas', 'Silla de ruedas', 110, '2025-07-12', 'Critico', 'Presenta dificultades para llegar a los salones', NULL, NULL, NULL),
(42, 'Victor Lopez', 'Sanchez', 'Cuevas', 'VSCL130410HNLRNRA0', '2004-07-13', 'Masculino', '8115434717', 'viuctor@gmail.com', '2849204', 'Ingeniería Biomédica', 8, 0, 'Activo', 'Auditiva', 'Escucha mal del izquierdo', 'Audifono especial', 'Clases con subtitulos', 110, '2025-10-01', 'Estable', 'Todo bien', NULL, NULL, NULL),
(43, 'Enrique', 'García', 'Puente', 'PDPA130410HNLRNRA0', '2004-06-12', 'Masculino', '8138492040', 'dwindajwdd@gmail.com', '2034204', 'Ingeniería Mecánica y Administración', 4, 0, 'Activo', 'rara', 'awfawr', 'awraw', 'awrarw', 110, '2025-09-12', 'dwaf', 'awffa', NULL, NULL, NULL),
(51, 'Alonso', 'Sanchez', 'Ruiz', 'ISKA230410HNLRNRA0', '2002-02-02', 'Masculino', '8129486333', 'alonsohg@uanl.edu.mx', '2242424', 'Ingeniería Biomédica', 2, 123, 'Activo', 'rwr', 'eresesr', 'esresr', 'resr', 108, '2025-03-23', 'sefes', 'fesfs', 'Gabriel Herrera', '8115435954', 'Amigo(a)'),
(52, 'Gabriel', 'García', 'Puente', 'JXKF130410HNLRNRA0', '2001-10-02', 'Masculino', '8129486409', 'jairt@uanl.edu.mx', '2028352', 'Ingeniería Biomédica', 1, 244, 'Baja temporal', 'estes', 'testst', 'stest', 'etst', 108, '2025-10-17', 'ubbhubh', 'hui', 'Gabriel Herrera', '8183294000', 'Amigo(a)');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `diagnosticos`
--

CREATE TABLE `diagnosticos` (
  `id_diagnostico` int(11) NOT NULL,
  `beneficiario_id` int(11) NOT NULL,
  `profesional_id` int(11) DEFAULT NULL,
  `fecha_diagnostico` date NOT NULL,
  `tipo_diagnostico` varchar(100) NOT NULL,
  `numero_diagnostico` int(10) UNSIGNED DEFAULT NULL,
  `resultado` text NOT NULL,
  `observaciones` text DEFAULT NULL,
  `archivo_adjunto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `diagnosticos`
--

INSERT INTO `diagnosticos` (`id_diagnostico`, `beneficiario_id`, `profesional_id`, `fecha_diagnostico`, `tipo_diagnostico`, `numero_diagnostico`, `resultado`, `observaciones`, `archivo_adjunto`) VALUES
(1, 21, 101, '2025-01-11', 'Psicológico', 1, 'El beneficiario presenta dificultades significativas en la gestión del tiempo y la concentración en el aula. Alto nivel de ansiedad reportado.', 'Se inician técnicas básicas de respiración y se implementa un cronograma de estudio estructurado. Es crucial el apoyo en casa.', 'https://drive.google.com/file/d/1jVFlb0iI0GOOmEIFb6X3TfjMkQTIq4hp/view?usp=drive_link'),
(2, 21, 101, '2025-02-10', 'Seguimiento Mensual', 2, 'Se observa una ligera mejoría en la organización de tareas. La concentración en sesiones ha aumentado de 15 a 25 minutos.', 'La implementación del cronograma ha sido positiva. Se deben reforzar las pausas activas y la priorización de tareas.', NULL),
(3, 21, 101, '2025-03-10', 'Revisión de Adaptaciones', 3, 'El beneficiario ha logrado mantener la consistencia en el uso de las técnicas de enfoque. La ansiedad se ha reducido a niveles manejables.', 'Los hábitos de estudio están más consolidados. Se recomienda una reunión con el docente principal para verificar el impacto en calificaciones.', NULL),
(4, 21, 101, '2025-04-10', 'Cierre Trimestral', 4, '**Mejoría Significativa:** El rendimiento académico ha mejorado un 15% en el último mes. El beneficiario ha adoptado plenamente las herramientas de autogestión.', 'Se da por finalizada la fase intensiva de intervención. Continuar con seguimiento semestral.', 'Cierre_21_Abr.pdf'),
(5, 21, 109, '2025-10-03', 'Médico General', 5, 'WD', 'EAE', 'AWEEWA'),
(6, 21, 101, '2025-10-08', 'Médico General', 6, 'WAAW', 'EAE', 'WEA'),
(10, 1, 108, '2025-10-02', 'Psicológico', 1, 'Problemas con la ansiedad', 'wewe', 'eww'),
(11, 21, 108, '2025-10-16', 'Auditivo', 7, 'Problemas oído izquierdo', 'Se le tiene pensado asignar audifono especial', ''),
(13, 1, 107, '2023-04-12', 'Psicológico', 2, 'eres', 'rr', 'rers'),
(14, 1, 107, '2025-10-09', 'Médico General', 3, 'waeae', 'awe', 'ewaawe');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `intervenciones`
--

CREATE TABLE `intervenciones` (
  `id_intervencion` int(11) NOT NULL,
  `beneficiario_id` int(11) NOT NULL,
  `numero_intervencion` int(10) UNSIGNED NOT NULL,
  `profesional_id` int(11) DEFAULT NULL,
  `fecha_implementacion` date NOT NULL,
  `estado` enum('Activa','En pausa','Finalizada') DEFAULT 'Activa',
  `tipo_intervencion` enum('Apoyo Académico','Orientación Psicológica','Terapia del Lenguaje','Apoyo Tecnológico','Mentoría','Otro') NOT NULL,
  `resultados_esperados` text DEFAULT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `intervenciones`
--

INSERT INTO `intervenciones` (`id_intervencion`, `beneficiario_id`, `numero_intervencion`, `profesional_id`, `fecha_implementacion`, `estado`, `tipo_intervencion`, `resultados_esperados`, `observaciones`) VALUES
(1, 21, 1, 102, '2025-10-16', 'Activa', 'Apoyo Académico', 'Se le brindo ayuda en cuanto las clases del alumno', 'Ninguna\r\n'),
(2, 21, 2, 108, '2025-10-16', 'En pausa', 'Apoyo Académico', 'warr', 'arw'),
(3, 21, 3, 112, '2025-10-28', 'Finalizada', 'Apoyo Académico', 'war', 'ar'),
(4, 1, 1, 108, '2025-10-24', 'En pausa', 'Orientación Psicológica', 'hbhbij', 'buou'),
(5, 21, 4, 110, '2025-10-10', 'En pausa', 'Apoyo Tecnológico', 'ert', 'srs');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesionales`
--

CREATE TABLE `profesionales` (
  `id_profesional` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido_paterno` varchar(100) NOT NULL,
  `apellido_materno` varchar(100) DEFAULT NULL,
  `correo_institucional` varchar(100) NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `especialidad` varchar(100) DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  `usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `permiso_beneficiario` tinyint(1) DEFAULT 0,
  `permiso_diagnostico` tinyint(1) DEFAULT 0,
  `permiso_adaptacion` tinyint(1) DEFAULT 0,
  `permiso_intervencion` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `profesionales`
--

INSERT INTO `profesionales` (`id_profesional`, `nombre`, `apellido_paterno`, `apellido_materno`, `correo_institucional`, `telefono`, `especialidad`, `estado`, `usuario`, `contrasena`, `permiso_beneficiario`, `permiso_diagnostico`, `permiso_adaptacion`, `permiso_intervencion`) VALUES
(101, 'Carlos', 'López', 'Gómez', 'carlos.lopez@uni.edu', '5551000101', 'Psicología', 'Activo', 'carlosl', '1234', 1, 1, 1, 1),
(102, 'Ana', 'Martínez', 'Ramírez', 'ana.martinez@uni.edu', '5551000102', 'Educación Especial', 'Activo', 'anam', '1234', 1, 1, 1, 1),
(103, 'Juan', 'García', 'Santos', 'juan.garcia@uni.edu', '5551000103', 'Psicología', 'Activo', 'juang', '1234', 1, 1, 1, 1),
(104, 'Laura', 'Ramírez', 'Vega', 'laura.ramirez@uni.edu', '5551000104', 'Educación Especial', 'Activo', 'laurar', '1234', 1, 1, 1, 1),
(105, 'Pedro', 'Vega', 'Cruz', 'pedro.vega@uni.edu', '5551000105', 'Psicología', 'Activo', 'pedrov', '1234', 1, 1, 1, 1),
(106, 'Marta', 'Santos', 'Hernández', 'marta.santos@uni.edu', '5551000106', 'Educación Especial', 'Activo', 'martas', '1234', 1, 1, 1, 1),
(107, 'Luis', 'Hernández', 'Cruz', 'luis.hernandez@uni.edu', '5551000107', 'Psicología', 'Activo', 'luish', '1234', 1, 1, 1, 1),
(108, 'María', 'Cruz', 'Ríos', 'maria.cruz@uni.edu', '5551000108', 'Educación Especial', 'Activo', 'mariac', '1234', 1, 1, 1, 1),
(109, 'Jorge', 'Ríos', 'Torres', 'jorge.rios@uni.edu', '5551000109', 'Psicología', 'Activo', 'jorger', '1234', 1, 1, 1, 1),
(110, 'Isabel', 'Torres', 'Pérez', 'isabel.torres@uni.edu', '5551000110', 'Educación Especial', 'Activo', 'isabelt', '1234', 1, 1, 1, 1),
(111, 'Fernando', 'Pineda', 'Guzmán', 'fernando.pineda@uni.edu', '5551000111', 'Psicología', 'Activo', 'fernandop', '1234', 1, 1, 1, 1),
(112, 'Alejandra', 'Morales', 'Lopez', 'alejandra.morales@uni.edu', '5551000112', 'Educación Especial', 'Activo', 'alejandram', '1234', 1, 1, 1, 1),
(113, 'Diego', 'Castro', 'Alvarado', 'diego.castro@uni.edu', '5551000113', 'Psicología', 'Activo', 'diegoc', '1234', 1, 1, 1, 1),
(114, 'Natalia', 'Reyes', 'Díaz', 'natalia.reyes@uni.edu', '5551000114', 'Educación Especial', 'Activo', 'nataliar', '1234', 1, 1, 1, 1),
(115, 'Ricardo', 'Jiménez', 'Sánchez', 'ricardo.jimenez@uni.edu', '5551000115', 'Psicología', 'Activo', 'ricardoj', '1234', 1, 1, 1, 1),
(116, 'Francisco Javier', 'García', 'Puente', 'fco_garcia@uanl.edu.mx', '8284934004', 'Pedagogía', 'Activo', 'Francisco Javier', 'futbolista10', 1, 1, 1, 0),
(117, 'Orlando', 'García', 'Puente', 'jair.pnt@uanl.edu.mx', '8193400455', 'Otro', 'Activo', 'OrlandoPuente', 'MeetsEvil10', 1, 1, 1, 1),
(118, 'Orlando', 'García', 'Puente', 'jair.garcia@uanl.edu.mx', '8193400455', 'Otro', 'Activo', 'OrlandoPuente2', 'MeetsEvil10', 1, 1, 1, 1),
(119, 'Adriana', 'Puente', 'Perez', 'adriana_puente@gmail.com', '8139493040', 'Otro', 'Activo', 'AdrianaPuente', 'adris10', 1, 0, 1, 1),
(120, 'Javier', 'García', 'Puente', 'javier_garcia@gmail.com', '8113744024', 'Trabajo Social', 'Activo', 'JavierPuente', 'javier10', 0, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_login`
--

CREATE TABLE `usuarios_login` (
  `id` int(11) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('Administrador','Profesional','Academico') NOT NULL,
  `estado` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios_login`
--

INSERT INTO `usuarios_login` (`id`, `usuario`, `contrasena`, `rol`, `estado`) VALUES
(1, 'admin_fime', '12345', 'Administrador', 'Inactivo'),
(2, 'orlandojgarciap@gmail.com', 'MeetsEvil10', 'Administrador', 'Inactivo'),
(3, 'prueba123', 'prueba123', 'Profesional', 'Inactivo'),
(4, 'prueba456', 'prueba456', 'Academico', 'Inactivo'),
(5, 'Francisco Javier', 'futbolista10', 'Profesional', 'Activo'),
(6, 'OrlandoPuente', 'MeetsEvil10', 'Administrador', 'Activo'),
(7, 'OrlandoPuente2', 'MeetsEvil10', 'Administrador', 'Activo'),
(8, 'AdrianaPuente', 'adris10', 'Profesional', 'Activo'),
(9, 'JavierPuente', 'javier10', 'Academico', 'Activo');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `adaptaciones`
--
ALTER TABLE `adaptaciones`
  ADD PRIMARY KEY (`id_adaptacion`),
  ADD KEY `beneficiario_id` (`beneficiario_id`),
  ADD KEY `profesional_id` (`profesional_id`);

--
-- Indices de la tabla `beneficiarios`
--
ALTER TABLE `beneficiarios`
  ADD PRIMARY KEY (`id_beneficiario`),
  ADD UNIQUE KEY `curp` (`curp`),
  ADD UNIQUE KEY `matricula` (`matricula`),
  ADD UNIQUE KEY `correo_institucional` (`correo_institucional`),
  ADD KEY `profesional_asignado` (`profesional_asignado`);

--
-- Indices de la tabla `diagnosticos`
--
ALTER TABLE `diagnosticos`
  ADD PRIMARY KEY (`id_diagnostico`),
  ADD KEY `beneficiario_id` (`beneficiario_id`),
  ADD KEY `profesional_id` (`profesional_id`);

--
-- Indices de la tabla `intervenciones`
--
ALTER TABLE `intervenciones`
  ADD PRIMARY KEY (`id_intervencion`),
  ADD KEY `beneficiario_id` (`beneficiario_id`),
  ADD KEY `profesional_id` (`profesional_id`);

--
-- Indices de la tabla `profesionales`
--
ALTER TABLE `profesionales`
  ADD PRIMARY KEY (`id_profesional`),
  ADD UNIQUE KEY `correo_institucional` (`correo_institucional`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Indices de la tabla `usuarios_login`
--
ALTER TABLE `usuarios_login`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `adaptaciones`
--
ALTER TABLE `adaptaciones`
  MODIFY `id_adaptacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `beneficiarios`
--
ALTER TABLE `beneficiarios`
  MODIFY `id_beneficiario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de la tabla `diagnosticos`
--
ALTER TABLE `diagnosticos`
  MODIFY `id_diagnostico` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `intervenciones`
--
ALTER TABLE `intervenciones`
  MODIFY `id_intervencion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `profesionales`
--
ALTER TABLE `profesionales`
  MODIFY `id_profesional` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT de la tabla `usuarios_login`
--
ALTER TABLE `usuarios_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `adaptaciones`
--
ALTER TABLE `adaptaciones`
  ADD CONSTRAINT `adaptaciones_ibfk_1` FOREIGN KEY (`beneficiario_id`) REFERENCES `beneficiarios` (`id_beneficiario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `adaptaciones_ibfk_2` FOREIGN KEY (`profesional_id`) REFERENCES `profesionales` (`id_profesional`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `beneficiarios`
--
ALTER TABLE `beneficiarios`
  ADD CONSTRAINT `beneficiarios_ibfk_1` FOREIGN KEY (`profesional_asignado`) REFERENCES `profesionales` (`id_profesional`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `diagnosticos`
--
ALTER TABLE `diagnosticos`
  ADD CONSTRAINT `diagnosticos_ibfk_1` FOREIGN KEY (`beneficiario_id`) REFERENCES `beneficiarios` (`id_beneficiario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `diagnosticos_ibfk_2` FOREIGN KEY (`profesional_id`) REFERENCES `profesionales` (`id_profesional`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `intervenciones`
--
ALTER TABLE `intervenciones`
  ADD CONSTRAINT `intervenciones_ibfk_1` FOREIGN KEY (`beneficiario_id`) REFERENCES `beneficiarios` (`id_beneficiario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `intervenciones_ibfk_2` FOREIGN KEY (`profesional_id`) REFERENCES `profesionales` (`id_profesional`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
