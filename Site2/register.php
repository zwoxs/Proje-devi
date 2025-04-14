<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Form verilerini al
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Kullanıcı adı ve şifre boş olmamalı
    if (empty($username) || empty($password) || empty($confirm_password)) {
        echo "<div class='error-message'>Tüm alanlar zorunludur.</div>";
        exit;
    }

    // Şifreler eşleşmeli
    if ($password !== $confirm_password) {
        echo "<div class='error-message'>Şifreler eşleşmiyor.</div>";
        exit;
    }



    $file = 'users.txt';

    // Dosya yoksa yeni oluştur
    if (!file_exists($file)) {
        file_put_contents($file, "");
    }

    // Kullanıcı adı kontrolü
    $users = file($file, FILE_IGNORE_NEW_LINES);
    foreach ($users as $user) {
        list($saved_username, $saved_password) = explode(':', $user);
        if ($saved_username == $username) {
            echo "<div class='error-message'>Bu kullanıcı adı zaten alınmış.</div>";
            exit;
        }
    }

    // Şifreyi hashle
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Yeni kullanıcıyı dosyaya ekle
    file_put_contents($file, $username . ':' . $hashed_password . PHP_EOL, FILE_APPEND);

    echo "<div class='success-message'>Kayıt başarılı! Yönlendiriliyorsunuz...</div>";
    header("Refresh: 2; url=login.php");
}
?>

<!-- Kayıt Formu -->
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #212121;
            margin: 0;
            color: #f0f0f0;
            overflow-x: hidden; /* Yatay kaymayı engeller */
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

        /* Register Form */
        .register-container {
            background-color: #333;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 450px;
            margin: 60px auto;
            animation: slideUp 0.5s ease-out;
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
            transition: background-color 0.3s ease-in-out;
        }

        button:hover {
            background-color: #7B1FA2;
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

        /* Animation for sliding up */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Göz ikonu için stil */
        .eye-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9C27B0;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div class="brand">WebSite</div>
    <div class="menu">
        <a href="index.php">Ana Sayfa</a>
        <a href="login.php">Giriş Yap</a>
        <a href="register.php">Kayıt Ol</a>
    </div>
</div>

<!-- Kayıt Formu -->
<div class="register-container">
    <h2>Kayıt Ol</h2>
    <form method="POST" action="register.php">
        <label for="username">Kullanıcı Adı:</label>
        <input type="text" name="username" required placeholder="Kullanıcı Adınızı Girin">

        <label for="password">Şifre:</label>
        <div style="position: relative;">
            <input type="password" name="password" id="password" required placeholder="Şifrenizi Girin">
            <i class="eye-icon" id="togglePassword">👁️</i>
        </div>

        <label for="confirm_password">Şifre Tekrarı:</label>
        <div style="position: relative;">
            <input type="password" name="confirm_password" id="confirm_password" required placeholder="Şifrenizi Tekrar Girin">
            <i class="eye-icon" id="toggleConfirmPassword">👁️</i>
        </div>

        <button type="submit">Kayıt Ol</button>
    </form>
    <div class="form-footer">
        <p>Hesabınız var mı? <a href="login.php">Giriş Yapın</a></p>
    </div>
</div>

<!-- JavaScript -->
<script>
    // Şifreyi göster/gizle işlevi
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const confirmPassword = document.getElementById('confirm_password');

    togglePassword.addEventListener('click', function () {
        const type = password.type === 'password' ? 'text' : 'password';
        password.type = type;
        this.textContent = type === 'password' ? '👁️' : '🙈';
    });

    toggleConfirmPassword.addEventListener('click', function () {
        const type = confirmPassword.type === 'password' ? 'text' : 'password';
        confirmPassword.type = type;
        this.textContent = type === 'password' ? '👁️' : '🙈';
    });
</script>

</body>
</html>
