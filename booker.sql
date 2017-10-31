-- phpMyAdmin SQL Dump
-- version 4.4.15.7
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1:3306
-- Время создания: Окт 31 2017 г., 16:25
-- Версия сервера: 5.7.13
-- Версия PHP: 5.6.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `booker`
--

-- --------------------------------------------------------

--
-- Структура таблицы `booker_events`
--

CREATE TABLE IF NOT EXISTS `booker_events` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `room_id` bigint(20) NOT NULL,
  `desc` text NOT NULL,
  `start` bigint(20) NOT NULL,
  `end` bigint(20) NOT NULL,
  `created` bigint(20) NOT NULL,
  `event_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `booker_rooms`
--

CREATE TABLE IF NOT EXISTS `booker_rooms` (
  `id` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `booker_rooms`
--

INSERT INTO `booker_rooms` (`id`, `name`) VALUES
(1, 'Room 1'),
(2, 'Room 2'),
(3, 'Room 3');

-- --------------------------------------------------------

--
-- Структура таблицы `booker_users`
--

CREATE TABLE IF NOT EXISTS `booker_users` (
  `id` bigint(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(32) NOT NULL,
  `hash` varchar(32) NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `booker_users`
--

INSERT INTO `booker_users` (`id`, `name`, `email`, `password`, `hash`, `admin`) VALUES
(5, 'Lambur', 'lamburii@gmail.com', 'b3084c0c110ebdb50d3308725fb29f3e', 'c03088362afee1d4afcd12b01711906f', 1),
(6, 'Lambura', 'lambur@gmail.com', 'b3084c0c110ebdb50d3308725fb29f3e', '5999b111264a0f51a5b0d45704b83755', 0);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `booker_events`
--
ALTER TABLE `booker_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booker_events_fk0` (`user_id`),
  ADD KEY `booker_events_fk1` (`room_id`);

--
-- Индексы таблицы `booker_rooms`
--
ALTER TABLE `booker_rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `booker_users`
--
ALTER TABLE `booker_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `hash` (`hash`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `booker_events`
--
ALTER TABLE `booker_events`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `booker_rooms`
--
ALTER TABLE `booker_rooms`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT для таблицы `booker_users`
--
ALTER TABLE `booker_users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `booker_events`
--
ALTER TABLE `booker_events`
  ADD CONSTRAINT `booker_events_fk0` FOREIGN KEY (`user_id`) REFERENCES `booker_users` (`id`),
  ADD CONSTRAINT `booker_events_fk1` FOREIGN KEY (`room_id`) REFERENCES `booker_rooms` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
