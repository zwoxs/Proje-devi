<?php
session_start(); // Oturum başlatılıyor

// Eğer kullanıcı kredisi yoksa, başlangıçta 0 kredi ver
if (!isset($_SESSION['user_credit'])) {
    $_SESSION['user_credit'] = 0;
}

$yukleme_basarili = false; // Başlangıçta yükleme başarısız

// Eğer daha önce kredi yükleme yapılmışsa, tekrar yapılmasını engelle
if (!isset($_SESSION['kredi_yukleme_basarili']) || $_SESSION['kredi_yukleme_basarili'] !== true) {
    // Form gönderildiğinde kredi işlemini gerçekleştiriyoruz
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kredi_miktar'])) {
        // Kredi miktarını alıyoruz ve sayısal bir değere dönüştürüyoruz
        $kredi = intval($_POST['kredi_miktar']);
        
        // Eğer kredi miktarı 0'dan büyükse, ekleyebiliriz
        if ($kredi > 0) {
            // Kredi ekleniyor
            $_SESSION['user_credit'] += $kredi;

            // Ödeme geçmişini JSON dosyasına kaydediyoruz
            $odeme_gecmisi = json_decode(file_get_contents('odeme_gecmisi.json'), true);
            $odeme_gecmisi[] = ['tarih' => date('Y-m-d H:i:s'), 'miktar' => $kredi];
            file_put_contents('odeme_gecmisi.json', json_encode($odeme_gecmisi));

            $_SESSION['kredi_yukleme_basarili'] = true; // Kredi yükleme başarılı
            $yukleme_basarili = true; // Yükleme başarılı oldu
        }
    }
}

// Ödeme geçmişini alıyoruz
$odeme_gecmisi = json_decode(file_get_contents('odeme_gecmisi.json'), true);
$odeme_miktarlari = array_column($odeme_gecmisi, 'miktar');
$odeme_tarihleri = array_column($odeme_gecmisi, 'tarih');
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kredi Yükle</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #121212;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            text-align: center;
            padding-top: 80px;
            margin: 0;
        }

        h1 {
            font-size: 40px;
            color: #ff9900;
        }

        .btn {
            background-color: #ff9900;
            border: none;
            color: white;
            padding: 15px 30px;
            font-size: 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn:hover {
            background-color: #ff6600;
            transform: scale(1.05);
        }

        .kredi-form {
            background: #333;
            display: none;
            margin: 20px auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 450px;
            text-align: left;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .kredi-form input, .kredi-form select {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: none;
            background-color: #444;
            color: white;
            font-size: 16px;
        }

        .kredi-form select {
            background-color: #444;
        }

        .kredi-bilgi {
            font-size: 24px;
            margin-bottom: 15px;
            color: #ffcc00;
        }

        .popup {
            display: none;
            background: #00cc66;
            padding: 15px;
            border-radius: 10px;
            color: white;
            font-weight: bold;
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 999;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .popup-error {
            background-color: #e74c3c;
        }

        .chart-container {
            width: 80%;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #222;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .form-part {
            display: none;
        }

        .form-part input {
            margin-top: 10px;
        }
    </style>
</head>
<body>

<h1>Kredi Yükle</h1>
<p class="kredi-bilgi">Mevcut Kredi: <?php echo $_SESSION['user_credit']; ?> Kredi</p>

<button class="btn" onclick="gosterForm()">Kredi Yükle</button>

<div id="formAlani" class="kredi-form">
    <form method="POST">
        <label for="krediInput">Kredi Miktarı:</label>
        <input type="number" name="kredi_miktar" id="krediInput" required min="1" placeholder="Örn: 100">

        <label for="odemeYontemi">Ödeme Yöntemi Seç:</label>
        <select id="odemeYontemi" onchange="gosterOdemeForm()" required>
            <option value="">-- Seçiniz --</option>
            <option value="kart">Kredi Kartı</option>
            <option value="papara">Papara</option>
            <option value="havale">Havale / EFT</option>
        </select>

        <div id="form_kart" class="form-part">
            <label>Kart Numarası:</label>
            <input type="text" maxlength="19" placeholder="1234 5678 9012 3456" oninput="autoFormatCard(this)" required>

            <label>Son Kullanma (AA/YY):</label>
            <input type="text" maxlength="5" placeholder="12/27" required>

            <label>CVC:</label>
            <input type="text" maxlength="3" placeholder="123" required>
        </div>

        <div id="form_papara" class="form-part">
            <label>Papara Numarası:</label>
            <input type="text" placeholder="1234567890" required>
        </div>

        <div id="form_havale" class="form-part">
            <label>IBAN:</label>
            <input type="text" value="TR00 0000 0000 0000 0000 0000 00" readonly>

            <label>Ad Soyad:</label>
            <input type="text" placeholder="Gönderici Ad Soyad" required>
        </div>

        <button type="submit" class="btn">Yüklemeyi Tamamla</button>
    </form>
</div>

<?php if ($yukleme_basarili): ?>
    <div id="popup" class="popup">✅ Kredi başarıyla yüklendi! Yeni bakiye: <?php echo $_SESSION['user_credit']; ?> Kredi</div>
    <script>
        setTimeout(() => {
            document.getElementById("popup").style.display = "block";
            setTimeout(() => {
                document.getElementById("popup").style.display = "none";
            }, 3000);
        }, 500);
    </script>
<?php endif; ?>

<!-- Chart.js Grafik Alanı -->
<div class="chart-container">
    <canvas id="odemeGrafik"></canvas>
</div>

<script>
    function gosterForm() {
        document.getElementById("formAlani").style.display = "block";
    }

    function gosterOdemeForm() {
        const secim = document.getElementById("odemeYontemi").value;
        document.querySelectorAll(".form-part").forEach(el => {
            el.style.display = "none";
            el.querySelectorAll("input").forEach(input => input.required = false);
        });

        if (secim) {
            document.getElementById("form_" + secim).style.display = "block";
            document.getElementById("form_" + secim).querySelectorAll("input").forEach(input => input.required = true);
        }
    }

    function autoFormatCard(input) {
        input.value = input.value.replace(/\D/g, '').replace(/(.{4})/g, '$1 ').trim();
    }

    // Grafik için veri hazırlığı
    const odemeMiktarları = <?php echo json_encode($odeme_miktarlari); ?>;
    const odemeTarihleri = <?php echo json_encode($odeme_tarihleri); ?>;

    // Chart.js Grafik Oluşturma
    const ctx = document.getElementById('odemeGrafik').getContext('2d');
    const odemeGrafik = new Chart(ctx, {
        type: 'bar', // Grafik türü: Çubuk Grafik
        data: {
            labels: odemeTarihleri, // X eksenindeki tarihleri etiket olarak kullanıyoruz
            datasets: [{
                label: 'Kredi Yükleme Miktarı',
                data: odemeMiktarları, // Y eksenindeki veriler (ödeme miktarları)
                backgroundColor: 'rgba(54, 162, 235, 0.5)', // Çubuk rengi
                borderColor: 'rgba(54, 162, 235, 1)', // Çubuk kenarlık rengi
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Tarih' // X ekseninin başlığı
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Ödeme Miktarı (₺)' // Y ekseninin başlığı
                    },
                    beginAtZero: true
                }
            }
        }
    });
</script>

</body>
</html>
