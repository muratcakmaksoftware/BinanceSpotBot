-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 14 Şub 2022, 19:58:42
-- Sunucu sürümü: 10.4.17-MariaDB
-- PHP Sürümü: 7.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `spotbot`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `coins`
--

CREATE TABLE `coins` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `profit` decimal(10,5) DEFAULT NULL,
  `purchase` decimal(10,5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tablo döküm verisi `coins`
--

INSERT INTO `coins` (`id`, `name`, `profit`, `purchase`) VALUES
(1, 'ADA', '0.00850', '0.00150'),
(2, 'TRX', '0.00800', '0.00400'),
(3, 'DOGE', '0.00010', '0.00400'),
(4, 'VET', '0.00800', '0.00400'),
(5, 'XRP', '0.00800', '0.00400'),
(6, 'MATIC', '0.00800', '0.00150');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `logs`
--

CREATE TABLE `logs` (
  `id` bigint(20) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `coin_id` int(11) DEFAULT NULL,
  `title` text DEFAULT NULL,
  `description` text NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `orders`
--

CREATE TABLE `orders` (
  `id` bigint(11) NOT NULL,
  `unique_id` varchar(255) DEFAULT NULL,
  `coin_id` int(11) NOT NULL,
  `orderId` bigint(20) NOT NULL,
  `symbol` varchar(255) NOT NULL,
  `side` varchar(255) NOT NULL,
  `origQty` decimal(13,8) NOT NULL DEFAULT 0.00000000,
  `price` decimal(13,8) NOT NULL DEFAULT 0.00000000,
  `type` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `fee` decimal(13,8) NOT NULL DEFAULT 0.00000000,
  `total` decimal(13,8) NOT NULL DEFAULT 0.00000000,
  `var_piece` decimal(13,8) DEFAULT 0.00000000,
  `var_price` decimal(13,8) DEFAULT 0.00000000,
  `json_data` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `order_logs`
--

CREATE TABLE `order_logs` (
  `id` bigint(20) NOT NULL,
  `unique_id` varchar(255) DEFAULT NULL,
  `orderId` bigint(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tablo döküm verisi `order_logs`
--

INSERT INTO `order_logs` (`id`, `unique_id`, `orderId`, `description`, `created_at`, `updated_at`) VALUES
(2388, NULL, NULL, '6', '2021-10-29 07:51:04', '2021-10-29 07:51:04'),
(2389, NULL, NULL, 'MATIC', '2021-10-29 07:51:04', '2021-10-29 07:51:04'),
(2390, NULL, NULL, 'USDT', '2021-10-29 07:51:04', '2021-10-29 07:51:04'),
(2391, NULL, NULL, 'MATICUSDT', '2021-10-29 07:51:04', '2021-10-29 07:51:04'),
(2392, NULL, NULL, '0.00800 USDT', '2021-10-29 07:51:04', '2021-10-29 07:51:04'),
(2393, NULL, NULL, '0.00150', '2021-10-29 07:51:04', '2021-10-29 07:51:04'),
(2394, '526824759617ba7e8609031.60563416', NULL, '-------------------------------', '2021-10-29 07:51:04', '2021-10-29 07:51:04'),
(2395, '526824759617ba7e8609031.60563416', NULL, '1-SPOT ALGORITHM START: 29.10.2021 07:51:04', '2021-10-29 07:51:04', '2021-10-29 07:51:04'),
(2396, '526824759617ba7e8609031.60563416', NULL, '1-Önceden Yapılmış Siparişin Bitmesi Bekleniyor...', '2021-10-29 07:51:04', '2021-10-29 07:51:04'),
(2397, NULL, NULL, '6', '2021-10-29 07:51:57', '2021-10-29 07:51:57'),
(2398, NULL, NULL, 'MATIC', '2021-10-29 07:51:57', '2021-10-29 07:51:57'),
(2399, NULL, NULL, 'USDT', '2021-10-29 07:51:57', '2021-10-29 07:51:57'),
(2400, NULL, NULL, 'MATICUSDT', '2021-10-29 07:51:57', '2021-10-29 07:51:57'),
(2401, NULL, NULL, '0.00800 USDT', '2021-10-29 07:51:57', '2021-10-29 07:51:57'),
(2402, NULL, NULL, '0.00150', '2021-10-29 07:51:57', '2021-10-29 07:51:57'),
(2403, '11263975617ba81dddb5c4.16310081', NULL, '-------------------------------', '2021-10-29 07:51:57', '2021-10-29 07:51:57'),
(2404, '11263975617ba81dddb5c4.16310081', NULL, '1-SPOT ALGORITHM START: 29.10.2021 07:51:57', '2021-10-29 07:51:57', '2021-10-29 07:51:57'),
(2405, '11263975617ba81dddb5c4.16310081', NULL, '1-Önceden Yapılmış Siparişin Bitmesi Bekleniyor...', '2021-10-29 07:51:57', '2021-10-29 07:51:57'),
(2406, NULL, NULL, '6', '2021-10-29 08:08:58', '2021-10-29 08:08:58'),
(2407, NULL, NULL, 'MATIC', '2021-10-29 08:08:58', '2021-10-29 08:08:58'),
(2408, NULL, NULL, 'USDT', '2021-10-29 08:08:58', '2021-10-29 08:08:58'),
(2409, NULL, NULL, 'MATICUSDT', '2021-10-29 08:08:58', '2021-10-29 08:08:58'),
(2410, NULL, NULL, '0.00800 USDT', '2021-10-29 08:08:58', '2021-10-29 08:08:58'),
(2411, NULL, NULL, '0.00150', '2021-10-29 08:08:58', '2021-10-29 08:08:58'),
(2412, '1470074502617bac1a0b5389.56592368', NULL, '-------------------------------', '2021-10-29 08:08:58', '2021-10-29 08:08:58'),
(2413, '1470074502617bac1a0b5389.56592368', NULL, '1-SPOT ALGORITHM START: 29.10.2021 08:08:58', '2021-10-29 08:08:58', '2021-10-29 08:08:58'),
(2414, '1470074502617bac1a0b5389.56592368', NULL, '1-Önceden Yapılmış Siparişin Bitmesi Bekleniyor...', '2021-10-29 08:08:58', '2021-10-29 08:08:58'),
(2415, NULL, NULL, '6', '2021-10-29 08:11:05', '2021-10-29 08:11:05'),
(2416, NULL, NULL, 'MATIC', '2021-10-29 08:11:05', '2021-10-29 08:11:05'),
(2417, NULL, NULL, 'USDT', '2021-10-29 08:11:05', '2021-10-29 08:11:05'),
(2418, NULL, NULL, 'MATICUSDT', '2021-10-29 08:11:05', '2021-10-29 08:11:05'),
(2419, NULL, NULL, '0.00800 USDT', '2021-10-29 08:11:05', '2021-10-29 08:11:05'),
(2420, NULL, NULL, '0.00150', '2021-10-29 08:11:05', '2021-10-29 08:11:05'),
(2421, '1555698858617bac99e74d01.52261467', NULL, '-------------------------------', '2021-10-29 08:11:05', '2021-10-29 08:11:05'),
(2422, '1555698858617bac99e74d01.52261467', NULL, '1-SPOT ALGORITHM START: 29.10.2021 08:11:05', '2021-10-29 08:11:05', '2021-10-29 08:11:05'),
(2423, '1555698858617bac99e74d01.52261467', NULL, '1-Önceden Yapılmış Siparişin Bitmesi Bekleniyor...', '2021-10-29 08:11:05', '2021-10-29 08:11:05'),
(2424, NULL, NULL, 'Önceki ALIM Limiti iptal ediliyor yeni alım limiti koyulacak.', '2021-10-29 08:11:06', '2021-10-29 08:11:06'),
(2425, NULL, NULL, 'Önceki satın alım limit başarıyla iptal edildi!', '2021-10-29 08:11:06', '2021-10-29 08:11:06'),
(2426, '1555698858617bac99e74d01.52261467', NULL, '1-Komisyon: 0.001', '2021-10-29 08:11:09', '2021-10-29 08:11:09'),
(2427, '1555698858617bac99e74d01.52261467', NULL, '1-Cüzdandaki USDT: 100', '2021-10-29 08:11:11', '2021-10-29 08:11:11'),
(2428, '1555698858617bac99e74d01.52261467', NULL, '1-Stabiletesi kontrol ediliyor...', '2021-10-29 08:11:11', '2021-10-29 08:11:11'),
(2429, NULL, NULL, '6', '2021-10-29 08:12:56', '2021-10-29 08:12:56'),
(2430, NULL, NULL, 'MATIC', '2021-10-29 08:12:56', '2021-10-29 08:12:56'),
(2431, NULL, NULL, 'USDT', '2021-10-29 08:12:56', '2021-10-29 08:12:56'),
(2432, NULL, NULL, 'MATICUSDT', '2021-10-29 08:12:56', '2021-10-29 08:12:56'),
(2433, NULL, NULL, '0.00800 USDT', '2021-10-29 08:12:56', '2021-10-29 08:12:56'),
(2434, NULL, NULL, '0.00150', '2021-10-29 08:12:56', '2021-10-29 08:12:56'),
(2435, '1795266911617bad088943e5.50216653', NULL, '-------------------------------', '2021-10-29 08:12:56', '2021-10-29 08:12:56'),
(2436, '1795266911617bad088943e5.50216653', NULL, '1-SPOT ALGORITHM START: 29.10.2021 08:12:56', '2021-10-29 08:12:56', '2021-10-29 08:12:56'),
(2437, '1795266911617bad088943e5.50216653', NULL, '1-Önceden Yapılmış Siparişin Bitmesi Bekleniyor...', '2021-10-29 08:12:56', '2021-10-29 08:12:56'),
(2438, NULL, NULL, 'Önceki ALIM Limiti iptal ediliyor yeni alım limiti koyulacak.', '2021-10-29 08:12:57', '2021-10-29 08:12:57'),
(2439, NULL, NULL, 'Önceki satın alım limit başarıyla iptal edildi!', '2021-10-29 08:12:57', '2021-10-29 08:12:57'),
(2440, '1795266911617bad088943e5.50216653', NULL, '1-Komisyon: 0.001', '2021-10-29 08:13:00', '2021-10-29 08:13:00'),
(2441, '1795266911617bad088943e5.50216653', NULL, '1-Cüzdandaki USDT: 100', '2021-10-29 08:13:01', '2021-10-29 08:13:01'),
(2442, '1795266911617bad088943e5.50216653', NULL, '1-Stabiletesi kontrol ediliyor...', '2021-10-29 08:13:01', '2021-10-29 08:13:01');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `coins`
--
ALTER TABLE `coins`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `order_logs`
--
ALTER TABLE `order_logs`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `coins`
--
ALTER TABLE `coins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `logs`
--
ALTER TABLE `logs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=831;

--
-- Tablo için AUTO_INCREMENT değeri `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- Tablo için AUTO_INCREMENT değeri `order_logs`
--
ALTER TABLE `order_logs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2443;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
