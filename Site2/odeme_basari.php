<?php
session_start();

// Ödeme başarılı mı?
if (!isset($_SESSION['payment_success']) || !$_SESSION['payment_success']) {
    header('Location: odeme.php'); // Eğer ödeme başarılı değilse, ödeme sayfasına yönlendir
    exit;
}

// Ödeme işlemi sonrasında başarılı olduğunu bildiren mesajı göster
unset($_SESSION['payment_success']); // Ödeme simülasyonu başarı mesajını temizle

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ödeme Başarı</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        .success-container {
            margin-top: 100px;
            padding: 40px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .success-container h2 {
            color: #28a745;
            margin-bottom: 20px;
        }

        .success-container p {
            font-size: 18px;
            color: #333;
        }

        .btn {
            padding: 12px 20px;
            font-size: 18px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="success-container">
    <h2>Ödemeniz Başarıyla Alındı!</h2>
    <p>Ödeme işleminiz başarıyla simüle edilmiştir. Gerçek ödeme yapılmamıştır.</p>
    <a href="index.php" class="btn">Ana Sayfaya Git</a>
</div>

</body>
</html>
