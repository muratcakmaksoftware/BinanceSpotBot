<?php

namespace App\Console\Commands;

use App\Enums\ConsoleMessageType;
use App\Models\Coin;
use App\Models\Order;
use App\Traits\BinanceTrait;
use App\Traits\LogTrait;
use App\Traits\MessageTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;
use \Binance;

class Mint extends Command
{
    use LogTrait;
    use BinanceTrait;
    use MessageTrait;

    protected $signature = 'mint
                                {--coin : Spot yapılacak coin}
                                {--currency : Satış yapılacak para birimi}
                                {--maxWalletPriceLimit : Maksimum cüzdandan çekilecek para miktarı}
                                {--stabilizationSensitivity : Stabilizasyon aralığı kontrol sayısı}';

    protected $description = 'Darphanem V3';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * İşlemin yapılacağı Borsanın API
     * @var Binance\API
     */
    protected $api = null;

    /**
     * Coinin ID si
     * @var int
     */
    protected $coinId = null;

    /**
     * Coinin Adi Örnek: MATIC
     * @var string
     */
    protected $coinName = null;

    /**
     * Satış Yapılacak Para birimi // Örnek: TRY
     * @var string
     */
    protected $currency = null;

    /**
     * Spot yapılacak coin birimi // Örnek: MATICTRY
     * @var string
     */
    protected $spot = null;

    /**
     * @var int
     */
    protected $coinProfit = null;

    /**
     * Satın alım işleminde artı ve eksi aralığını belirler. // Örnek: 0.00150
     * @var int
     */
    protected $coinPurchase = null;

    /**
     * Maksimum cüzdandan çekilecek para miktarı. // Örnek: $100
     * @var int
     */
    protected $maxWalletPriceLimit = null;

    /**
     * Stabilizasyon aralığı kontrol sayısı // Örnek: 50
     * @var int
     */
    protected $stabilizationSensitivity = null;

    /**
     * Spot işleminin ayırt etmek için uniqueId // Örnek: 11263975617ba81dddb5c4.16310081
     * @var string
     */
    protected $uniqueId = -1;

    /**
     * Göze alınan kabul edilebilir kayıp toleransı. Satın alım rakamından yüzde kaç kayıp olursa satışı gerçekleştireceğini belirler.
     * @var float
     */
    protected $lossTolerance = 0.022; //%22 Kayıp toleransı

    /**
     * Göze alınan kayıpta yüksek düşüşler gerçekleştiği zaman satış yapmaması sağlamaktadır. Belirlenen yüzde miktarı aşıldığında satışı gerçekleştirmeyecektir.
     * @var float
     */
    protected $limitLossTolerance = 0.04; //%4 Yüksek zarar miktarını engellemek için tolerans.

    /**
     * Belirlenen kayıp tolaransında satış işleminin yapılıp yapılmayacağını belirler. Özetle belirli yüzdelere göre zarar satış yapılıp yapılmayacağını belirler.
     * True zarar satış yapabilir demektir. False ise yapamaz demektir.
     * @var bool
     */
    protected $lossToleranceStatus = true;

    /**
     * Borsaya satın alım ve satış işleminde ödenecek olan yüzdelik orandır.
     * @var float
     */
    protected $fee = 0.001;

    /**
     * Borsadaki cüzdandaki para birimi
     * @var float
     */
    protected $walletCurrency = 0;

    public function handle()
    {
        //php artisan mint --coin MATIC --currency USDT --maxWalletPriceLimit 100 --stabilizationSensitivity 50
        $this->api = new Binance\API(base_path('public/binance/config.json'));
        $this->api->caOverride = true;

        /**
         * Sets Argument
         */
        $this->coinName = strtoupper($this->argument("coin")); //Ex: MATIC
        $this->currency = strtoupper($this->argument("currency")); //Ex: TRY
        $this->maxWalletPriceLimit = intval($this->argument("maxWalletPriceLimit")); //Ex: $20 cüzdandaki kullanılacak para miktarı.
        $this->stabilizationSensitivity = intval($this->argument("stabilizationSensitivity"));

        $coin = Coin::where("name", $this->coinName)->first();
        if (isset($coin)) {
            $this->coinId = $coin->id;
            $this->coinName = $coin->name; //MATIC
            $this->spot = $this->coinName . $this->currency; //MATICTRY
            $this->coinProfit = $coin->profit; // Satışta alacağımız kazanç miktarı
            $this->coinPurchase = $coin->purchase; // satın alma aralık coin para birimi artı ve eksi aralığını belirler.
            $this->orderLog(ConsoleMessageType::INFO, "Coin ID: " . $this->coinId);

            $this->orderLog(ConsoleMessageType::INFO, "Alınacak Coin Adı: " . $this->coinName);

            $this->orderLog(ConsoleMessageType::INFO, "Satılacak Para Birimi: " . $this->currency);

            $this->orderLog(ConsoleMessageType::INFO, "SPOT: " . $this->spot);

            $this->orderLog(ConsoleMessageType::INFO, "Min kâr: " . $this->coinProfit . " " . $this->currency);

            $this->orderLog(ConsoleMessageType::INFO, "Coin Sirkülasyon Aralığı: " . $this->coinPurchase);

            $whileCounter = 1;
            while (true) {
                $this->uniqueId = uniqid(rand(), true);
                $this->orderLog(ConsoleMessageType::INFO, "-------------------------------", $this->uniqueId);

                $this->orderLog(ConsoleMessageType::INFO, $whileCounter . "-SPOT ALGORITHM START: ", $this->uniqueId);

                $this->orderLog(ConsoleMessageType::INFO, $whileCounter . "-Önceden Yapılmış Siparişin Bitmesi Bekleniyor...", $this->uniqueId);

                //Beklemede olan açık emir yoksa algoritmaya giriş yapabilir!
                $this->waitOpenOrders();

                $fee = $this->getCommission($this->spot);
                $this->info($whileCounter . "-Komisyon: " . $fee);

                $this->orderLog(ConsoleMessageType::INFO, $whileCounter . "-Komisyon: " . $fee, $this->uniqueId);
                $this->fee = floatval($fee);

                //Cüzdan daki para bilgisi alınıyor
                $this->walletCurrency = $this->getWalletCurrency($this->currency);

                //Limit parası cüzdandaki paradan büyük mü ?
                if ($this->maxWalletPriceLimit > $this->walletCurrency) {
                    $this->orderLog(ConsoleMessageType::ERROR, "Cüzdan Limit Sorunu", $whileCounter . "-Belirlediğiniz limit: " . $this->maxWalletPriceLimit . " " . $this->currency . " cüzdan miktarından küçük olmalıdır. Cüzdan Miktarınız: " . $this->walletCurrency . " " . $this->currency, $this->uniqueId);
                    break;
                }

                if ($this->walletCurrency > $this->maxWalletPriceLimit) { //Cüzdan miktarı limitten büyükse limit fiyatına sabitle.
                    //Test için en düşük X dolardan alım yapılabilir.
                    $this->walletCurrency = $this->maxWalletPriceLimit;
                }

                $this->orderLog(ConsoleMessageType::INFO, $whileCounter . "-Cüzdandaki " . $this->currency . ": " . $this->walletCurrency, $this->uniqueId);

                $this->orderLog(ConsoleMessageType::INFO, $whileCounter . "-Stabiletesi kontrol ediliyor...", $this->uniqueId);

                $buyPrice = $this->getStabilizationPrice($this->spot, $this->coinPurchase, $this->stabilizationSensitivity); //stabil fiyat alınıyor
                $this->info($whileCounter . "-Stabiletesi bulunmuş Fiyat: " . $buyPrice);
                $this->orderLog(ConsoleMessageType::INFO, $whileCounter . "-Stabiletesi bulunmuş Fiyat: " . $buyPrice, $this->uniqueId);

                $coinDigit = pow(10, $this->getCoinPriceDigit($buyPrice)); //Coinin küsürat sayısının öğrenilmesi ve üstü alınarak sellPrice da düzeltme yapılması.

                // ################## [KOMİSYON VE ALINACAK ADETIN BELİRLENMESİ BAŞLANGIÇ] ##################

                $commissionPercent = $fee; //Para birimine göre komisyon miktarı
                $buyPiece = floor($this->walletCurrency / $buyPrice); //alınacak adet.

                // ################## [KOMİSYON VE ALINACAK ADETIN BELİRLENMESİ BİTİŞ] ##################


                // ############# [SATIN ALMA BAŞLANGIÇ] #############
                $this->orderLog(ConsoleMessageType::INFO,$whileCounter . "-Satın Alınacak Fiyat: " . $buyPrice, $this->uniqueId);
                $this->orderLog(ConsoleMessageType::INFO,$whileCounter . "-Satın Alınacak Adet: " . $buyPiece, $this->uniqueId);

                $totalBuyPrice = $buyPrice * $buyPiece;
                $this->orderLog(ConsoleMessageType::INFO, $whileCounter . "-Satın Alımında Toplam Ödenecek " . $this->currency . ": " . $totalBuyPrice, $this->uniqueId);
                $this->orderLog(ConsoleMessageType::INFO,$whileCounter . "-Satın Alma Limiti Koyma = Başlatıldı!", $this->uniqueId);

                //SATIN ALMA LİMİTİNİN OLUŞTURMA İŞLEMİ
                $buyOrderId = $this->buyCoin($this->spot, $buyPiece, $buyPrice);

                $this->orderLog(ConsoleMessageType::INFO, $whileCounter . "-Satın Alma Limiti = Başarıyla Koyuldu!", $this->uniqueId, $buyOrderId);
                $this->orderLog(ConsoleMessageType::INFO,$whileCounter . "-Satın Alma Limitinin gerçekleşmesi bekleniyor...", $this->uniqueId, $buyOrderId);

                $buyOrder = Order::where("id", $buyOrderId)->first();
                $this->getOrderStatus($this->spot, $buyOrder);

                $this->orderLog(ConsoleMessageType::INFO,$whileCounter . "-Satın Alma Limiti Başarıyla Gerçekleşti!", $this->uniqueId, $buyOrderId);

                // ############# [SATIN ALMA BİTİŞ] #############

                // ############# [SATIŞ BİLGİLERİNİN HESAPLANMA İŞLEMLERİ BAŞLANGIÇ] #############

                //Satış yapılacak kar belirlenmesi
                $buyPieceCommission = $buyPiece * $commissionPercent; //Alım yapıldığında coin den komisyon düşümü.
                $this->orderLog(ConsoleMessageType::INFO, $whileCounter . "-Satın Alma komisyon düşümü için alınan adette düşülecek Adet: " . $buyPieceCommission, $this->uniqueId, $buyOrderId);

                /* //Düşülmüş olan komisyon coin bilgisiyle = düşülmüş olan her adet için komisyon dolar bilgisini öğrenme.
                    Alınan adet = 188
                    188 * 0,001 = 0,188 = toplam kesilen komisyon miktarı COİN
                    0,0583665 * 0,188 = 0,010972902 // komisyon kesilen coinin toplam dolar karşılığı.
                    0,010972902 / 188 = 0,0000583665 // coin adeti başına alınan komisyon dolar bilgisi.
                */
                $buyCommissionPrice = (($buyPrice * $buyPieceCommission) / $buyPiece);
                $this->orderLog(ConsoleMessageType::INFO, $whileCounter . "-Adet başına kesilen komisyon " . $this->currency . ": " . $buyCommissionPrice, $this->uniqueId, $buyOrderId);
                $sellPiece = $buyPiece - $buyPieceCommission; //satış için KALAN ADET
                $sellPiece = floor($sellPiece * 10) / 10; // satış için basamak düzeltme. ör: 10.989 => 10.9
                $this->orderLog(ConsoleMessageType::INFO,$whileCounter . "-Satın aldıktan sonra komisyon adetten düşmüş ve satış için kalan adet: " . $sellPiece, $this->uniqueId);

                //SATIŞ İÇİN MİKTARIN BELİRLENMESİ
                //           ( alım miktar + kar artım )
                $sellPrice = $buyPrice + $this->coinProfit;

                //Satışta kesilecek komisyon bilgisi
                /*
                    Satış için kalan adet Adet = 188
                    188 * 0,001 = 0,188 = toplam kesilen komisyon miktarı COİN
                    0,0587266 * 0,188 = 0,0110406008 // komisyon kesilen coinin toplam dolar karşılığı.
                    0,0110406008 / 0,188 = 0,0587266 // coin başına alınan komisyon dolar bilgisi.
                */
                $sellCommissionPrice = ($sellPrice * ($sellPiece * $commissionPercent)) / $sellPiece; //satışta kesilecek olan adet başına komisyon doları
                $totalCommission = $buyCommissionPrice + $sellCommissionPrice; //Satın alım komisyon toplamı ve satışta alacak komisyon toplamı

                $this->orderLog(ConsoleMessageType::INFO,$whileCounter . "-Her alım adeti için ödenen komisyon: " . $this->currency . " " . $buyCommissionPrice, $this->uniqueId);
                $this->orderLog(ConsoleMessageType::INFO, $whileCounter . "-Her satım adeti için ödenen komisyon: " . $this->currency . " " . $sellCommissionPrice, $this->uniqueId);
                $this->orderLog(ConsoleMessageType::INFO, $whileCounter . "-Her alım+satım adeti için toplam ödenen komisyon: " . $this->currency . " " . $totalCommission, $this->uniqueId);

                //###### SATIŞ LİMİT FİYATININ BELİRLENMESİ ######
                //$sellPrice = $sellPrice + $totalCommission;
                $sellPrice = ceil($sellPrice * $coinDigit) / $coinDigit; //kusurat duzeltme örn: 1.2359069 => 1.23591

                // ############# [SATIŞ BİLGİLERİNİN HESAPLANMA İŞLEMLERİ BİTİŞ] #############


                // ################## [SATIŞ YAPMA BAŞLANGIÇ] ##################
                $this->orderLog(ConsoleMessageType::INFO, $whileCounter . "-Satılacak Fiyat: " . $sellPrice, $this->uniqueId);
                $this->orderLog(ConsoleMessageType::INFO, $whileCounter . "-Satılacak Adet: " . $sellPiece, $this->uniqueId);
                $this->orderLog(ConsoleMessageType::INFO, $whileCounter . "-Satış Limiti Koyma = Başlatıldı!", $this->uniqueId);

                //SATIŞ LİMİNİTİN OLUŞTURMA İŞLEMİ
                $sellOrderId = $this->sellCoin($this->spot, $sellPiece, $sellPrice);

                $this->orderLog(ConsoleMessageType::INFO, $whileCounter . "-Satış Limiti Koyma = Başarıyla Koyuldu!", $this->uniqueId);

                $sellOrder = Order::where("id", $sellOrderId)->first();

                //Satışta harcanan total miktarın alınması.
                $totalSellPrice = $sellPrice * $sellPiece;

                //Satışta Ödenen Toplam komisyon dolarının kaydedilmesi.
                $totalSellCommissionPrice = $sellCommissionPrice * $sellPiece;

                //Ön Analiz Bilgilerinin Basılması
                $this->orderLog(ConsoleMessageType::INFO,$whileCounter . "-Alımda ödenen " . $this->currency . ": " . $buyOrder->total, $this->uniqueId);
                $this->orderLog(ConsoleMessageType::INFO,$whileCounter . "-Alımda Ödenen Toplam Komisyon " . $this->currency . ": " . $buyOrder->fee, $this->uniqueId);
                $this->orderLog(ConsoleMessageType::INFO,$whileCounter . "-Satımda Ödenecek " . $this->currency . ": " . $totalSellPrice, $this->uniqueId);
                $this->orderLog(ConsoleMessageType::INFO,$whileCounter . "-Satımda Ödenecek Toplam Komisyon " . $this->currency . ": " . $totalSellCommissionPrice, $this->uniqueId);
                $this->orderLog(ConsoleMessageType::INFO,$whileCounter . "-Satış işleminin gerçekleşmesi bekleniyor...", $this->uniqueId, $sellOrderId);
                $this->orderLog(ConsoleMessageType::INFO,$whileCounter . "-Toplam Kâr: " . floatval(($totalSellPrice - $buyOrder->total) - ($totalSellCommissionPrice + $buyOrder->fee)), $this->uniqueId);

                //Satış limiti gerçekleşmiş mi ?
                if ($this->getOrderStatus($this->spot, $sellOrder)) {
                    $this->orderLog(ConsoleMessageType::INFO, $whileCounter . "-Satış Limiti Başarıyla Gerçekleşti!", $this->uniqueId, $sellOrderId);
                } else {
                    $this->orderLog(ConsoleMessageType::INFO,$whileCounter . "-Satış Limiti İptal Ediliyor!", $this->uniqueId, $sellOrderId);
                    $this->orderCancel($sellOrder);
                    $this->orderLog(ConsoleMessageType::INFO, $whileCounter . "-Satış Limiti Başarıyla İptal Edildi!", $this->uniqueId, $sellOrderId);
                }

                // ################## [SATIŞ YAPMA BİTİŞ] ##################

                /*
                //Kar analiz
                $this->info($whileCounter."-Toplam Kâr: ". floatval(($sellPrice - $buyPrice) - $totalCommission));
                LogHelper::orderLog("Kâr",$whileCounter."-Toplam Kâr: ". floatval(($sellPrice - $buyPrice) - $totalCommission), $this->uniqueId);


                //(satış fiyati * satış adeti) - (satış komisyon fiyatı * satış adeti)
                $totalSellPrice = ($sellPrice * $sellPiece) - ($sellCommissionPrice * $sellPiece);
                $this->info($whileCounter."-Cüzdana Dönen ".$this->currency.": ". $totalSellPrice);
                LogHelper::orderLog("",$whileCounter."-Cüzdana Dönen ".$this->currency.": ". $totalSellPrice, $this->uniqueId);

                //satıştan sonra cüzdanda kalan para - alımdan sonra komisyon düşümüyle kalan fiyat
                $gain = $totalSellPrice - (($buyPrice * $buyPiece) - ($buyCommissionPrice * $buyPiece));
                $this->info($whileCounter."-Cüzdana Kazanç: ". $gain);
                LogHelper::orderLog("",$whileCounter."-Cüzdana Kazanç: ". $gain, $this->uniqueId);*/

                $this->orderLog(ConsoleMessageType::INFO, $whileCounter . "-SPOT ALGORITHM END:", $this->uniqueId);
                $this->orderLog(ConsoleMessageType::INFO, "-------------------------------", $this->uniqueId);

                //Yeni Alım - Satım için geçiş
                $whileCounter++;
                sleep(2);
            }
        } else {
            $this->log(ConsoleMessageType::INFO, null, "Coin Select", "Coin bulunamadı!");
        }
        $this->consoleMessage(ConsoleMessageType::ERROR,"ALGORİTMA BİR DURUMDAN DOLAYI DURDURULDU!");
    }
}
