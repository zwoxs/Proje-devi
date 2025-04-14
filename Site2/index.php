<?php
session_start();

// Eğer oturumda kullanıcı bilgisi varsa, giriş yapılmış demektir
$user_logged_in = isset($_SESSION['username']) ? true : false;

// Admin kontrolü, admin rolü eklenmeli
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin' ? true : false;

// Ürünleri JSON dosyasından al
$products_json = file_get_contents('products.json');
$products = json_decode($products_json, true);

// Sepet işlemleri
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
if (!isset($_SESSION['user_credit'])) $_SESSION['user_credit'] = 0;

$cart = $_SESSION['cart'];
$total_price = 0;

foreach ($cart as &$item) {
    if (!isset($item['item_quantity'])) {
        $item['item_quantity'] = 1;
    }
    $total_price += $item['item_price'] * $item['item_quantity'];
}

// Sepete ürün ekleme işlemi
if (isset($_POST['add_to_cart'])) {
    $item_id = $_POST['item_id'];
    $item_quantity = 1; // Varsayılan olarak 1 adet ekle

    // Ürünü products.json'dan alalım
    $product_found = false; // Ürün daha önce sepete eklenmiş mi kontrol et

    foreach ($products as $product) {
        if ($product['item_id'] == $item_id) {
            // Aynı ürün zaten sepette var mı kontrol et
            foreach ($_SESSION['cart'] as &$cart_item) {
                if ($cart_item['item_id'] == $item_id) {
                    $cart_item['item_quantity'] += $item_quantity; // Miktarı arttır
                    $product_found = true;
                    break;
                }
            }

            // Eğer ürün sepette yoksa, ekle
            if (!$product_found) {
                $product['item_quantity'] = $item_quantity;
                $_SESSION['cart'][] = $product;
            }
            break;
        }
    }
}

// Sepeti temizleme işlemi
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];
    $cart = [];
    $total_price = 0;
}

// Sepet güncelleme işlemi
if (isset($_POST['update_cart'])) {
    foreach ($cart as &$item) {
        if (isset($_POST['item_quantity'][$item['item_id']])) {
            $new_quantity = max(1, intval($_POST['item_quantity'][$item['item_id']])); 
            $item['item_quantity'] = $new_quantity;
        }
    }
    $_SESSION['cart'] = $cart;
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Box PVP - Ana Sayfa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #181818;
            color: #fff;
        }

        .navbar {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 20px 30px;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            transition: all 0.3s ease;
        }

        .navbar:hover {
            background-color: rgba(0, 0, 0, 1);
        }

        .navbar .logo {
            font-size: 28px;
            font-weight: bold;
            color: #ff9900;
            transition: color 0.3s ease;
        }

        .navbar .logo:hover {
            color: #ffcc00;
        }

        .navbar .nav-links {
            display: flex;
            align-items: center;
        }

        .navbar .nav-links a {
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            margin-left: 20px;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .navbar .nav-links a:hover {
            background-color: #ff9900;
            transform: scale(1.05);
        }

        .navbar .sepet-btn {
            background-color: #ff6600;
            padding: 12px 30px;
            border-radius: 5px;
            color: #fff;
            font-size: 18px;
            transition: background-color 0.3s ease;
            text-align: center;
            display: block;
        }

        .navbar .sepet-btn:hover {
            background-color: #ff4d00;
        }

        /* Ana içerik düzeni */
        .container {
            text-align: center;
            margin-top: 120px;
        }

        h1 {
            font-size: 45px;
            margin-bottom: 20px;
            color: #ff9900;
            text-transform: uppercase;
        }

        p {
            font-size: 20px;
            color: #ccc;
            margin-bottom: 30px;
        }

        /* Mağaza kartları */
        .store-item {
            background-color: #444;
            border-radius: 12px;
            margin: 20px;
            padding: 25px;
            width: 270px;
            display: inline-block;
            vertical-align: top;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.4);
        }

        .store-item:hover {
            transform: translateY(-12px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        }

        .store-item img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .store-item h3 {
            font-size: 24px;
            margin-top: 10px;
            color: #ff9900;
            text-transform: capitalize;
        }

        .store-item p {
            font-size: 18px;
            color: #ccc;
        }

        .store-item button {
            background-color: #ff4d4d;
            color: #fff;
            padding: 14px 22px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            margin-top: 15px;
            transition: background-color 0.3s ease;
        }

        .store-item button:hover {
            background-color: #e60000;
        }

        /* Bildirim Butonu */
        .notification-btn {
            text-align: center;
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 100;
        }

        .notification-btn button {
            background-color: #0077cc;
            padding: 15px 30px;
            border-radius: 8px;
            cursor: pointer;
            color: white;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }

        .notification-btn button:hover {
            background-color: #005fa3;
        }

        /* Responsive düzen */
        @media (max-width: 768px) {
            .store-item {
                width: 100%;
                margin: 15px 0;
            }

            .navbar .nav-links {
                flex-direction: column;
                align-items: flex-start;
                width: 100%;
            }

            .navbar .nav-links a {
                margin-left: 0;
                margin-bottom: 15px;
            }

            .navbar .sepet-btn {
                margin-top: 15px;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div class="logo">Box PVP</div>
    <div class="nav-links">
        <a href="sepet.php" class="sepet-btn">Sepet (<?php echo count($_SESSION['cart']); ?>)</a>
        <?php if ($user_logged_in): ?>
            <!-- Giriş yapmışsa Profil ve Çıkış linklerini göster -->
            <a href="profile.php" class="profil-btn">Profil</a>
            <a href="logout.php" class="logout-btn">Çıkış</a>
        <?php else: ?>
            <!-- Giriş yapmamışsa Giriş ve Kayıt Ol linklerini göster -->
            <a href="login.php">Giriş</a>
            <a href="register.php">Kayıt Ol</a>
        <?php endif; ?>
        <a href="iletisim.php" style="background-color:#0077cc;">Destek</a>
    </div>
</div>

<!-- Ana Sayfa İçeriği -->
<div class="container">
    <h1>Box PVP'ye Hoş Geldin!</h1>
    <p>Gerçek PvP savaşlarına katılmaya hazır mısın?</p>

    <!-- Mağaza Kartları -->
    <?php foreach ($products as $product): ?>
        <div class="store-item">
            <h3><?php echo $product['item_name']; ?></h3>
            <p>Fiyat: <?php echo $product['item_price']; ?> Kredi</p>
            <form action="index.php" method="POST">
                <input type="hidden" name="item_id" value="<?php echo $product['item_id']; ?>">
                <button type="submit" name="add_to_cart">Sepete Ekle</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>

<!-- Bildirim Butonu -->
<div class="notification-btn">
    <a href="notifications.php">
        <button>Bildirimlere Git</button>
    </a>
</div>

<!-- Footer -->
<footer style="background-color: #111; padding: 40px 20px; text-align: center; color: #aaa; margin-top: 100px; border-top: 2px solid #333;">
    <div style="max-width: 1000px; margin: auto;">
        <h2 style="color: #ff9900; margin-bottom: 20px;">Box PVP</h2>
        <p style="font-size: 16px; margin-bottom: 10px;">Efsanevi PvP deneyimi için doğru yerdesin.</p>
        <p style="font-size: 14px;">© 2025 Box PVP. Tüm hakları saklıdır.</p>
    </div>
</footer>

</body>
</html>
