<?php
session_start();

// Admin kontrolü yapalım
if (!isset($_SESSION["username"]) || $_SESSION["username"] !== "admin") {
    header("Location: login.php"); // Admin değilse login sayfasına yönlendir
    exit;
}

// Banlanacak kullanıcıyı alalım
if (isset($_GET['username'])) {
    $username = $_GET['username'];
    $file = 'users.txt';

    if (!file_exists($file)) {
        echo "<div class='error-message'>Kullanıcı bulunamadı.</div>";
        exit;
    }

    // Kullanıcıları dosyadan oku
    $users = file($file, FILE_IGNORE_NEW_LINES);
    $updated_users = [];

    // Kullanıcıyı banla (Silme işlemi)
    foreach ($users as $user) {
        list($saved_username, $saved_password) = explode(':', $user);
        if ($saved_username !== $username) {
            $updated_users[] = $user; // Banlanmamış kullanıcıları ekle
        }
    }

    // Güncellenmiş kullanıcıları dosyaya yaz
    file_put_contents($file, implode("\n", $updated_users));

    echo "<div class='success-message'>$username başarıyla banlandı.</div>";
    header("Refresh: 2; url=admin.php"); // Admin paneline yönlendir
}
?>
