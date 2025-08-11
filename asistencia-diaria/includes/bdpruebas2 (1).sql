-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-08-2025 a las 22:20:50
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
-- 
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wp_aess_attendance`
--

CREATE TABLE `wp_aess_attendance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('asistió','faltó','tarde','justificado') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `group_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `wp_aess_attendance`
--

INSERT INTO `wp_aess_attendance` (`id`, `user_id`, `course_id`, `attendance_date`, `status`, `created_at`, `group_id`) VALUES
(1, 1, 1, '2025-07-26', 'faltó', '2025-07-27 02:39:14', 1),
(2, 2, 1, '2025-07-26', 'tarde', '2025-07-27 02:39:14', 1),
(3, 3, 1, '2025-07-26', 'justificado', '2025-07-27 02:39:14', 1),
(4, 1, 1, '2025-07-28', 'faltó', '2025-07-28 19:26:12', 1),
(5, 2, 1, '2025-07-28', 'faltó', '2025-07-28 19:26:12', 1),
(6, 3, 1, '2025-07-28', 'asistió', '2025-07-28 19:26:12', 1),
(7, 4, 1, '2025-07-28', 'asistió', '2025-07-28 20:24:45', 2),
(8, 5, 1, '2025-07-28', 'faltó', '2025-07-28 20:24:45', 2),
(9, 6, 2, '2025-08-01', 'asistió', '2025-08-01 18:58:42', 3),
(10, 7, 2, '2025-08-01', 'faltó', '2025-08-01 18:58:42', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wp_aess_courses`
--

CREATE TABLE `wp_aess_courses` (
  `id` int(11) NOT NULL,
  `course_name` varchar(150) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_hours` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `wp_aess_courses`
--

INSERT INTO `wp_aess_courses` (`id`, `course_name`, `price`, `total_hours`, `created_at`) VALUES
(1, 'Fundamentos de Vigilancia', 150.00, 40, '2025-07-26 19:43:12'),
(2, 'Fundamentos de Escolta', 200.00, 45, '2025-07-26 19:43:12'),
(3, 'Atención Prehospitalaria Básica', 180.00, 30, '2025-07-26 19:43:12'),
(4, 'Protección a Dignatarios', 220.00, 50, '2025-07-26 19:43:12'),
(5, 'Defensa Personal', 180.00, 30, '2025-07-26 19:43:12'),
(6, 'Manejo de Conflictos', 190.00, 35, '2025-07-26 19:43:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wp_aess_course_groups`
--

CREATE TABLE `wp_aess_course_groups` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `group_code` varchar(10) NOT NULL,
  `schedule_day` enum('lunes','martes','miércoles','jueves','viernes','sábado','domingo') DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `wp_aess_course_groups`
--

INSERT INTO `wp_aess_course_groups` (`id`, `course_id`, `group_code`, `schedule_day`, `start_time`, `end_time`, `created_at`) VALUES
(1, 1, 'G1', 'lunes', '08:00:00', '10:00:00', '2025-07-26 19:43:18'),
(2, 1, 'G2', 'jueves', '10:00:00', '12:00:00', '2025-07-26 19:43:18'),
(3, 2, 'G1', 'jueves', '08:00:00', '10:00:00', '2025-07-26 19:43:18'),
(4, 2, 'G2', 'viernes', '14:00:00', '16:00:00', '2025-07-26 19:43:18'),
(5, 2, 'G3', 'viernes', '16:00:00', '18:00:00', '2025-07-26 19:43:18'),
(6, 3, 'G1', 'jueves', '10:00:00', '15:00:00', '2025-07-26 19:43:18'),
(7, 4, 'G1', 'viernes', '08:00:00', '10:00:00', '2025-07-26 19:43:18'),
(8, 5, 'G1', 'viernes', '10:00:00', '12:00:00', '2025-07-26 19:43:18'),
(9, 6, 'G1', 'viernes', '14:00:00', '16:00:00', '2025-07-26 19:43:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wp_aess_enrollments`
--

CREATE TABLE `wp_aess_enrollments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `enrollment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('activo','finalizado','cancelado') DEFAULT 'activo',
  `group_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `wp_aess_enrollments`
--

INSERT INTO `wp_aess_enrollments` (`id`, `user_id`, `course_id`, `enrollment_date`, `status`, `group_id`) VALUES
(7, 1, 1, '2025-07-26 19:48:34', 'activo', 1),
(8, 2, 1, '2025-07-26 19:48:34', 'activo', 1),
(9, 3, 1, '2025-07-26 19:48:34', 'activo', 1),
(10, 4, 1, '2025-07-26 19:48:34', 'activo', 2),
(11, 5, 1, '2025-07-26 19:48:34', 'activo', 2),
(12, 6, 2, '2025-07-26 19:48:34', 'activo', 3),
(13, 7, 2, '2025-07-26 19:48:34', 'activo', 3),
(14, 8, 2, '2025-07-26 19:48:34', 'activo', 4),
(15, 6, 2, '2025-07-26 19:48:34', 'activo', 5),
(16, 7, 2, '2025-07-26 19:48:34', 'activo', 5),
(17, 9, 3, '2025-07-26 19:48:34', 'activo', 6),
(18, 10, 3, '2025-07-26 19:48:34', 'activo', 6),
(19, 9, 4, '2025-07-26 19:48:34', 'activo', 7),
(20, 10, 4, '2025-07-26 19:48:34', 'activo', 7),
(21, 1, 5, '2025-07-26 19:48:34', 'activo', 8),
(22, 2, 5, '2025-07-26 19:48:34', 'activo', 8),
(23, 4, 6, '2025-07-26 19:48:34', 'activo', 9),
(24, 5, 6, '2025-07-26 19:48:34', 'activo', 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wp_aess_users`
--

CREATE TABLE `wp_aess_users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `second_last_name` varchar(50) DEFAULT NULL,
  `national_id` varchar(20) NOT NULL,
  `gender` enum('masculino','femenino','otro') NOT NULL,
  `birth_date` date NOT NULL,
  `departamento` varchar(100) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `has_military_id` tinyint(1) DEFAULT 0,
  `has_work_experience` tinyint(1) DEFAULT 0,
  `address` varchar(255) NOT NULL,
  `user_role` enum('profesor','administrador','estudiante') NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `email_verificado` tinyint(1) DEFAULT 0,
  `codigo_activacion` varchar(10) DEFAULT NULL COMMENT 'Código de activación de 6 caracteres',
  `fecha_activacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `wp_aess_users`
--

INSERT INTO `wp_aess_users` (`id`, `first_name`, `middle_name`, `last_name`, `second_last_name`, `national_id`, `gender`, `birth_date`, `departamento`, `city`, `phone_number`, `email`, `has_military_id`, `has_work_experience`, `address`, `user_role`, `contrasena`, `created_at`, `email_verificado`, `codigo_activacion`, `fecha_activacion`) VALUES
(1, 'Juan', NULL, 'Pérez', NULL, '1033937287', 'masculino', '1998-05-10', NULL, '', NULL, 'juan@example.com', 0, 0, '', 'estudiante', '', '2025-07-26 19:43:01', 1, NULL, NULL),
(2, 'Maria', NULL, 'Gomez', NULL, '1009897121', 'femenino', '1999-07-15', NULL, '', NULL, 'maria@example.com', 0, 0, '', 'estudiante', '', '2025-07-26 19:43:01', 1, NULL, NULL),
(3, 'Luis', NULL, 'Rodriguez', NULL, '1035652323', 'masculino', '2000-01-22', NULL, '', NULL, 'luis@example.com', 0, 0, '', 'estudiante', '', '2025-07-26 19:43:01', 1, NULL, NULL),
(4, 'Ana', NULL, 'Martinez', NULL, '1002786227', 'femenino', '1997-09-30', NULL, '', NULL, 'ana@example.com', 0, 0, '', 'estudiante', '', '2025-07-26 19:43:01', 1, NULL, NULL),
(5, 'Carlos', NULL, 'Sanchez', NULL, '1242201022', 'masculino', '2001-04-12', NULL, '', NULL, 'carlos@example.com', 0, 0, '', 'estudiante', '', '2025-07-26 19:43:01', 1, NULL, NULL),
(6, 'Laura', NULL, 'Diaz', NULL, '1772990898', 'femenino', '1996-11-02', NULL, '', NULL, 'laura@example.com', 0, 0, '', 'estudiante', '', '2025-07-26 19:43:01', 1, NULL, NULL),
(7, 'Andres', NULL, 'Torres', NULL, '1003672982', 'masculino', '1998-03-18', NULL, '', NULL, 'andres@example.com', 0, 0, '', 'estudiante', '', '2025-07-26 19:43:01', 1, NULL, NULL),
(8, 'Valeria', NULL, 'Castro', NULL, '1003565528', 'femenino', '2000-12-27', NULL, '', NULL, 'valeria@example.com', 0, 0, '', 'estudiante', '', '2025-07-26 19:43:01', 1, NULL, NULL),
(9, 'Diego', NULL, 'Ruiz', NULL, '1828835920', 'masculino', '1995-06-01', NULL, '', NULL, 'diego@example.com', 0, 0, '', 'estudiante', '', '2025-07-26 19:43:01', 1, NULL, NULL),
(10, 'Isabella', NULL, 'Moreno', NULL, '1257201278', 'femenino', '1999-10-05', NULL, '', NULL, 'isabella@example.com', 0, 0, '', 'estudiante', '', '2025-07-26 19:43:01', 1, NULL, NULL),
(11, 'Juan', 'Camilo', 'Cuenca', 'Sepulveda', '1004779025', 'masculino', '2003-09-18', 'Quindío', 'Armenia', '3243978514', 'camilocuenca1810@gmail.com', 0, 0, 'crr 23 # 7 19', 'estudiante', '$2y$10$IOlh3o2lm8DVgmdehQWcvuWevFz72bndO3x5uCDE6cJRrVzGtDEnO', '2025-07-26 20:46:24', 0, 'ECF83C', NULL),
(14, 'Juan', 'Camilo', 'qwdqwd', 'qwdqwdqwd', '1234567890', '', '2004-09-18', 'La Guajira', 'Riohacha', '3243978512', 'juan.lopez@example.com', 0, 0, 'crr 23 # 7 15', 'estudiante', '$2y$10$pRIbG5tDphKIeIHq7aTJ3.U2KxGk8e8EOJsn7xWH/I36M6FpLSiTG', '2025-07-26 21:10:00', 0, 'CBBD8D', '2025-07-26 21:18:19');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `wp_aess_attendance`
--
ALTER TABLE `wp_aess_attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`user_id`,`course_id`,`attendance_date`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indices de la tabla `wp_aess_courses`
--
ALTER TABLE `wp_aess_courses`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `wp_aess_course_groups`
--
ALTER TABLE `wp_aess_course_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indices de la tabla `wp_aess_enrollments`
--
ALTER TABLE `wp_aess_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indices de la tabla `wp_aess_users`
--
ALTER TABLE `wp_aess_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `national_id` (`national_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_codigo_activacion` (`codigo_activacion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `wp_aess_attendance`
--
ALTER TABLE `wp_aess_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `wp_aess_courses`
--
ALTER TABLE `wp_aess_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `wp_aess_course_groups`
--
ALTER TABLE `wp_aess_course_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `wp_aess_enrollments`
--
ALTER TABLE `wp_aess_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `wp_aess_users`
--
ALTER TABLE `wp_aess_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `wp_aess_attendance`
--
ALTER TABLE `wp_aess_attendance`
  ADD CONSTRAINT `wp_aess_attendance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `wp_aess_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wp_aess_attendance_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `wp_aess_courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wp_aess_attendance_ibfk_3` FOREIGN KEY (`group_id`) REFERENCES `wp_aess_course_groups` (`id`);

--
-- Filtros para la tabla `wp_aess_course_groups`
--
ALTER TABLE `wp_aess_course_groups`
  ADD CONSTRAINT `wp_aess_course_groups_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `wp_aess_courses` (`id`);

--
-- Filtros para la tabla `wp_aess_enrollments`
--
ALTER TABLE `wp_aess_enrollments`
  ADD CONSTRAINT `wp_aess_enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `wp_aess_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wp_aess_enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `wp_aess_courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wp_aess_enrollments_ibfk_3` FOREIGN KEY (`group_id`) REFERENCES `wp_aess_course_groups` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
