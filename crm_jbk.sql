-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-08-2024 a las 01:14:43
-- Versión del servidor: 10.4.22-MariaDB
-- Versión de PHP: 7.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `crm_jbk`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `companies`
--

CREATE TABLE `companies` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `short_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `document` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `pais` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `contact` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `asist_type` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `sufijo` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `limit` int(11) DEFAULT NULL,
  `caducidad` date DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at_user` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `updated_at_user` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `companies`
--

INSERT INTO `companies` (`id`, `name`, `short_name`, `document`, `pais`, `contact`, `asist_type`, `sufijo`, `limit`, `caducidad`, `logo`, `state`, `created_at`, `updated_at`, `created_at_user`, `updated_at_user`, `deleted_at`) VALUES
(1, 'Empresa1', NULL, NULL, 'Chile', '123B2', 'Login/Logout al Sistema', '@dominio.com', NULL, NULL, NULL, NULL, NULL, '2024-08-07 17:19:51', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `days`
--

CREATE TABLE `days` (
  `id` bigint(20) NOT NULL,
  `horario_id` bigint(20) NOT NULL,
  `day` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `inicio` time DEFAULT NULL,
  `final` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at_user` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `updated_at_user` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `days`
--

INSERT INTO `days` (`id`, `horario_id`, `day`, `inicio`, `final`, `created_at`, `updated_at`, `created_at_user`, `updated_at_user`, `deleted_at`) VALUES
(71, 8, 'Lunes', '11:22:00', '22:02:00', '2024-08-09 23:01:55', '2024-08-09 23:02:22', NULL, NULL, '2024-08-09 23:02:22'),
(72, 8, 'Martes', '03:33:00', '04:44:00', '2024-08-09 23:01:55', '2024-08-09 23:02:22', NULL, NULL, '2024-08-09 23:02:22'),
(73, 8, 'Miércoles', '05:55:00', '06:59:00', '2024-08-09 23:01:55', '2024-08-09 23:02:22', NULL, NULL, '2024-08-09 23:02:22'),
(74, 8, 'Jueves', '07:59:00', '08:59:00', '2024-08-09 23:01:55', '2024-08-09 23:02:22', NULL, NULL, '2024-08-09 23:02:22'),
(75, 8, 'Viernes', '09:59:00', '06:59:00', '2024-08-09 23:01:55', '2024-08-09 23:02:22', NULL, NULL, '2024-08-09 23:02:22'),
(76, 8, 'Sábado', '05:55:00', '04:44:00', '2024-08-09 23:01:55', '2024-08-09 23:02:22', NULL, NULL, '2024-08-09 23:02:22'),
(77, 8, 'Domingo', '03:33:00', '22:22:00', '2024-08-09 23:01:55', '2024-08-09 23:02:22', NULL, NULL, '2024-08-09 23:02:22'),
(78, 8, 'Lunes', '11:22:00', '22:02:00', '2024-08-09 23:02:22', '2024-08-09 23:02:22', NULL, NULL, NULL),
(79, 8, 'Martes', '03:33:00', '04:44:00', '2024-08-09 23:02:22', '2024-08-09 23:02:22', NULL, NULL, NULL),
(80, 8, 'Miércoles', '05:55:00', '06:59:00', '2024-08-09 23:02:22', '2024-08-09 23:02:22', NULL, NULL, NULL),
(81, 8, 'Jueves', '07:59:00', '08:59:00', '2024-08-09 23:02:22', '2024-08-09 23:02:22', NULL, NULL, NULL),
(82, 8, 'Viernes', '09:59:00', '06:59:00', '2024-08-09 23:02:22', '2024-08-09 23:02:22', NULL, NULL, NULL),
(83, 8, 'Sábado', '05:55:00', '04:44:00', '2024-08-09 23:02:22', '2024-08-09 23:02:22', NULL, NULL, NULL),
(84, 8, 'Domingo', '03:33:00', '22:22:00', '2024-08-09 23:02:22', '2024-08-09 23:02:22', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `des_types`
--

CREATE TABLE `des_types` (
  `id` bigint(20) NOT NULL,
  `sede_id` bigint(20) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at_user` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `updated_at_user` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `groups`
--

CREATE TABLE `groups` (
  `id` bigint(20) NOT NULL,
  `company_id` bigint(20) DEFAULT NULL,
  `campana_id` bigint(20) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `ip` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `horario_id` bigint(20) DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  `perfil_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at_user` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `updated_at_user` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `groups`
--

INSERT INTO `groups` (`id`, `company_id`, `campana_id`, `name`, `ip`, `horario_id`, `state`, `perfil_id`, `created_at`, `updated_at`, `created_at_user`, `updated_at_user`, `deleted_at`) VALUES
(1, 1, 1, 'GRUPO 1', '123123123', 1, NULL, 1, NULL, NULL, NULL, NULL, NULL),
(2, 1, 1, 'GRUPO2', '123123222', NULL, NULL, NULL, NULL, '2024-08-09 05:51:12', NULL, NULL, '2024-08-09 05:51:12'),
(3, 1, 1, 'grupo3', '44444', NULL, NULL, NULL, NULL, '2024-08-09 05:49:51', NULL, NULL, '2024-08-09 05:49:51'),
(4, 1, 1, 'GRUPO4', 'CCCCC', NULL, NULL, NULL, NULL, '2024-08-09 05:50:50', NULL, NULL, '2024-08-09 05:50:50'),
(5, 1, NULL, 'aaa', 'bbb', NULL, NULL, NULL, '2024-08-09 05:53:18', '2024-08-09 05:56:53', NULL, NULL, '2024-08-09 05:56:53'),
(6, 1, NULL, 'ffffA', 'eeeB', NULL, NULL, NULL, '2024-08-09 05:53:44', '2024-08-09 05:54:04', NULL, NULL, '2024-08-09 05:54:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

CREATE TABLE `horarios` (
  `id` bigint(20) NOT NULL,
  `sede_id` bigint(20) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `state` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `tolerancia_min` int(11) DEFAULT NULL,
  `motivo_tardanza` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `motivo_temprano` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `restringir_last` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `restringir_gest` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at_user` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `updated_at_user` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `horarios`
--

INSERT INTO `horarios` (`id`, `sede_id`, `name`, `state`, `tolerancia_min`, `motivo_tardanza`, `motivo_temprano`, `restringir_last`, `restringir_gest`, `created_at`, `updated_at`, `created_at_user`, `updated_at_user`, `deleted_at`) VALUES
(8, NULL, 'HORARIO1', 'Activo', 200, '1', '0', '1', '1', '2024-08-09 23:01:55', '2024-08-09 23:02:22', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perfils`
--

CREATE TABLE `perfils` (
  `id` bigint(20) NOT NULL,
  `user_group_id` bigint(20) DEFAULT NULL,
  `permision_id` bigint(20) DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at_user` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `updated_at_user` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisions`
--

CREATE TABLE `permisions` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at_user` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `updated_at_user` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sedes`
--

CREATE TABLE `sedes` (
  `id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at_user` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `updated_at_user` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `telefono` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `genero` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_naci` date DEFAULT NULL,
  `group_perfil_id` int(11) DEFAULT NULL,
  `obs` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pass_change` int(11) DEFAULT NULL,
  `document_type_id` int(11) DEFAULT NULL,
  `document_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `bank_account` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_cci` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_cese` date DEFAULT NULL,
  `fecha_inicap` date DEFAULT NULL,
  `fecha_fincap` date DEFAULT NULL,
  `foto_perfil` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_doc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `curriculum` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contrato` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `user`, `user_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `telefono`, `genero`, `fecha_naci`, `group_perfil_id`, `obs`, `pass_change`, `document_type_id`, `document_number`, `bank_id`, `bank_account`, `bank_cci`, `fecha_inicio`, `fecha_cese`, `fecha_inicap`, `fecha_fincap`, `foto_perfil`, `foto_doc`, `curriculum`, `contrato`) VALUES
(1, 'Gino Sanchez', 'admin@dominio.com', NULL, '$2y$10$cDTHe3hLLQEyJ2E0bq95leT44Sk0BzOu/mb/XXXc/X/jFvIKXuIjS', '12123', '2019-10-05 01:32:02', '2024-08-09 14:23:27', '123123', 'Femenino', '2323-03-22', NULL, 'SSSS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(37, 'DANFEER balbin santiagoA', 'danfeerbsB@dominio.com', NULL, '123', '123', '2024-08-09 04:51:15', '2024-08-09 05:00:56', '918521375C', 'Femenino', '2002-05-03', NULL, 'ningunoD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_groups`
--

CREATE TABLE `user_groups` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `group_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at_user` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `updated_at_user` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `user_groups`
--

INSERT INTO `user_groups` (`id`, `user_id`, `group_id`, `created_at`, `updated_at`, `created_at_user`, `updated_at_user`, `deleted_at`) VALUES
(13, 1, 1, NULL, '2024-08-08 18:38:50', NULL, NULL, '0000-00-00 00:00:00'),
(14, 1, 1, NULL, '2024-08-08 18:43:17', NULL, NULL, '2024-08-08 18:43:17'),
(15, 36, 1, NULL, '2024-08-08 18:43:59', NULL, NULL, '2024-08-08 18:43:59'),
(16, 36, 1, NULL, '2024-08-08 18:44:49', NULL, NULL, '2024-08-08 18:44:49'),
(17, 36, 1, NULL, '2024-08-08 18:45:31', NULL, NULL, '2024-08-08 18:45:31'),
(18, 36, 1, NULL, '2024-08-08 18:47:44', NULL, NULL, '2024-08-08 18:47:44'),
(19, 36, 2, NULL, '2024-08-09 02:45:46', NULL, NULL, '2024-08-09 02:45:46'),
(20, 36, 3, NULL, '2024-08-08 18:48:00', NULL, NULL, '2024-08-08 18:48:00'),
(21, 36, 3, NULL, '2024-08-09 02:58:22', NULL, NULL, '2024-08-09 02:58:22'),
(22, 36, 3, NULL, '2024-08-09 03:17:02', NULL, NULL, '2024-08-09 03:17:02'),
(23, 36, 2, NULL, '2024-08-09 04:15:33', NULL, NULL, '2024-08-09 04:15:33'),
(24, 36, 4, NULL, '2024-08-09 04:16:37', NULL, NULL, '2024-08-09 04:16:37'),
(25, 36, 1, '2024-08-09 04:29:39', '2024-08-09 04:31:22', NULL, NULL, '2024-08-09 04:31:22'),
(28, 36, 4, '2024-08-09 04:29:59', '2024-08-09 04:29:59', NULL, NULL, NULL),
(29, 36, 2, '2024-08-09 04:31:26', '2024-08-09 04:31:47', NULL, NULL, '2024-08-09 04:31:47'),
(30, 36, 3, '2024-08-09 04:31:34', '2024-08-09 04:31:34', NULL, NULL, NULL),
(31, 36, 4, '2024-08-09 04:31:54', '2024-08-09 04:32:11', NULL, NULL, '2024-08-09 04:32:11'),
(32, 1, 1, '2024-08-09 04:32:51', '2024-08-09 04:32:51', NULL, NULL, NULL),
(33, 37, 1, '2024-08-09 05:03:39', '2024-08-09 05:11:20', NULL, NULL, '2024-08-09 05:11:20'),
(34, 37, 2, '2024-08-09 05:03:43', '2024-08-09 05:11:22', NULL, NULL, '2024-08-09 05:11:22'),
(35, 37, 2, '2024-08-09 05:06:56', '2024-08-09 05:11:23', NULL, NULL, '2024-08-09 05:11:23'),
(36, 37, 1, '2024-08-09 05:10:48', '2024-08-09 05:11:25', NULL, NULL, '2024-08-09 05:11:25'),
(37, 37, 1, NULL, '2024-08-09 05:21:15', NULL, NULL, '2024-08-09 05:21:15'),
(38, 37, 2, '2024-08-09 05:20:40', '2024-08-09 05:20:52', NULL, NULL, '2024-08-09 05:20:52'),
(39, 37, 2, '2024-08-09 05:20:55', '2024-08-09 05:20:55', NULL, NULL, NULL),
(40, 37, 3, '2024-08-09 05:21:13', '2024-08-09 05:21:13', NULL, NULL, NULL),
(41, 37, 1, '2024-08-09 05:21:17', '2024-08-09 05:21:21', NULL, NULL, '2024-08-09 05:21:21'),
(42, 37, 1, '2024-08-09 05:21:23', '2024-08-09 05:21:23', NULL, NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indices de la tabla `days`
--
ALTER TABLE `days`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indices de la tabla `des_types`
--
ALTER TABLE `des_types`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indices de la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indices de la tabla `perfils`
--
ALTER TABLE `perfils`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indices de la tabla `permisions`
--
ALTER TABLE `permisions`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indices de la tabla `sedes`
--
ALTER TABLE `sedes`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `users_user_unique` (`user`) USING BTREE;

--
-- Indices de la tabla `user_groups`
--
ALTER TABLE `user_groups`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `days`
--
ALTER TABLE `days`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT de la tabla `des_types`
--
ALTER TABLE `des_types`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `groups`
--
ALTER TABLE `groups`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `perfils`
--
ALTER TABLE `perfils`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permisions`
--
ALTER TABLE `permisions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sedes`
--
ALTER TABLE `sedes`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `user_groups`
--
ALTER TABLE `user_groups`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
