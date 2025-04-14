<?php
session_start(); // Oturumu başlatıyoruz

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Form verilerini al
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Kullanıcı adı ve şifre boş olmamalı
    if (empty($username) || empty($password)) {
        echo "<div class='error-message'>Tüm alanlar zorunludur.</div>";
        exit;
    }

    // Kullanıcıyı dosyadan kontrol et
    $file = 'users.txt';
    if (!file_exists($file)) {
        echo "<div class='error-message'>Kullanıcı bulunamadı.</div>";
        exit;
    }

    $users = file($file, FILE_IGNORE_NEW_LINES);
    $user_found = false;
    
    // Admin kontrolü
    if ($username == 'admin' && $password == '123') {
        // Admin olarak giriş yapıldı
        $_SESSION['username'] = 'admin'; // Admin oturumu başlatıyoruz
        echo "<div class='success-message'>Admin olarak giriş başarılı! Yönlendiriliyorsunuz...</div>";
        header("Refresh: 2; url=admin.php");
        exit;
    }

    // Normal kullanıcı kontrolü
    foreach ($users as $user) {
        list($saved_username, $saved_password) = explode(':', $user);
        if ($saved_username == $username) {
            // Şifreyi kontrol et (hash'li şifre kontrolü)
            if (password_verify($password, $saved_password)) {
                $_SESSION['username'] = $username; // Oturumu başlatıyoruz
                echo "<div class='success-message'>Giriş başarılı! Yönlendiriliyorsunuz...</div>";
                header("Refresh: 2; url=index.php"); // Ana sayfaya yönlendir
                $user_found = true;
                break;
            } else {
                echo "<div class='error-message'>Şifre yanlış.</div>";
                exit;
            }
        }
    }

    if (!$user_found) {
        echo "<div class='error-message'>Kullanıcı adı bulunamadı.</div>";
        exit;
    }
}
?>

<!-- Giriş Formu -->
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #212121;
            margin: 0;
            color: #f0f0f0;
            overflow-x: hidden;
        }

        /* Navbar */
        .navbar {
            background-color: #333;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 12px 18px;
            font-size: 18px;
            transition: background-color 0.3s ease-in-out;
        }

        .navbar a:hover {
            background-color: #9C27B0;
            border-radius: 4px;
        }

        .navbar .brand {
            font-size: 26px;
            font-weight: bold;
            color: #9C27B0;
            letter-spacing: 1px;
        }

        .navbar .menu {
            display: flex;
        }

        /* Login Form */
        .login-container {
            background-color: #333;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 450px;
            margin: 60px auto;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #9C27B0;
            font-size: 28px;
            font-weight: bold;
        }

        label {
            font-size: 18px;
            margin-bottom: 8px;
            display: block;
            color: #fff;
        }

        input {
            width: 100%;
            padding: 14px;
            margin-bottom: 15px;
            border: 1px solid #555;
            border-radius: 4px;
            font-size: 16px;
            background-color: #444;
            color: #fff;
        }

        input:focus {
            border-color: #9C27B0;
            background-color: #555;
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: #9C27B0;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 18px;
            cursor: pointer;
        }

        .error-message {
            color: #f44336;
            font-size: 14px;
            text-align: center;
            margin-top: 20px;
        }

        .success-message {
            color: #4CAF50;
            font-size: 14px;
            text-align: center;
            margin-top: 20px;
        }

        .form-footer {
            text-align: center;
            margin-top: 20px;
        }

        .form-footer a {
            color: #9C27B0;
            text-decoration: none;
            font-size: 16px;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div class="brand">WebSite</div>
    <div class="menu">
        <a href="index.php">Ana Sayfa</a>
        <?php if (isset($_SESSION['username'])): ?>
            <!-- Eğer giriş yaptıysa, 'Giriş Yap' ve 'Kayıt Ol' linklerini gizle -->
            <a href="logout.php">Çıkış Yap</a>
        <?php else: ?>
            <a href="login.php">Giriş Yap</a>
            <a href="register.php">Kayıt Ol</a>
        <?php endif; ?>
    </div>
</div>

<!-- Giriş Formu -->
<div class="login-container">
    <h2>Giriş Yap</h2>
    <form method="POST" action="login.php">
        <label for="username">Kullanıcı Adı:</label>
        <input type="text" name="username" required placeholder="Kullanıcı Adınızı Girin">

        <label for="password">Şifre:</label>
        <input type="password" name="password" id="password" required placeholder="Şifrenizi Girin">

        <button type="submit">Giriş Yap</button>
    </form>
    <div class="form-footer">
        <p>Hesabınız yok mu? <a href="register.php">Kayıt Olun</a></p>
    </div>
</div>

</body>
</html>
