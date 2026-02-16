# [TR] Tosla (AkÖde) Sanal POS PHP Entegrasyonu

Tosla (AkÖde) Sanal POS için basit, kullanıma hazır PHP entegrasyonu.

Bu proje, AkÖde PHP SDK yapısını kullanarak 3D Secure ödeme akışının temiz bir uygulamasını içerir.

## Özellikler

-   **3D Secure Ödeme**: Tam 3D Secure akışı uygulaması (Başlatma -> Yönlendirme -> Dönüş).
-   **Güvenli**: Hash oluşturma ve doğrulama için `AkodePayment` sınıfını kullanır.
-   **Kolay Kurulum**: Tek dosyadan ayar yapılabilir.
-   **Bağlantı İyileştirmeleri**: Çeşitli sunucu ortamlarıyla uyumluluk için cURL iyileştirmelerini (`CURLOPT_CONNECTTIMEOUT`, `IPv4 Resolve`) içerir.

## Dosya Yapısı

-   `AkodePayment.php`: Temel kütüphane dosyası (SDK).
-   `index.php`: Ödeme sayfası. Kart bilgilerini alır ve işlemi başlatır.
-   `callback.php`: Dönüş URL işleyicisi (Callback). İşlemi doğrular ve ödemeyi tamamlar (`PostAuth`).

## Kurulum

1.  Bu projeyi indirin.
2.  Dosyaları sunucunuza yükleyin.
3.  `index.php` ve `callback.php` dosyalarını açın.

## Ayarlar

`index.php` ve `callback.php` dosyalarının **her ikisini de** düzenleyin ve API bilgilerinizi girin:

```php
$apiUser = "YOUR_API_USER";   // API Kullanıcı Adı
$clientId = "YOUR_CLIENT_ID"; // Mağaza No
$apiPass = "YOUR_API_PASS";   // API Şifresi
$environment = "https://entegrasyon.tosla.com/api/Payment/"; // Canlı Ortam URL'si
```

## Kullanım

1.  Tarayıcınızda `http://alanadiniz.com/dosya-yolu/index.php` adresine gidin.
2.  Tutar ve sipariş detaylarını girin (gerçek uygulamada sepet sisteminize entegre edin).
3.  Kullanıcı kart bilgilerini girer ve "Öde"ye tıklar.
4.  Sistem 3D Secure yönlendirmesini yapar ve sonucu `callback.php` dosyasına döndürür.

## Gereksinimler

-   PHP 7.0 veya üzeri
-   cURL eklentisi
-   OpenSSL eklentisi

## Yasal Uyarı

Bu örnek bir uygulamadır. Kredi kartı verilerini işlerken PCI-DSS kurallarına uyduğunuzdan emin olun.
