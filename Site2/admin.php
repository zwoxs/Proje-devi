<?php
$dosya = "mesajlar.json";
$mesajlar = file_exists($dosya) ? json_decode(file_get_contents($dosya), true) : [];
$cevapsizSayisi = count(array_filter($mesajlar, fn($m) => empty($m["cevap"])));

// Bildirim dosyasını yükle
$notificationsFile = "notifications.json";
$notifications = file_exists($notificationsFile) ? json_decode(file_get_contents($notificationsFile), true) : [];

// POST işlemleri
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Mesaj silme
    if (isset($_POST["sil"]) && isset($_POST["index"])) {
        $index = $_POST["index"];
        unset($mesajlar[$index]);
        $mesajlar = array_values($mesajlar); // indeksleri sıfla
        file_put_contents($dosya, json_encode($mesajlar, JSON_PRETTY_PRINT));
        header("Location: admin.php");
        exit;
    }

    // Cevap verme
    if (isset($_POST["cevap"]) && isset($_POST["index"])) {
        $index = $_POST["index"];
        $cevap = $_POST["cevap"];
        $mesajlar[$index]["cevap"] = $cevap;
        file_put_contents($dosya, json_encode($mesajlar, JSON_PRETTY_PRINT));
        header("Location: admin.php");
        exit;
    }

    // Tüm kullanıcılara bildirim gönderme
    if (isset($_POST["yeniBildirim"])) {
        $yeniBildirim = $_POST["yeniBildirim"];
        // Yeni bildirimi "notifications.json" dosyasına ekle
        $notifications[] = ["bildirim" => $yeniBildirim, "tarih" => date("Y-m-d H:i:s")];
        file_put_contents($notificationsFile, json_encode($notifications, JSON_PRETTY_PRINT));
        header("Location: notifications.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Genel stil */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
            color: #333;
            transition: background-color 0.3s, color 0.3s;
        }

        nav {
            background-color: #2c3e50;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        nav .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav .nav-logo {
            color: #ecf0f1;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        nav .nav-links {
            list-style: none;
            display: flex;
            margin: 0;
        }

        nav .nav-links li {
            margin-left: 20px;
        }

        nav .nav-links li a {
            color: #ecf0f1;
            text-decoration: none;
            font-size: 16px;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        nav .nav-links li a:hover {
            background-color: #34495e;
        }

        .container {
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }

        h2 {
            font-size: 28px;
            color: #34495e;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .cevap-button {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 12px 20px;
            margin-top: 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .cevap-button:hover {
            background-color: #2980b9;
        }

        textarea {
            width: 100%;
            padding: 15px;
            margin-top: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
            resize: vertical;
            box-sizing: border-box;
        }

        textarea:focus {
            outline: none;
            border-color: #3498db;
        }

        .mesaj-kutu {
            background-color: #ecf0f1;
            border-left: 5px solid #3498db;
            padding: 15px;
            margin-top: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .mesaj-kutu strong {
            color: #2c3e50;
            font-size: 18px;
        }

        .mesaj-kutu p {
            color: #34495e;
        }

        .mesaj-kutu button {
            background-color: #e74c3c;
            color: #fff;
            border: none;
            padding: 8px 15px;
            margin-top: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .mesaj-kutu button:hover {
            background-color: #c0392b;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            nav .nav-container {
                flex-direction: column;
                align-items: flex-start;
            }

            nav .nav-logo {
                margin-bottom: 10px;
            }

            .container {
                padding: 20px;
            }

            h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>

<nav>
    <div class="nav-container">
        <div class="nav-logo">🔧 Admin Paneli</div>
        <ul class="nav-links">
            <li><a href="index.php">🏠 Ana Sayfa</a></li>
            <li><a href="admin.php">📨 Mesajlar</a></li>
            <li><a href="notifications.php">🔔 Bildirimler</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <h2>🔔 Tüm Kullanıcılara Bildirim Gönder</h2>
    <form method="POST">
        <textarea name="yeniBildirim" placeholder="Bildirim mesajınızı buraya yazın..." required></textarea>
        <button type="submit" class="cevap-button">Bildirim Gönder</button>
    </form>

    <h2>📨 Gelen Mesajlar</h2>
    <?php if (empty($mesajlar)): ?>
        <p>Henüz mesaj gönderilmemiş.</p>
    <?php else: ?>
        <?php foreach (array_reverse($mesajlar) as $i => $m): ?>
            <div class="mesaj-kutu">
                <strong><?= htmlspecialchars($m["ad"]) ?></strong>
                <p><?= htmlspecialchars($m["mesaj"]) ?></p>
                <form method="POST">
                    <textarea name="cevap" placeholder="Cevabınızı buraya yazın..." required></textarea>
                    <button type="submit" name="index" value="<?= $i ?>" class="cevap-button">Cevap Ver</button>
                </form>
                <form method="POST" style="margin-top: 10px;">
                    <button type="submit" name="sil" value="1" class="cevap-button">Sil</button>
                    <input type="hidden" name="index" value="<?= $i ?>">
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
