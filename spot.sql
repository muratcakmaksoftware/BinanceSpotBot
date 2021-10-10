-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 10 Eki 2021, 19:18:53
-- Sunucu sürümü: 10.4.18-MariaDB
-- PHP Sürümü: 7.3.27

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
(1, 'ADA', '0.00800', '0.00400'),
(2, 'TRX', '0.00800', '0.00400'),
(3, 'DOGE', '0.00010', '0.00400'),
(4, 'VET', '0.00800', '0.00400'),
(5, 'XRP', '0.00800', '0.00400');

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
(804, 2, 1, 'Limit Status Error', 'Limit Emrinin Kontrolü Başarısız. Detay: signedRequest error: {\"code\":-2013,\"msg\":\"Order does not exist.\"} Satır: 1064', '2021-04-20 17:08:29', '2021-04-20 17:08:29'),
(805, 2, 1, 'Limit Status Error', 'Limit Emrinin Kontrolü Başarısız. Detay: signedRequest error: {\"code\":-2013,\"msg\":\"Order does not exist.\"} Satır: 1064', '2021-04-20 17:08:31', '2021-04-20 17:08:31'),
(806, 2, 1, 'Limit Status Error', 'Limit Emrinin Kontrolü Başarısız. Detay: signedRequest error: {\"code\":-2013,\"msg\":\"Order does not exist.\"} Satır: 1064', '2021-04-20 17:08:37', '2021-04-20 17:08:37'),
(807, 2, 1, 'Limit Status Error', 'Limit Emrinin Kontrolü Başarısız. Detay: signedRequest error: {\"code\":-2013,\"msg\":\"Order does not exist.\"} Satır: 1064', '2021-04-20 17:08:38', '2021-04-20 17:08:38'),
(808, 2, 1, 'Limit Status Error', 'Limit Emrinin Kontrolü Başarısız. Detay: signedRequest error: {\"code\":-2013,\"msg\":\"Order does not exist.\"} Satır: 1064', '2021-04-20 17:08:41', '2021-04-20 17:08:41'),
(809, 2, 1, 'Limit Status Error', 'Limit Emrinin Kontrolü Başarısız. Detay: signedRequest error: {\"code\":-2013,\"msg\":\"Order does not exist.\"} Satır: 1064', '2021-04-20 17:08:50', '2021-04-20 17:08:50'),
(810, 2, 1, 'Limit Status Error', 'Limit Emrinin Kontrolü Başarısız. Detay: signedRequest error: {\"code\":-2013,\"msg\":\"Order does not exist.\"} Satır: 1064', '2021-04-20 17:08:54', '2021-04-20 17:08:54'),
(811, 2, 1, 'Limit Status Error', 'Limit Emrinin Kontrolü Başarısız. Detay: signedRequest error: {\"code\":-2013,\"msg\":\"Order does not exist.\"} Satır: 1064', '2021-04-20 17:08:58', '2021-04-20 17:08:58'),
(812, 2, 1, 'Limit Status Error', 'Limit Emrinin Kontrolü Başarısız. Detay: signedRequest error: {\"code\":-2013,\"msg\":\"Order does not exist.\"} Satır: 1064', '2021-04-20 17:09:00', '2021-04-20 17:09:00'),
(813, 2, 1, 'Limit Status Error', 'Limit Emrinin Kontrolü Başarısız. Detay: signedRequest error: {\"code\":-2013,\"msg\":\"Order does not exist.\"} Satır: 1064', '2021-04-20 17:09:21', '2021-04-20 17:09:21'),
(814, 2, 1, 'Limit Status Error', 'Limit Emrinin Kontrolü Başarısız. Detay: signedRequest error: {\"code\":-2013,\"msg\":\"Order does not exist.\"} Satır: 1064', '2021-04-20 17:09:33', '2021-04-20 17:09:33'),
(815, 2, 1, 'Limit Status Error', 'Limit Emrinin Kontrolü Başarısız. Detay: signedRequest error: {\"code\":-2013,\"msg\":\"Order does not exist.\"} Satır: 1064', '2021-04-20 17:09:39', '2021-04-20 17:09:39'),
(816, 2, 1, 'Limit Status Error', 'Limit Emrinin Kontrolü Başarısız. Detay: signedRequest error: {\"code\":-2013,\"msg\":\"Order does not exist.\"} Satır: 1064', '2021-04-20 17:09:41', '2021-04-20 17:09:41'),
(817, 2, 1, 'Limit Status Error', 'Limit Emrinin Kontrolü Başarısız. Detay: signedRequest error: {\"code\":-2013,\"msg\":\"Order does not exist.\"} Satır: 1064', '2021-04-20 17:09:50', '2021-04-20 17:09:50'),
(818, 2, 1, 'Limit Status Error', 'Limit Emrinin Kontrolü Başarısız. Detay: signedRequest error: {\"code\":-2013,\"msg\":\"Order does not exist.\"} Satır: 1064', '2021-04-20 17:09:53', '2021-04-20 17:09:53'),
(819, 2, 1, 'Limit Status Error', 'Limit Emrinin Kontrolü Başarısız. Detay: signedRequest error: {\"code\":-2013,\"msg\":\"Order does not exist.\"} Satır: 1064', '2021-04-20 19:08:51', '2021-04-20 19:08:51'),
(820, 2, 1, 'Limit Status Error', 'Limit Emrinin Kontrolü Başarısız. Detay: signedRequest error: {\"code\":-2013,\"msg\":\"Order does not exist.\"} Satır: 1064', '2021-04-20 19:08:58', '2021-04-20 19:08:58');

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
  `var_piece` decimal(13,8) DEFAULT 0.00000000,
  `var_price` decimal(13,8) DEFAULT 0.00000000,
  `json_data` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tablo döküm verisi `orders`
--

INSERT INTO `orders` (`id`, `coin_id`, `orderId`, `symbol`, `side`, `origQty`, `price`, `type`, `status`, `var_piece`, `var_price`, `json_data`, `created_at`, `updated_at`) VALUES
(27, 1, 1325399344, 'ADAUSDT', 'BUY', '16.00000000', '1.20895000', 'LIMIT', 'FILLED', '16.00000000', '1.20895000', '{\"symbol\":\"ADAUSDT\",\"orderId\":1325399344,\"orderListId\":-1,\"clientOrderId\":\"wow5gja9oPMcM4I3EClDQF\",\"transactTime\":1618938509759,\"price\":\"1.20895000\",\"origQty\":\"16.00000000\",\"executedQty\":\"0.00000000\",\"cummulativeQuoteQty\":\"0.00000000\",\"status\":\"NEW\",\"timeInForce\":\"GTC\",\"type\":\"LIMIT\",\"side\":\"BUY\",\"fills\":[]}', '2021-04-20 17:08:29', '2021-04-20 17:08:40'),
(28, 1, 1325399911, 'ADAUSDT', 'SELL', '15.90000000', '1.22138000', 'LIMIT', 'FILLED', '15.90000000', '1.22138000', '{\"symbol\":\"ADAUSDT\",\"orderId\":1325399911,\"orderListId\":-1,\"clientOrderId\":\"E7EwnaDXZrPWbqDZ4r2rtO\",\"transactTime\":1618938521597,\"price\":\"1.22138000\",\"origQty\":\"15.90000000\",\"executedQty\":\"0.00000000\",\"cummulativeQuoteQty\":\"0.00000000\",\"status\":\"NEW\",\"timeInForce\":\"GTC\",\"type\":\"LIMIT\",\"side\":\"SELL\",\"fills\":[]}', '2021-04-20 17:08:41', '2021-04-20 18:52:28'),
(29, 1, 1325929216, 'ADAUSDT', 'BUY', '16.00000000', '1.22355000', 'LIMIT', 'FILLED', '16.00000000', '1.22355000', '{\"symbol\":\"ADAUSDT\",\"orderId\":1325929216,\"orderListId\":-1,\"clientOrderId\":\"XLm82XVZmIOKYXqmqtftz4\",\"transactTime\":1618945731394,\"price\":\"1.22355000\",\"origQty\":\"16.00000000\",\"executedQty\":\"0.00000000\",\"cummulativeQuoteQty\":\"0.00000000\",\"status\":\"NEW\",\"timeInForce\":\"GTC\",\"type\":\"LIMIT\",\"side\":\"BUY\",\"fills\":[]}', '2021-04-20 19:08:50', '2021-04-20 19:08:57'),
(30, 1, 1325929562, 'ADAUSDT', 'SELL', '15.90000000', '1.23601000', 'LIMIT', 'FILLED', '15.90000000', '1.23601000', '{\"symbol\":\"ADAUSDT\",\"orderId\":1325929562,\"orderListId\":-1,\"clientOrderId\":\"LGTYy41vkCbSXZmvLak29L\",\"transactTime\":1618945739154,\"price\":\"1.23601000\",\"origQty\":\"15.90000000\",\"executedQty\":\"0.00000000\",\"cummulativeQuoteQty\":\"0.00000000\",\"status\":\"NEW\",\"timeInForce\":\"GTC\",\"type\":\"LIMIT\",\"side\":\"SELL\",\"fills\":[]}', '2021-04-20 19:08:58', '2021-04-20 19:24:06');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `order_logs`
--

CREATE TABLE `order_logs` (
  `id` bigint(20) NOT NULL,
  `unique_id` varchar(255) DEFAULT NULL,
  `orderId` bigint(20) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tablo döküm verisi `order_logs`
--

INSERT INTO `order_logs` (`id`, `unique_id`, `orderId`, `title`, `description`, `created_at`, `updated_at`) VALUES
(585, NULL, NULL, 'Coin ID', '1', '2021-04-20 17:07:39', '2021-04-20 17:07:39'),
(586, NULL, NULL, 'Coin Adı', 'ADA', '2021-04-20 17:07:39', '2021-04-20 17:07:39'),
(587, NULL, NULL, 'Coin USD', 'ADAUSDT', '2021-04-20 17:07:39', '2021-04-20 17:07:39'),
(588, NULL, NULL, 'Coin Sabit Kazanç', '0.00800', '2021-04-20 17:07:39', '2021-04-20 17:07:39'),
(589, NULL, NULL, 'Coin Sürkülasyon Aralığı', '0.00400', '2021-04-20 17:07:39', '2021-04-20 17:07:39'),
(590, '1970013095607f0a5b13f143.34304354', NULL, '', '-------------------------------', '2021-04-20 17:07:39', '2021-04-20 17:07:39'),
(591, '1970013095607f0a5b13f143.34304354', NULL, '', '1-SPOT ALGORITHM START: 20.04.2021 17:07:39', '2021-04-20 17:07:39', '2021-04-20 17:07:39'),
(592, '1970013095607f0a5b13f143.34304354', NULL, '', '1-Önceden Yapılmış Siparişin Bitmesi Bekleniyor...', '2021-04-20 17:07:39', '2021-04-20 17:07:39'),
(593, '1970013095607f0a5b13f143.34304354', NULL, '', '1-Orders Bypass OK!', '2021-04-20 17:07:39', '2021-04-20 17:07:39'),
(594, '1970013095607f0a5b13f143.34304354', NULL, '', '1-Komisyon: 0.001', '2021-04-20 17:07:40', '2021-04-20 17:07:40'),
(595, '1970013095607f0a5b13f143.34304354', NULL, '', '1-Cüzdandaki Dolar: 20', '2021-04-20 17:07:41', '2021-04-20 17:07:41'),
(596, '1970013095607f0a5b13f143.34304354', NULL, '', '1-Stabiletesi kontrol ediliyor...', '2021-04-20 17:07:41', '2021-04-20 17:07:41'),
(597, '1970013095607f0a5b13f143.34304354', NULL, '', '1-Stabiletesi bulunmuş Fiyat: 1.20895', '2021-04-20 17:08:28', '2021-04-20 17:08:28'),
(598, '1970013095607f0a5b13f143.34304354', NULL, 'Satın Alma', '1-Satın Alınacak Fiyat: 1.20895', '2021-04-20 17:08:28', '2021-04-20 17:08:28'),
(599, '1970013095607f0a5b13f143.34304354', NULL, 'Satın Alma', '1-Satın Alınacak Adet: 16', '2021-04-20 17:08:28', '2021-04-20 17:08:28'),
(600, '1970013095607f0a5b13f143.34304354', NULL, 'Satın Alma', '1-Satın Alışda ödenecek dolar: 19.3432', '2021-04-20 17:08:28', '2021-04-20 17:08:28'),
(601, '1970013095607f0a5b13f143.34304354', NULL, 'Satın Alma Limit Koyma', '1-Satın Alma Limiti Koyma = Başlatıldı!', '2021-04-20 17:08:28', '2021-04-20 17:08:28'),
(602, '1970013095607f0a5b13f143.34304354', 27, 'Satın Alma Limit Koyma', '1-Satın Alma Limiti = Başarıyla Koyuldu!', '2021-04-20 17:08:29', '2021-04-20 17:08:29'),
(603, '1970013095607f0a5b13f143.34304354', 27, 'Satın Alma Limit', '1-Satın Alma Limitinin gerçekleşmesi bekleniyor...', '2021-04-20 17:08:29', '2021-04-20 17:08:29'),
(604, '1970013095607f0a5b13f143.34304354', 27, 'Satın Alma Limit', '1-Satın Alma Limiti Başarıyla Gerçekleşti!', '2021-04-20 17:08:40', '2021-04-20 17:08:40'),
(605, '1970013095607f0a5b13f143.34304354', NULL, 'Satın Alım Komisyon', '1-Satın Alma komisyon düşümü için alınan adette düşülecek Adet: 0.016', '2021-04-20 17:08:40', '2021-04-20 17:08:40'),
(606, '1970013095607f0a5b13f143.34304354', NULL, 'Satın Alım Komisyon', '1-Adet başına kesilen komisyon doları: 0.00120895', '2021-04-20 17:08:40', '2021-04-20 17:08:40'),
(607, '1970013095607f0a5b13f143.34304354', NULL, 'Satın Alma Komisyon', '1-Satın aldıktan sonra komisyon adetten düşmüş ve satış için kalan adet: 15.9', '2021-04-20 17:08:40', '2021-04-20 17:08:40'),
(608, '1970013095607f0a5b13f143.34304354', NULL, 'Komisyon Analiz', '1-Her alım adeti için ödenen komisyon: $0.00120895', '2021-04-20 17:08:40', '2021-04-20 17:08:40'),
(609, '1970013095607f0a5b13f143.34304354', NULL, 'Komisyon Analiz', '1-Her satım adeti için ödenen komisyon: $0.00121895', '2021-04-20 17:08:40', '2021-04-20 17:08:40'),
(610, '1970013095607f0a5b13f143.34304354', NULL, 'Komisyon Analiz', '1-Toplam Ödenen komisyon: $0.0024279', '2021-04-20 17:08:40', '2021-04-20 17:08:40'),
(611, '1970013095607f0a5b13f143.34304354', NULL, 'Satış Yapma', '1-Satılacak Fiyat: 1.22138', '2021-04-20 17:08:40', '2021-04-20 17:08:40'),
(612, '1970013095607f0a5b13f143.34304354', NULL, 'Satış Yapma', '1-Satılacak Adet: 15.9', '2021-04-20 17:08:40', '2021-04-20 17:08:40'),
(613, '1970013095607f0a5b13f143.34304354', NULL, 'Satış Yapma', '1-Satış Limiti Koyma = Başlatıldı!', '2021-04-20 17:08:40', '2021-04-20 17:08:40'),
(614, '1970013095607f0a5b13f143.34304354', 28, 'Satış Yapma', '1-Satış Limiti Koyma = Başarıyla Koyuldu!', '2021-04-20 17:08:41', '2021-04-20 17:08:41'),
(615, '1970013095607f0a5b13f143.34304354', 28, 'Satış Yapma', '1-Satış işleminin gerçekleşmesi bekleniyor...', '2021-04-20 17:08:41', '2021-04-20 17:08:41'),
(616, '1970013095607f0a5b13f143.34304354', 28, 'Satış Yapma', '1-Satın Limiti Başarıyla Gerçekleşti!', '2021-04-20 19:06:43', '2021-04-20 19:06:43'),
(617, '1970013095607f0a5b13f143.34304354', NULL, 'Kâr', '1-Toplam Kâr: 0.0100021', '2021-04-20 19:06:43', '2021-04-20 19:06:43'),
(618, '1970013095607f0a5b13f143.34304354', NULL, '', '1-Cüzdana Dönen Dolar: 19.419942', '2021-04-20 19:06:43', '2021-04-20 19:06:43'),
(619, '1970013095607f0a5b13f143.34304354', NULL, '', '1-Cüzdana Kazanç: 0.076741999999999', '2021-04-20 19:06:43', '2021-04-20 19:06:43'),
(620, '1970013095607f0a5b13f143.34304354', NULL, '', '1-SPOT ALGORITHM END: 20.04.2021 19:06:43', '2021-04-20 19:06:43', '2021-04-20 19:06:43'),
(621, '1970013095607f0a5b13f143.34304354', NULL, '', '-------------------------------', '2021-04-20 19:06:43', '2021-04-20 19:06:43'),
(622, '2132770255607f26482ed3f0.15231385', NULL, '', '-------------------------------', '2021-04-20 19:06:48', '2021-04-20 19:06:48'),
(623, '2132770255607f26482ed3f0.15231385', NULL, '', '2-SPOT ALGORITHM START: 20.04.2021 19:06:48', '2021-04-20 19:06:48', '2021-04-20 19:06:48'),
(624, '2132770255607f26482ed3f0.15231385', NULL, '', '2-Önceden Yapılmış Siparişin Bitmesi Bekleniyor...', '2021-04-20 19:06:48', '2021-04-20 19:06:48'),
(625, '2132770255607f26482ed3f0.15231385', NULL, '', '2-Orders Bypass OK!', '2021-04-20 19:06:48', '2021-04-20 19:06:48'),
(626, '2132770255607f26482ed3f0.15231385', NULL, '', '2-Komisyon: 0.001', '2021-04-20 19:06:49', '2021-04-20 19:06:49'),
(627, '2132770255607f26482ed3f0.15231385', NULL, '', '2-Cüzdandaki Dolar: 20', '2021-04-20 19:06:50', '2021-04-20 19:06:50'),
(628, '2132770255607f26482ed3f0.15231385', NULL, '', '2-Stabiletesi kontrol ediliyor...', '2021-04-20 19:06:50', '2021-04-20 19:06:50'),
(629, '2132770255607f26482ed3f0.15231385', NULL, '', '2-Stabiletesi bulunmuş Fiyat: 1.22355', '2021-04-20 19:08:50', '2021-04-20 19:08:50'),
(630, '2132770255607f26482ed3f0.15231385', NULL, 'Satın Alma', '2-Satın Alınacak Fiyat: 1.22355', '2021-04-20 19:08:50', '2021-04-20 19:08:50'),
(631, '2132770255607f26482ed3f0.15231385', NULL, 'Satın Alma', '2-Satın Alınacak Adet: 16', '2021-04-20 19:08:50', '2021-04-20 19:08:50'),
(632, '2132770255607f26482ed3f0.15231385', NULL, 'Satın Alma', '2-Satın Alışda ödenecek dolar: 19.5768', '2021-04-20 19:08:50', '2021-04-20 19:08:50'),
(633, '2132770255607f26482ed3f0.15231385', NULL, 'Satın Alma Limit Koyma', '2-Satın Alma Limiti Koyma = Başlatıldı!', '2021-04-20 19:08:50', '2021-04-20 19:08:50'),
(634, '2132770255607f26482ed3f0.15231385', 29, 'Satın Alma Limit Koyma', '2-Satın Alma Limiti = Başarıyla Koyuldu!', '2021-04-20 19:08:50', '2021-04-20 19:08:50'),
(635, '2132770255607f26482ed3f0.15231385', 29, 'Satın Alma Limit', '2-Satın Alma Limitinin gerçekleşmesi bekleniyor...', '2021-04-20 19:08:50', '2021-04-20 19:08:50'),
(636, '2132770255607f26482ed3f0.15231385', 29, 'Satın Alma Limit', '2-Satın Alma Limiti Başarıyla Gerçekleşti!', '2021-04-20 19:08:57', '2021-04-20 19:08:57'),
(637, '2132770255607f26482ed3f0.15231385', NULL, 'Satın Alım Komisyon', '2-Satın Alma komisyon düşümü için alınan adette düşülecek Adet: 0.016', '2021-04-20 19:08:57', '2021-04-20 19:08:57'),
(638, '2132770255607f26482ed3f0.15231385', NULL, 'Satın Alım Komisyon', '2-Adet başına kesilen komisyon doları: 0.00122355', '2021-04-20 19:08:57', '2021-04-20 19:08:57'),
(639, '2132770255607f26482ed3f0.15231385', NULL, 'Satın Alma Komisyon', '2-Satın aldıktan sonra komisyon adetten düşmüş ve satış için kalan adet: 15.9', '2021-04-20 19:08:57', '2021-04-20 19:08:57'),
(640, '2132770255607f26482ed3f0.15231385', NULL, 'Komisyon Analiz', '2-Her alım adeti için ödenen komisyon: $0.00122355', '2021-04-20 19:08:57', '2021-04-20 19:08:57'),
(641, '2132770255607f26482ed3f0.15231385', NULL, 'Komisyon Analiz', '2-Her satım adeti için ödenen komisyon: $0.00123355', '2021-04-20 19:08:57', '2021-04-20 19:08:57'),
(642, '2132770255607f26482ed3f0.15231385', NULL, 'Komisyon Analiz', '2-Toplam Ödenen komisyon: $0.0024571', '2021-04-20 19:08:57', '2021-04-20 19:08:57'),
(643, '2132770255607f26482ed3f0.15231385', NULL, 'Satış Yapma', '2-Satılacak Fiyat: 1.23601', '2021-04-20 19:08:57', '2021-04-20 19:08:57'),
(644, '2132770255607f26482ed3f0.15231385', NULL, 'Satış Yapma', '2-Satılacak Adet: 15.9', '2021-04-20 19:08:57', '2021-04-20 19:08:57'),
(645, '2132770255607f26482ed3f0.15231385', NULL, 'Satış Yapma', '2-Satış Limiti Koyma = Başlatıldı!', '2021-04-20 19:08:57', '2021-04-20 19:08:57'),
(646, '2132770255607f26482ed3f0.15231385', 30, 'Satış Yapma', '2-Satış Limiti Koyma = Başarıyla Koyuldu!', '2021-04-20 19:08:58', '2021-04-20 19:08:58'),
(647, '2132770255607f26482ed3f0.15231385', 30, 'Satış Yapma', '2-Satış işleminin gerçekleşmesi bekleniyor...', '2021-04-20 19:08:58', '2021-04-20 19:08:58'),
(648, '2132770255607f26482ed3f0.15231385', 30, 'Satış Yapma', '2-Satın Limiti Başarıyla Gerçekleşti!', '2021-04-20 19:32:43', '2021-04-20 19:32:43'),
(649, '2132770255607f26482ed3f0.15231385', NULL, 'Kâr', '2-Toplam Kâr: 0.0100029', '2021-04-20 19:32:43', '2021-04-20 19:32:43'),
(650, '2132770255607f26482ed3f0.15231385', NULL, '', '2-Cüzdana Dönen Dolar: 19.652559', '2021-04-20 19:32:43', '2021-04-20 19:32:43'),
(651, '2132770255607f26482ed3f0.15231385', NULL, '', '2-Cüzdana Kazanç: 0.075759000000001', '2021-04-20 19:32:43', '2021-04-20 19:32:43'),
(652, '2132770255607f26482ed3f0.15231385', NULL, '', '2-SPOT ALGORITHM END: 20.04.2021 19:32:43', '2021-04-20 19:32:43', '2021-04-20 19:32:43'),
(653, '2132770255607f26482ed3f0.15231385', NULL, '', '-------------------------------', '2021-04-20 19:32:43', '2021-04-20 19:32:43'),
(654, '802510199607f2c60269517.62635715', NULL, '', '-------------------------------', '2021-04-20 19:32:48', '2021-04-20 19:32:48'),
(655, '802510199607f2c60269517.62635715', NULL, '', '3-SPOT ALGORITHM START: 20.04.2021 19:32:48', '2021-04-20 19:32:48', '2021-04-20 19:32:48'),
(656, '802510199607f2c60269517.62635715', NULL, '', '3-Önceden Yapılmış Siparişin Bitmesi Bekleniyor...', '2021-04-20 19:32:48', '2021-04-20 19:32:48'),
(657, '802510199607f2c60269517.62635715', NULL, '', '3-Orders Bypass OK!', '2021-04-20 19:32:48', '2021-04-20 19:32:48'),
(658, '802510199607f2c60269517.62635715', NULL, '', '3-Komisyon: 0.001', '2021-04-20 19:32:49', '2021-04-20 19:32:49'),
(659, '802510199607f2c60269517.62635715', NULL, '', '3-Cüzdandaki Dolar: 20', '2021-04-20 19:32:50', '2021-04-20 19:32:50'),
(660, '802510199607f2c60269517.62635715', NULL, '', '3-Stabiletesi kontrol ediliyor...', '2021-04-20 19:32:50', '2021-04-20 19:32:50');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `logs`
--
ALTER TABLE `logs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=821;

--
-- Tablo için AUTO_INCREMENT değeri `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Tablo için AUTO_INCREMENT değeri `order_logs`
--
ALTER TABLE `order_logs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=661;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
