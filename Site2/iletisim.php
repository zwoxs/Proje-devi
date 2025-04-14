<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ad = $_POST["ad"];
    $mesaj = $_POST["mesaj"];
    $zaman = date("d.m.Y H:i:s"); // ZAMAN formatı

    $veri = [
        "ad" => $ad,
        "mesaj" => $mesaj,
        "zaman" => $zaman, // DÜZENLENDİ: "tarih" değil "zaman"
        "cevap" => ""
    ];

    $dosya = "mesajlar.json";
    $mevcut = file_exists($dosya) ? json_decode(file_get_contents($dosya), true) : [];
    $mevcut[] = $veri;
    file_put_contents($dosya, json_encode($mevcut, JSON_PRETTY_PRINT));
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Destek Talebi</title>
    <style>
        body {
            background-color: #1e1e1e;
            font-family: Arial, sans-serif;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #121212;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .navbar h1 {
            color: #ff9900;
            margin: 0;
            font-size: 24px;
        }

        .navbar ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .navbar ul li {
            margin-left: 25px;
        }

        .navbar ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        .navbar ul li a:hover {
            color: #ff6600;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 80px);
        }

        form {
            background-color: #2c2c2c;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 400px;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            margin-bottom: 20px;
            border: none;
            border-radius: 8px;
            background-color: #3a3a3a;
            color: #fff;
            font-size: 16px;
        }

        button {
            background-color: #ff9900;
            color: #fff;
            padding: 12px;
            width: 100%;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            background-color: #ff6600;
            transform: scale(1.03);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #ff9900;
        }

        @media screen and (max-width: 600px) {
            .navbar ul {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>

    <div class="navbar">
        <h1>Box PvP</h1>
        <ul>
            <li><a href="index.php">Ana Sayfa</a></li>
            <li><a href="iletisim.php">İletişim</a></li>
            <li><a href="magaza.php">Mağaza</a></li>
        </ul>
    </div>

    <div class="container">
        <form method="POST">
            <h2>Destek Talebi Oluştur</h2>
            <label>Adınız:</label>
            <input type="text" name="ad" required>
            <label>Mesajınız:</label>
            <textarea name="mesaj" rows="5" required></textarea>
            <button type="submit">Gönder</button>
        </form>
    </div>

</body>
</html>
