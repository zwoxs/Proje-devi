<?php
session_start();

// Oturumdan kullanıcı girişi kontrolü
$user_logged_in = isset($_SESSION['username']);

// Ürünler JSON'dan alınır
$products = json_decode(file_get_contents('products.json'), true);

// Sepet başlatılır
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
if (!isset($_SESSION['user_credit'])) $_SESSION['user_credit'] = 0;

$cart = &$_SESSION['cart'];
$total_price = 0;

// Ürün ekleme işlemi
if (isset($_POST['add_to_cart']) && isset($_POST['item_id'])) {
    $item_id = intval($_POST['item_id']);
    $item_quantity = 1;

    foreach ($products as $product) {
        if ($product['item_id'] == $item_id) {
            $found = false;
            foreach ($cart as &$cart_item) {
                if ($cart_item['item_id'] == $item_id) {
                    $cart_item['item_quantity'] += $item_quantity;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $product['item_quantity'] = $item_quantity;
                $cart[] = $product;
            }
            break;
        }
    }
}

// Sepeti temizleme
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];
    $cart = [];
}

// Sepeti güncelleme
if (isset($_POST['update_cart']) && isset($_POST['item_quantity'])) {
    foreach ($cart as &$item) {
        if (isset($_POST['item_quantity'][$item['item_id']])) {
            $new_quantity = max(1, intval($_POST['item_quantity'][$item['item_id']]));
            $item['item_quantity'] = $new_quantity;
        }
    }
    header("Location: sepet.php");
    exit();
}

// Toplam fiyat hesapla
foreach ($cart as $item) {
    $total_price += $item['item_price'] * $item['item_quantity'];
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sepet</title>
    <style>
        body {
            background-color: #121212;
            color: #fff;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #333;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 100;
        }

        .navbar .logo {
            font-size: 24px;
            font-weight: bold;
            color: #ff9900;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            margin-left: 20px;
            font-size: 18px;
        }

        .cart-container {
            padding: 100px 20px;
            text-align: center;
        }

        .cart-item {
            background: #333;
            padding: 20px;
            border-radius: 10px;
            margin: 15px;
            display: inline-block;
            width: 250px;
        }

        .cart-item input {
            width: 50px;
            text-align: center;
        }

        .btn {
            background-color: #ff9900;
            border: none;
            color: white;
            padding: 12px 25px;
            font-size: 16px;
            border-radius: 5px;
            margin: 10px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #ff6600;
        }

        .total {
            font-size: 20px;
            margin-top: 20px;
            color: #ffcc00;
        }

        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #222;
            color: white;
            padding: 30px;
            border-radius: 10px;
            z-index: 999;
            box-shadow: 0 0 15px #000;
            text-align: center;
        }

        .popup h2 {
            color: #ff9900;
        }

        .popup button {
            margin-top: 20px;
            background-color: #ff9900;
        }
    </style>
</head>
<body>

<div class="navbar">
<a href="index.php" class="logo">Box PVP</a>
<h1>1 Kredi 0.50 TL Kampanya</h1>
    <a href="kredi_yukle.php">Kredi Yükle</a>
</div>

<div class="cart-container">
    <h1>Sepetiniz</h1>

    <?php if (empty($cart)): ?>
        <p>Sepetiniz boş.</p>
    <?php else: ?>
        <form method="POST">
            <?php foreach ($cart as $item): ?>
                <div class="cart-item">
                    <h3><?= $item['item_name'] ?></h3>
                    <p>Fiyat: <?= $item['item_price'] ?> Kredi</p>
                    <p>Adet:
                        <input type="number" name="item_quantity[<?= $item['item_id'] ?>]" value="<?= $item['item_quantity'] ?>" min="1">
                    </p>
                </div>
            <?php endforeach; ?>
            <div class="total">
                <p><strong>Toplam Tutar: <?= $total_price ?> Kredi</strong></p>
                <p><strong>Mevcut Kredi: <?= $_SESSION['user_credit'] ?> Kredi</strong></p>
            </div>
            <button type="submit" name="update_cart" class="btn">Sepeti Güncelle</button>
        </form>

        <form method="POST">
            <button type="submit" name="clear_cart" class="btn">Sepeti Temizle</button>
        </form>

        <button class="btn" onclick="odemePopup()">Ödeme Yap</button>
    <?php endif; ?>
</div>

<!-- Ödeme Popup -->
<div id="odemePenceresi" class="popup">
    <h2>Ödeme Ekranı</h2>
    <p>Toplam: <?= $total_price ?> Kredi</p>
    <p>Mevcut Kredi: <?= $_SESSION['user_credit'] ?> Kredi</p>
    <?php if ($_SESSION['user_credit'] >= $total_price): ?>
        <?php $_SESSION['user_credit'] -= $total_price; $_SESSION['cart'] = []; ?>
        <p style="color:#0f0;">✅ Ödeme başarılı! Yeni bakiyen: <?= $_SESSION['user_credit'] ?> Kredi</p>
    <?php else: ?>
        <p style="color:#f00;">❌ Yetersiz bakiye. Lütfen kredi yükleyin.</p>
    <?php endif; ?>
    <button onclick="kapatPopup()">Kapat</button>
</div>

<script>
    function odemePopup() {
        document.getElementById("odemePenceresi").style.display = "block";
    }

    function kapatPopup() {
        window.location.href = "sepet.php";
    }
</script>

</body>
</html>
