-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Авг 15 2024 г., 10:37
-- Версия сервера: 10.3.39-MariaDB-0ubuntu0.20.04.2
-- Версия PHP: 7.4.3-4ubuntu2.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `bot`
--

-- --------------------------------------------------------

--
-- Структура таблицы `bans`
--

CREATE TABLE `bans` (
  `id` int(255) NOT NULL,
  `user_id` int(255) NOT NULL,
  `chat_id` int(255) NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `bans`
--


-- --------------------------------------------------------

--
-- Структура таблицы `chats`
--

CREATE TABLE `chats` (
  `id` int(255) NOT NULL,
  `peer_id` int(255) NOT NULL,
  `is_active` int(255) NOT NULL,
  `last_active` int(255) NOT NULL,
  `date` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `chats`
--

INSERT INTO `chats` (`id`, `peer_id`, `is_active`, `last_active`, `date`) VALUES
(2, 2000000007, 1, 1723632675, 1693300922),
(8, 2000000001, 0, 1694419131, 1694419131),
(9, 2000000008, 0, 1694768337, 1694768337),
(10, 662084466, 0, 1695417927, 1695417927),
(11, 2000000009, 0, 1697096780, 1697096780),
(12, 817541757, 0, 1704220476, 1704220476),
(13, 717591626, 0, 1705324804, 1705324804),
(14, 2000000010, 0, 1706198404, 1706198404),
(15, 803951573, 0, 1706198461, 1706198461),
(16, 2000000011, 0, 1706379137, 1706379137),
(17, 2000000012, 0, 1708154772, 1708154772),
(18, 2000000013, 0, 1714046187, 1714046187),
(19, 2000000014, 0, 1714046188, 1714046188);

-- --------------------------------------------------------

--
-- Структура таблицы `chat_admins`
--

CREATE TABLE `chat_admins` (
  `id` int(255) NOT NULL,
  `user_id` int(255) NOT NULL,
  `chat_id` int(255) NOT NULL,
  `added_id` int(255) NOT NULL,
  `date` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `control_chats`
--

CREATE TABLE `control_chats` (
  `id` int(255) NOT NULL,
  `local_chat_id` int(255) NOT NULL,
  `security` int(11) NOT NULL DEFAULT 0,
  `links` int(11) NOT NULL DEFAULT 0,
  `invites` int(11) NOT NULL DEFAULT 0,
  `bots` int(11) DEFAULT 0,
  `nude_security` int(255) NOT NULL DEFAULT 0,
  `censor` int(11) NOT NULL DEFAULT 0,
  `repost` int(11) NOT NULL DEFAULT 0,
  `added_usr_id` int(255) NOT NULL,
  `date_add` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `control_chats`
--



-- --------------------------------------------------------

--
-- Структура таблицы `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `chat_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `count` int(11) NOT NULL,
  `preview` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `items`
--


-- --------------------------------------------------------

--
-- Структура таблицы `kick_logs`
--

CREATE TABLE `kick_logs` (
  `id` int(255) NOT NULL,
  `user_id` int(255) NOT NULL,
  `admin_id` int(255) NOT NULL,
  `chat_id` int(255) NOT NULL,
  `date` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `kick_logs`
--


-- --------------------------------------------------------

--
-- Структура таблицы `kick_polls`
--

CREATE TABLE `kick_polls` (
  `id` int(255) NOT NULL,
  `chat_id` int(255) NOT NULL,
  `author_id` int(255) NOT NULL,
  `kick_usr_id` int(255) NOT NULL,
  `needed_votes` int(255) NOT NULL,
  `current_votes` int(255) NOT NULL,
  `reresolved` int(11) NOT NULL DEFAULT 0,
  `date_create` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `kick_poll_votes`
--

CREATE TABLE `kick_poll_votes` (
  `id` int(255) NOT NULL,
  `poll_id` int(255) NOT NULL,
  `author_id` int(255) NOT NULL,
  `date` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `last_activity`
--

CREATE TABLE `last_activity` (
  `id` int(255) NOT NULL,
  `user_id` int(255) NOT NULL,
  `chat_id` int(255) NOT NULL,
  `date_last_acivity` int(255) NOT NULL,
  `date` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `last_activity`
--

INSERT INTO `last_activity` (`id`, `user_id`, `chat_id`, `date_last_acivity`, `date`) VALUES


-- --------------------------------------------------------

--
-- Структура таблицы `message_logs`
--

CREATE TABLE `message_logs` (
  `id` int(255) NOT NULL,
  `from_id` int(255) NOT NULL,
  `chat_id` int(255) NOT NULL,
  `text` text NOT NULL,
  `len_text` int(255) NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `message_logs`
--

INSERT INTO `message_logs` (`id`, `from_id`, `chat_id`, `text`, `len_text`, `date`) VALUES


-- --------------------------------------------------------

--
-- Структура таблицы `mute`
--

CREATE TABLE `mute` (
  `id` int(11) NOT NULL,
  `user_id` int(255) NOT NULL,
  `chat_id` int(255) NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `mute`
--



-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `chat_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `money` int(30) NOT NULL DEFAULT 50,
  `rep` int(11) NOT NULL DEFAULT 0,
  `rang` int(11) NOT NULL DEFAULT 1,
  `count_mess` int(100) NOT NULL DEFAULT 0,
  `last_rep` int(100) NOT NULL DEFAULT 0,
  `last_bonus` int(100) NOT NULL DEFAULT 0,
  `last_drink` int(100) NOT NULL DEFAULT 0,
  `last_gift` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--


-- --------------------------------------------------------

--
-- Структура таблицы `users_nick`
--

CREATE TABLE `users_nick` (
  `id` int(255) NOT NULL,
  `chat_id` int(255) NOT NULL,
  `user_id` int(255) NOT NULL,
  `nick` varchar(255) NOT NULL,
  `date` bigint(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users_nick`
--


-- --------------------------------------------------------

--
-- Структура таблицы `warn`
--

CREATE TABLE `warn` (
  `id` int(11) NOT NULL,
  `user_id` int(255) NOT NULL,
  `chat_id` int(255) NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `warn`
--


--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `bans`
--
ALTER TABLE `bans`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `chat_admins`
--
ALTER TABLE `chat_admins`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `control_chats`
--
ALTER TABLE `control_chats`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `kick_logs`
--
ALTER TABLE `kick_logs`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `kick_polls`
--
ALTER TABLE `kick_polls`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `kick_poll_votes`
--
ALTER TABLE `kick_poll_votes`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `last_activity`
--
ALTER TABLE `last_activity`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `message_logs`
--
ALTER TABLE `message_logs`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `mute`
--
ALTER TABLE `mute`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users_nick`
--
ALTER TABLE `users_nick`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `warn`
--
ALTER TABLE `warn`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `bans`
--
ALTER TABLE `bans`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT для таблицы `chats`
--
ALTER TABLE `chats`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT для таблицы `chat_admins`
--
ALTER TABLE `chat_admins`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `control_chats`
--
ALTER TABLE `control_chats`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=194;

--
-- AUTO_INCREMENT для таблицы `kick_logs`
--
ALTER TABLE `kick_logs`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `kick_polls`
--
ALTER TABLE `kick_polls`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `kick_poll_votes`
--
ALTER TABLE `kick_poll_votes`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `last_activity`
--
ALTER TABLE `last_activity`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=212;

--
-- AUTO_INCREMENT для таблицы `message_logs`
--
ALTER TABLE `message_logs`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6983;

--
-- AUTO_INCREMENT для таблицы `mute`
--
ALTER TABLE `mute`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=318;

--
-- AUTO_INCREMENT для таблицы `users_nick`
--
ALTER TABLE `users_nick`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `warn`
--
ALTER TABLE `warn`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=281;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
