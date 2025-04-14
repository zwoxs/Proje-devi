<?php
session_start();

// Giriş kontrolü
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];

// Kullanıcı var mı kontrolü (users.txt'den sadece username kısmı karşılaştırılıyor)
$kullanici_var = false;

if (file_exists('users.txt')) {
    $satirlar = file('users.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($satirlar as $satir) {
        $parcalar = explode(':', $satir); // satırı kullanıcı adı ve şifre olarak ayır
        if (trim($parcalar[0]) === $username) {
            $kullanici_var = true;
            break;
        }
    }
}

// Bildirimleri yükle (eğer kullanıcı varsa)
$mesajlar = [];

if ($kullanici_var && file_exists('mesajlar.json')) {
    $tumMesajlar = json_decode(file_get_contents('mesajlar.json'), true);

    foreach ($tumMesajlar as $mesaj) {
        if (isset($mesaj['ad']) && $mesaj['ad'] === $username) {
            $mesajlar[] = $mesaj;
        }
    }
}

// Admin Bildirimi Gönderme İşlemi
$notificationsFile = "notifications.json";
$notifications = file_exists($notificationsFile) ? json_decode(file_get_contents($notificationsFile), true) : [];

// Admin paneli bildirim gönderme
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['yeniBildirim'])) {
    $yeniBildirim = $_POST['yeniBildirim'];
    $notifications[] = ["bildirim" => $yeniBildirim, "tarih" => date("Y-m-d H:i:s")];
    file_put_contents($notificationsFile, json_encode($notifications, JSON_PRETTY_PRINT));
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bildirimler</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #1c1c1c;
            color: #fff;
        }

        .navbar {
            background-color: #333;
            padding: 15px 30px;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            margin-left: 20px;
            padding: 10px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .navbar a:hover {
            background-color: #555;
        }

        .container {
            text-align: center;
            margin-top: 80px;
        }

        h1 {
            color: #ff9900;
        }

        .notification-item {
            background-color: #444;
            padding: 20px;
            border-radius: 10px;
            margin: 20px auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            text-align: left;
        }

        .notification-item h3 {
            color: #ff9900;
            margin-bottom: 10px;
        }

        .notification-item p {
            color: #ccc;
            margin: 5px 0;
        }

        textarea {
            width: 80%;
            padding: 10px;
            margin-top: 20px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        button {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <a href="index.php">Ana Sayfa</a>
    <a href="sepet.php">Sepet</a>
</div>

<!-- Bildirimler -->
<div class="container">
    <h1>Bildirimler</h1>

    <?php if ($kullanici_var && !empty($mesajlar)): ?>
        <?php foreach ($mesajlar as $mesaj): ?>
            <div class="notification-item">
                <h3>Merhaba <?= htmlspecialchars($username) ?>, cevabın geldi!</h3>
                <p><strong>Gönderdiğin Mesaj:</strong> <?= htmlspecialchars($mesaj['mesaj']) ?></p>
                <p><strong>Adminin Cevabı:</strong> <?= htmlspecialchars($mesaj['cevap']) ?></p>
                <p><em><?= htmlspecialchars($mesaj['zaman']) ?></em></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Henüz bir bildiriminiz yok.</p>
    <?php endif; ?>

    <!-- Admin için Bildirim Gönderme Formu -->
    <?php if ($username === 'admin'): ?>
        <h2>Yeni Bildirim Gönder</h2>
        <form method="POST">
            <textarea name="yeniBildirim" placeholder="Bildirim mesajınızı buraya yazın..." required></textarea>
            <button type="submit">Bildirim Gönder</button>
        </form>
    <?php endif; ?>

    <!-- Admin Bildirimleri -->
    <h2>Admin Bildirimleri</h2>
    <?php if (!empty($notifications)): ?>
        <?php foreach ($notifications as $n): ?>
            <div class="notification-item">
                <p><strong>Bildirim:</strong> <?= htmlspecialchars($n["bildirim"]) ?></p>
                <p><em>Gönderildi: <?= htmlspecialchars($n["tarih"]) ?></em></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Henüz admin tarafından bir bildirim gönderilmemiş.</p>
    <?php endif; ?>
</div>

</body>
</html>
