-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 02 Nis 2022, 16:37:58
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
  `purchase` decimal(10,5) DEFAULT NULL,
  `spot_digit` tinyint(4) DEFAULT 1 COMMENT '0 girilirse tam sayı olarak adet satışı yapar.\r\n1 girilirse 10^1 yani 10 dur.\r\n2 girilirse 10^2 yani 100 dür'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tablo döküm verisi `coins`
--

INSERT INTO `coins` (`id`, `name`, `profit`, `purchase`, `spot_digit`) VALUES
(1, 'ADA', '0.00850', '0.00150', 1),
(2, 'TRX', '0.00800', '0.00400', 1),
(3, 'DOGE', '0.00010', '0.00400', 1),
(4, 'VET', '0.00800', '0.00400', 1),
(5, 'XRP', '0.00800', '0.00300', 0),
(6, 'MATIC', '0.00800', '0.00150', 1),
(7, 'BNB', '0.02000', '0.00150', 3);

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
(835, 3, 7, 'buyCoin Limit Error', 'Satın Alma Limit Başarısız. Detay: signedRequest error: {\"code\":-1013,\"msg\":\"Invalid quantity.\"} Satır: 1350', '2022-04-02 12:21:17', '2022-04-02 12:21:17');

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

--
-- Tablo döküm verisi `orders`
--

INSERT INTO `orders` (`id`, `unique_id`, `coin_id`, `orderId`, `symbol`, `side`, `origQty`, `price`, `type`, `status`, `fee`, `total`, `var_piece`, `var_price`, `json_data`, `created_at`, `updated_at`) VALUES
(95, '6402649206248513775ce18.48516363', 5, 4230739804, 'XRPUSDT', 'BUY', '141.00000000', '0.84590000', 'LIMIT', 'FILLED', '0.11927190', '119.27190000', '141.00000000', '0.84590000', '{\"symbol\":\"XRPUSDT\",\"orderId\":4230739804,\"orderListId\":-1,\"clientOrderId\":\"gGMXSi8p9MTHH6uBqIBv76\",\"transactTime\":1648906597483,\"price\":\"0.84590000\",\"origQty\":\"141.00000000\",\"executedQty\":\"141.00000000\",\"cummulativeQuoteQty\":\"119.27190000\",\"status\":\"FILLED\",\"timeInForce\":\"GTC\",\"type\":\"LIMIT\",\"side\":\"BUY\",\"fills\":[{\"price\":\"0.84590000\",\"qty\":\"141.00000000\",\"commission\":\"0.14100000\",\"commissionAsset\":\"XRP\",\"tradeId\":434744502}]}', '2022-04-02 13:36:37', '2022-04-02 13:36:38'),
(96, '6402649206248513775ce18.48516363', 5, 4230739817, 'XRPUSDT', 'SELL', '140.00000000', '0.85560000', 'LIMIT', 'NEW', '0.00000000', '0.00000000', '140.00000000', '0.85560000', '{\"symbol\":\"XRPUSDT\",\"orderId\":4230739817,\"orderListId\":-1,\"clientOrderId\":\"RqwUL5yYs7WktQLS9NkY90\",\"transactTime\":1648906598728,\"price\":\"0.85560000\",\"origQty\":\"140.00000000\",\"executedQty\":\"0.00000000\",\"cummulativeQuoteQty\":\"0.00000000\",\"status\":\"NEW\",\"timeInForce\":\"GTC\",\"type\":\"LIMIT\",\"side\":\"SELL\",\"fills\":[]}', '2022-04-02 13:36:39', '2022-04-02 13:36:39');

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
(3509, NULL, NULL, 'Coin ID: 5', '2022-04-02 13:35:51', '2022-04-02 13:35:51'),
(3510, NULL, NULL, 'Alınacak Coin Adı: XRP', '2022-04-02 13:35:51', '2022-04-02 13:35:51'),
(3511, NULL, NULL, 'Satılacak Para Birimi: USDT', '2022-04-02 13:35:51', '2022-04-02 13:35:51'),
(3512, NULL, NULL, 'SPOT: XRPUSDT', '2022-04-02 13:35:51', '2022-04-02 13:35:51'),
(3513, NULL, NULL, 'Min kâr: 0.00800 USDT', '2022-04-02 13:35:51', '2022-04-02 13:35:51'),
(3514, NULL, NULL, 'Coin Sirkülasyon Aralığı: 0.00300', '2022-04-02 13:35:51', '2022-04-02 13:35:51'),
(3515, '6402649206248513775ce18.48516363', NULL, '-------------------------------', '2022-04-02 13:35:51', '2022-04-02 13:35:51'),
(3516, '6402649206248513775ce18.48516363', NULL, '1-SPOT ALGORITHM START: ', '2022-04-02 13:35:51', '2022-04-02 13:35:51'),
(3517, '6402649206248513775ce18.48516363', NULL, '1-Önceden Yapılmış Siparişin Bitmesi Bekleniyor...', '2022-04-02 13:35:51', '2022-04-02 13:35:51'),
(3518, '6402649206248513775ce18.48516363', NULL, '1-Komisyon: 0.001', '2022-04-02 13:35:52', '2022-04-02 13:35:52'),
(3519, '6402649206248513775ce18.48516363', NULL, '1-Cüzdandaki USDT: 120', '2022-04-02 13:35:53', '2022-04-02 13:35:53'),
(3520, '6402649206248513775ce18.48516363', NULL, '1-Stabiletesi kontrol ediliyor...', '2022-04-02 13:35:53', '2022-04-02 13:35:53'),
(3521, '6402649206248513775ce18.48516363', NULL, '1-Stabiletesi bulunmuş Fiyat: 0.8459', '2022-04-02 13:36:37', '2022-04-02 13:36:37'),
(3522, '6402649206248513775ce18.48516363', NULL, '1-Satın Alınacak Fiyat: 0.8459', '2022-04-02 13:36:37', '2022-04-02 13:36:37'),
(3523, '6402649206248513775ce18.48516363', NULL, '1-Satın Alınacak Adet: 141', '2022-04-02 13:36:37', '2022-04-02 13:36:37'),
(3524, '6402649206248513775ce18.48516363', NULL, '1-Satın Alımında Toplam Ödenecek USDT: 119.2719', '2022-04-02 13:36:37', '2022-04-02 13:36:37'),
(3525, '6402649206248513775ce18.48516363', NULL, '1-Satın Alma Limiti Koyma = Başlatıldı!', '2022-04-02 13:36:37', '2022-04-02 13:36:37'),
(3526, '6402649206248513775ce18.48516363', 95, '1-Satın Alma Limiti = Başarıyla Koyuldu!', '2022-04-02 13:36:37', '2022-04-02 13:36:37'),
(3527, '6402649206248513775ce18.48516363', 95, '1-Satın Alma Limitinin gerçekleşmesi bekleniyor...', '2022-04-02 13:36:37', '2022-04-02 13:36:37'),
(3528, '6402649206248513775ce18.48516363', 95, '1-Satın Alma Limiti Başarıyla Gerçekleşti!', '2022-04-02 13:36:38', '2022-04-02 13:36:38'),
(3529, '6402649206248513775ce18.48516363', 95, '1-Satın Alma komisyon düşümü için alınan adette düşülecek Adet: 0.141', '2022-04-02 13:36:38', '2022-04-02 13:36:38'),
(3530, '6402649206248513775ce18.48516363', 95, '1-Adet başına kesilen komisyon USDT: 0.0008459', '2022-04-02 13:36:38', '2022-04-02 13:36:38'),
(3531, '6402649206248513775ce18.48516363', NULL, '1-Satın aldıktan sonra komisyon adetten düşmüş ve satış için kalan adet: 140', '2022-04-02 13:36:38', '2022-04-02 13:36:38'),
(3532, '6402649206248513775ce18.48516363', NULL, '1-Her alım adeti için ödenen komisyon: USDT 0.0008459', '2022-04-02 13:36:38', '2022-04-02 13:36:38'),
(3533, '6402649206248513775ce18.48516363', NULL, '1-Her satım adeti için ödenen komisyon: USDT 0.0008539', '2022-04-02 13:36:38', '2022-04-02 13:36:38'),
(3534, '6402649206248513775ce18.48516363', NULL, '1-Her alım+satım adeti için toplam ödenen komisyon: USDT 0.0016998', '2022-04-02 13:36:38', '2022-04-02 13:36:38'),
(3535, '6402649206248513775ce18.48516363', NULL, '1-Satılacak Fiyat: 0.8556', '2022-04-02 13:36:38', '2022-04-02 13:36:38'),
(3536, '6402649206248513775ce18.48516363', NULL, '1-Satılacak Adet: 140', '2022-04-02 13:36:38', '2022-04-02 13:36:38'),
(3537, '6402649206248513775ce18.48516363', NULL, '1-Satış Limiti Koyma = Başlatıldı!', '2022-04-02 13:36:38', '2022-04-02 13:36:38'),
(3538, '6402649206248513775ce18.48516363', NULL, '1-Satış Limiti Koyma = Başarıyla Koyuldu!', '2022-04-02 13:36:39', '2022-04-02 13:36:39'),
(3539, '6402649206248513775ce18.48516363', NULL, '1-Alımda ödenen USDT: 119.2719', '2022-04-02 13:36:39', '2022-04-02 13:36:39'),
(3540, '6402649206248513775ce18.48516363', NULL, '1-Alımda Ödenen Toplam Komisyon USDT: 0.1192719', '2022-04-02 13:36:39', '2022-04-02 13:36:39'),
(3541, '6402649206248513775ce18.48516363', NULL, '1-Satımda Ödenecek USDT: 119.784', '2022-04-02 13:36:39', '2022-04-02 13:36:39'),
(3542, '6402649206248513775ce18.48516363', NULL, '1-Satımda Ödenecek Toplam Komisyon USDT: 0.119546', '2022-04-02 13:36:39', '2022-04-02 13:36:39'),
(3543, '6402649206248513775ce18.48516363', 96, '1-Satış işleminin gerçekleşmesi bekleniyor...', '2022-04-02 13:36:39', '2022-04-02 13:36:39'),
(3544, '6402649206248513775ce18.48516363', NULL, '1-Toplam Kâr: 0.2732821', '2022-04-02 13:36:39', '2022-04-02 13:36:39');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tablo için AUTO_INCREMENT değeri `logs`
--
ALTER TABLE `logs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=836;

--
-- Tablo için AUTO_INCREMENT değeri `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- Tablo için AUTO_INCREMENT değeri `order_logs`
--
ALTER TABLE `order_logs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3545;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
