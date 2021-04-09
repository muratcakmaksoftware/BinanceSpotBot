- PHP Version ^7.3

- [Install jaggedsoft/php-binance-api](https://github.com/jaggedsoft/php-binance-api)
````
composer require "jaggedsoft/php-binance-api @dev"
````

- [Binance Spot Document - binance-spot-api-docs](https://github.com/binance/binance-spot-api-docs/blob/master/rest-api.md)

### Windows Configuration
````
download -> http://curl.haxx.se/ca/cacert.pem
php.ini >
curl.cainfo="C:\xampp73\ca\cacert.pem"
openssl.cafile="C:\xampp73\ca\cacert.pem"
Restart Windows
````

### Spot Rules
````
En az işlem için gerekli miktar: 10$ gerekli işlem testi için 11$ gereklidir.

Limit
Yukarıdan satın alım yapılırsa bulunduğu miktardan satın alır buna dikkat edilmelidir.
Her zaman satın alım bulunduğu limitin altından,
Her zaman satış için bulunduğu limitin üstünden yapılmalıdır.

Alım ve Satışta Adet
Ondalıklı basamak sayısı 1 olabilir en fazla
Örnek: 1.1 doğru 1.11 yanlıştır.

````
