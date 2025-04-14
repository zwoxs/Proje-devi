<?php
session_start();

if (!isset($_SESSION["username"]) || $_SESSION["username"] !== "admin") {
    header("Location: login.php");
    exit;
}

function log_action($action) {
    file_put_contents("logs.txt", date("Y-m-d H:i:s") . " - $action\n", FILE_APPEND);
}

$file = 'users.txt';
$banned_file = 'banned_users.txt';

$users = file_exists($file) ? array_filter(file($file, FILE_IGNORE_NEW_LINES), 'strlen') : [];
$banned_users = file_exists($banned_file) ? array_filter(file($banned_file, FILE_IGNORE_NEW_LINES), 'strlen') : [];

function get_user_data($username) {
    return [
        'username' => $username,
        'registered' => '2024-01-01',
        'last_ip' => '192.168.1.1',
        'last_active' => '2025-04-08 22:00:00'
    ];
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #212121;
            color: #fff;
        }
        .container {
            width: 80%;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            color: #9C27B0;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #555;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #333;
        }
        tr:nth-child(even) {
            background-color: #444;
        }
        .action-btn {
            color: #fff;
            background-color: #9C27B0;
            padding: 6px 12px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            text-decoration: none;
        }
        .action-btn:hover {
            background-color: #7b1fa2;
        }
        .success-message, .error-message {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
        }
        .success-message { background: #4caf50; }
        .error-message { background: #f44336; }
        .search-box {
            margin-top: 20px;
            text-align: center;
        }
        input[type="text"] {
            padding: 8px;
            width: 50%;
            border-radius: 4px;
            border: none;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Admin Paneli</h1>
    <a href="logout.php" class="action-btn">Çıkış Yap</a>

    <?php if (isset($_GET['msg'])): ?>
        <div class="success-message"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <div class="search-box">
        <input type="text" id="search" placeholder="Kullanıcı Ara...">
    </div>

    <h2>Banlı Kullanıcılar</h2>
    <form action="toplu_unban.php" method="post">
        <table id="banned-table">
            <thead>
                <tr><th></th><th>Kullanıcı Adı</th><th>İşlem</th></tr>
            </thead>
            <tbody>
                <?php foreach ($banned_users as $banned_user): ?>
                    <tr>
                        <td><input type="checkbox" name="selected_users[]" value="<?= $banned_user ?>"></td>
                        <td><?= $banned_user ?></td>
                        <td>
                            <a href='unban.php?username=<?= $banned_user ?>' class='action-btn'>Unbanla</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" class="action-btn">Seçilenleri Unbanla</button>
    </form>

    <h2>Aktif Kullanıcılar</h2>
    <form action="toplu_ban.php" method="post">
        <table id="user-table">
            <thead>
                <tr><th></th><th>Kullanıcı Adı</th><th>İşlem</th></tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <?php list($username, $password) = explode(':', $user); ?>
                    <tr>
                        <td><input type="checkbox" name="selected_users[]" value="<?= $username ?>"></td>
                        <td><?= $username ?></td>
                        <td>
                            <a href='ban.php?username=<?= $username ?>' class='action-btn'>Banla</a>
                            <a href='kick.php?username=<?= $username ?>' class='action-btn'>Kickle</a>
                            <a href='reset_password.php?username=<?= $username ?>' class='action-btn'>Şifre Sıfırla</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" class="action-btn">Seçilenleri Banla</button>
    </form>

    <h2>Yeni Kullanıcı Ekle</h2>
    <form action="kullanici_ekle.php" method="post">
        <input type="text" name="username" placeholder="Kullanıcı adı" required>
        <input type="password" name="password" placeholder="Şifre" required>
        <button type="submit" class="action-btn">Ekle</button>
    </form>

    <h2>İşlem Logları</h2>
    <a href="logs.txt" class="action-btn" target="_blank">Logları Görüntüle</a>
</div>

<script>
    document.getElementById('search').addEventListener('input', function() {
        let val = this.value.toLowerCase();
        document.querySelectorAll('#user-table tbody tr, #banned-table tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(val) ? '' : 'none';
        });
    });
</script>

</body>
</html>
