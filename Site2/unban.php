<?php
session_start();

// Admin kontrolü yapalım
if (!isset($_SESSION["username"]) || $_SESSION["username"] !== "admin") {
    header("Location: login.php"); // Admin değilse login sayfasına yönlendir
    exit;
}

// Unbanlanacak kullanıcıyı alalım
if (isset($_GET['username'])) {
    $username = $_GET['username'];
    
    // Banned kullanıcılar dosyasını oku
    $banned_file = 'banned_users.txt';
    $banned_users = file($banned_file, FILE_IGNORE_NEW_LINES);

    // Eğer kullanıcı banned_users.txt içinde varsa, unban işlemi yapalım
    if (in_array($username, $banned_users)) {
        // Banned kullanıcıyı dosyadan çıkartalım
        $banned_users = array_diff($banned_users, [$username]);
        file_put_contents($banned_file, implode("\n", $banned_users));

        // Kullanıcıyı users.txt dosyasına ekleyelim
        $users_file = 'users.txt';
        $users = file($users_file, FILE_IGNORE_NEW_LINES);
        $users[] = "$username:123"; // Şifreyi sabit "123" olarak ekliyoruz (gerekirse farklı şekilde düzenleyebilirsiniz)
        file_put_contents($users_file, implode("\n", $users));

        echo "<div class='success-message'>$username başarıyla unbanlandı.</div>";
        header("Refresh: 2; url=admin.php"); // Admin paneline yönlendir
    } else {
        echo "<div class='error-message'>$username zaten banlı değil.</div>";
        header("Refresh: 2; url=admin.php"); // Admin paneline yönlendir
    }
}
?>
