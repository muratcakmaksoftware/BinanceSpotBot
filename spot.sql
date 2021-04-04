-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 04 Nis 2021, 10:21:51
-- Sunucu sürümü: 10.4.17-MariaDB
-- PHP Sürümü: 7.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `spot`
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
(1, 'ADA', '0.00500', '0.00500'),
(2, 'TRX', '0.00500', '0.00500'),
(3, 'DOGE', '0.00010', '0.00500');

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

--
-- Tablo döküm verisi `logs`
--

INSERT INTO `logs` (`id`, `type`, `coin_id`, `title`, `description`, `created_at`, `updated_at`) VALUES
(769, 2, 3, 'Limit Status Error', 'Limit Emrinin Kontrolü Başarısız. Detay: signedRequest error: {\"code\":-2013,\"msg\":\"Order does not exist.\"}', '2021-04-03 14:34:14', '2021-04-03 14:34:14'),
(770, 2, 3, 'Limit Status Error', 'Limit Emrinin Kontrolü Başarısız. Detay: signedRequest error: {\"code\":-2013,\"msg\":\"Order does not exist.\"}', '2021-04-03 14:55:36', '2021-04-03 14:55:36'),
(771, 2, 3, 'sellCoin Limit Error', 'Satış Yapma Limit Başarısız. Detay: signedRequest error: {\"code\":-2010,\"msg\":\"Account has insufficient balance for requested action.\"}', '2021-04-03 14:55:39', '2021-04-03 14:55:39');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `orders`
--

CREATE TABLE `orders` (
  `id` bigint(11) NOT NULL,
  `coin_id` int(11) NOT NULL,
  `orderId` bigint(20) NOT NULL,
  `symbol` varchar(255) NOT NULL,
  `side` varchar(255) NOT NULL,
  `origQty` decimal(13,8) NOT NULL DEFAULT 0.00000000,
  `price` decimal(13,8) NOT NULL DEFAULT 0.00000000,
  `type` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `var_piece` decimal(13,8) NOT NULL DEFAULT 0.00000000,
  `var_price` decimal(13,8) NOT NULL DEFAULT 0.00000000,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tablo döküm verisi `orders`
--

INSERT INTO `orders` (`id`, `coin_id`, `orderId`, `symbol`, `side`, `origQty`, `price`, `type`, `status`, `var_piece`, `var_price`, `created_at`, `updated_at`) VALUES
(2, 3, 431446770, 'DOGEUSDT', 'BUY', '188.00000000', '0.05836650', 'LIMIT', 'FILLED', '188.00000000', '0.05836650', '2021-04-03 10:46:30', '2021-04-03 10:46:38'),
(3, 3, 431446875, 'DOGEUSDT', 'SELL', '188.00000000', '0.05872660', 'LIMIT', 'FILLED', '188.00000000', '0.05872660', '2021-04-03 10:46:38', '2021-04-03 10:46:38'),
(4, 3, 431777481, 'DOGEUSDT', 'BUY', '357.00000000', '0.05875700', 'LIMIT', 'FILLED', '357.00000000', '0.05875700', '2021-04-03 14:34:10', '2021-04-03 14:34:13'),
(5, 3, 431777543, 'DOGEUSDT', 'SELL', '357.00000000', '0.05900000', 'LIMIT', 'FILLED', '357.00000000', '0.05900000', '2021-04-03 14:34:13', '2021-04-03 14:34:13'),
(6, 3, 431816035, 'DOGEUSDT', 'BUY', '355.00000000', '0.05905000', 'LIMIT', 'FILLED', '355.00000000', '0.05905000', '2021-04-03 14:55:35', '2021-04-03 14:55:38');

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
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `coins`
--
ALTER TABLE `coins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `logs`
--
ALTER TABLE `logs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=772;

--
-- Tablo için AUTO_INCREMENT değeri `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
