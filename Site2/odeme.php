<?php
session_start();

// Varsayılan değerler
$kartNumarasi = $sonKullanmaTarihi = $cvv = $kredi = '';
$errorMessage = '';

// Kredi ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kartNumarasi = isset($_POST['kartNumarasi']) ? trim($_POST['kartNumarasi']) : '';
    $sonKullanmaTarihi = isset($_POST['sonKullanmaTarihi']) ? trim($_POST['sonKullanmaTarihi']) : '';
    $cvv = isset($_POST['cvv']) ? trim($_POST['cvv']) : '';
    $kredi = isset($_POST['kredi']) ? trim($_POST['kredi']) : '';

    // Doğrulama: Boş alanları kontrol et
    if (empty($kartNumarasi) || empty($sonKullanmaTarihi) || empty($cvv) || empty($kredi)) {
        $errorMessage = "Lütfen tüm alanları doldurduğunuzdan emin olun.";
    } else {
        // Son Kullanma Tarihi formatı kontrolü (MM/YY)
        if (!preg_match('/^\d{2}\/\d{2}$/', $sonKullanmaTarihi)) {
            $errorMessage = "Son kullanma tarihi formatı geçersiz. Lütfen MM/YY formatında girin.";
        }
        // CVV kontrolü
        elseif (!preg_match('/^\d{3,4}$/', $cvv)) {
            $errorMessage = "Geçersiz CVV numarası.";
        }
        // Kredi miktarı kontrolü
        elseif (!is_numeric($kredi) || $kredi <= 0) {
            $errorMessage = "Geçersiz kredi miktarı.";
        } else {
            // Kredi ekleme
            if (!isset($_SESSION['credits'])) {
                $_SESSION['credits'] = 0; // Eğer krediler sıfırsa başlat
            }
            $_SESSION['credits'] += (int)$kredi; // Krediyi ekle

            // Ödeme başarılı, yönlendirme
            header("Location: odeme_basari.php"); // Ödeme başarılı sayfasına yönlendir
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ödeme Sayfası</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: white;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            border-top: 6px solid #0077cc;
        }
        h2 {
            text-align: center;
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .input-group {
            margin-bottom: 20px;
        }
        .input-group label {
            display: block;
            font-size: 16px;
            margin-bottom: 8px;
            color: #555;
        }
        .input-group input {
            width: 100%;
            padding: 14px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            transition: border 0.3s ease;
        }
        .input-group input:focus {
            border-color: #0077cc;
            outline: none;
        }
        .error-message {
            color: #e74c3c;
            font-size: 14px;
            text-align: center;
            margin-top: 10px;
            font-weight: bold;
        }
        .btn-submit {
            width: 100%;
            padding: 14px;
            background-color: #0077cc;
            color: white;
            font-size: 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-submit:hover {
            background-color: #005fa3;
        }
        .btn-submit:active {
            background-color: #004a87;
        }
        .form-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Ödeme Yap</h2>

    <?php if (!empty($errorMessage)): ?>
        <div class="error-message"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <form method="POST" action="odeme.php">
        <div class="input-group">
            <label for="kartNumarasi">Kart Numarası:</label>
            <input type="text" id="kartNumarasi" name="kartNumarasi" value="<?php echo htmlspecialchars($kartNumarasi); ?>" required>
        </div>

        <div class="input-group">
            <label for="sonKullanmaTarihi">Son Kullanma Tarihi (MM/YY):</label>
            <input type="text" id="sonKullanmaTarihi" name="sonKullanmaTarihi" value="<?php echo htmlspecialchars($sonKullanmaTarihi); ?>" required>
        </div>

        <div class="input-group">
            <label for="cvv">CVV:</label>
            <input type="text" id="cvv" name="cvv" value="<?php echo htmlspecialchars($cvv); ?>" required>
        </div>

        <div class="input-group">
            <label for="kredi">Kredi Miktarı:</label>
            <input type="number" id="kredi" name="kredi" value="<?php echo htmlspecialchars($kredi); ?>" required>
        </div>

        <button type="submit" class="btn-submit">Ödeme Yap</button>
    </form>

    <div class="form-footer">
        <p>Ödeme işleminizi güvenle tamamlayabilirsiniz.</p>
    </div>
</div>

</body>
</html>
