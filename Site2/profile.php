<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$profileFile = 'profile.json';
$profiles = file_exists($profileFile) ? json_decode(file_get_contents($profileFile), true) : [];

if (!isset($profiles[$username])) {
    $profiles[$username] = [
        'username' => $username,
        'email' => '',
        'join_date' => date("d/m/Y"),
        'profile_picture' => 'uploads/default_profile_picture.jpg',
        'badges' => ['Yeni Üye'] // Başlangıç rozeti
    ];
}

$user_data = $profiles[$username];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = ['status' => 'success'];

    if (isset($_POST['email'])) {
        $user_data['email'] = $_POST['email'];
        $response['email'] = $user_data['email'];
    }

    if (isset($_POST['username'])) {
        $new_username = $_POST['username'];
        if ($new_username !== $username) {
            $profiles[$new_username] = $user_data;
            unset($profiles[$username]);
            $_SESSION['username'] = $new_username;
            $username = $new_username;
        }
        $user_data['username'] = $new_username;
        $response['username'] = $new_username;
    }

    if (isset($_POST['password']) && $_POST['password'] != '') {
        $user_data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $response['password'] = '******';
    }

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_type = $_FILES['profile_picture']['type'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

        if (in_array($file_type, $allowed_types)) {
            $upload_dir = "uploads/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $file_name = uniqid() . '_' . basename($_FILES['profile_picture']['name']);
            $file_path = $upload_dir . $file_name;
            if (move_uploaded_file($file_tmp, $file_path)) {
                $user_data['profile_picture'] = $file_path;
                $response['profile_picture'] = $file_path;
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Fotoğraf yüklenemedi.';
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Geçersiz dosya türü.';
        }
    }

    $profiles[$username] = $user_data;

    if (file_put_contents($profileFile, json_encode($profiles, JSON_PRETTY_PRINT))) {
        echo json_encode(['status' => $response['status'], 'data' => $response]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Profil verileri kaydedilemedi.']);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Profil Sayfası</title>
    <link href="https://fonts.googleapis.com/css2?family=Rubik&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Rubik', sans-serif;
            background-color: #f7f7f7;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #4A148C;
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            margin: 0 20px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .navbar a:hover {
            color: #ff4081;
        }

        .profile-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 40px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .profile-container h1 {
            color: #4A148C;
            font-size: 36px;
            margin-bottom: 20px;
        }

        .profile-photo {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 6px solid #ff4081;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .profile-photo:hover {
            transform: scale(1.1);
        }

        .details p {
            font-size: 18px;
            color: #555;
            margin: 10px 0;
        }

        .details span {
            color: #4A148C;
            font-weight: 600;
        }

        .badges {
            margin-top: 30px;
        }

        .badge {
            display: inline-block;
            background-color: #ff4081;
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 14px;
            margin: 5px;
        }

        .update-profile input,
        .update-profile button {
            padding: 12px 18px;
            margin: 15px 0;
            width: 80%;
            max-width: 400px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .update-profile input:focus {
            border-color: #ff4081;
            outline: none;
        }

        .update-profile button {
            background-color: #4A148C;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .update-profile button:hover {
            background-color: #8E24AA;
        }

        .logout-button a {
            display: inline-block;
            background-color: #e53935;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .logout-button a:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="index.php">Ana Sayfa</a>
    <a href="profile.php">Profilim</a>
    <a href="logout.php">Çıkış Yap</a>
</div>

<div class="profile-container">
    <h1>Hoş Geldin, <span id="username"><?php echo htmlspecialchars($user_data['username']); ?></span>!</h1>

    <img src="<?php echo htmlspecialchars($user_data['profile_picture']); ?>" class="profile-photo" id="profile-picture">

    <div class="details">
        <p><span>Kullanıcı Adı:</span> <span id="username-display"><?php echo htmlspecialchars($user_data['username']); ?></span></p>
        <p><span>Email:</span> <span id="email"><?php echo htmlspecialchars($user_data['email']); ?></span></p>
        <p><span>Katılma Tarihi:</span> <?php echo htmlspecialchars($user_data['join_date']); ?></p>
    </div>

    <div class="badges">
        <h3>Rozetler</h3>
        <?php
        if (!empty($user_data['badges']) && is_array($user_data['badges'])) {
            foreach ($user_data['badges'] as $badge) {
                echo "<span class='badge'>" . htmlspecialchars($badge) . "</span>";
            }
        } else {
            echo "<span class='badge'>Rozet yok</span>";
        }
        ?>
    </div>

    <div class="update-profile">
        <form id="updateForm" method="POST" action="profile.php" enctype="multipart/form-data">
            <input type="text" name="username" placeholder="Yeni Kullanıcı Adı" value="<?php echo htmlspecialchars($user_data['username']); ?>">
            <input type="email" name="email" placeholder="Yeni Email" value="<?php echo htmlspecialchars($user_data['email']); ?>">
            <input type="password" name="password" placeholder="Yeni Şifre">
            <input type="file" name="profile_picture" accept="image/*">
            <button type="submit">Güncelle</button>
        </form>
    </div>

    <div class="logout-button">
        <a href="logout.php">Çıkış Yap</a>
    </div>
</div>

<script>
document.getElementById('updateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'profile.php', true);

    xhr.onload = function() {
        if (xhr.status == 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.status === 'success') {
                if (response.data.username)
                    document.getElementById('username-display').textContent = response.data.username;
                if (response.data.email)
                    document.getElementById('email').textContent = response.data.email;
                if (response.data.profile_picture)
                    document.getElementById('profile-picture').src = response.data.profile_picture;
                if (response.data.username)
                    document.getElementById('username').textContent = response.data.username;

                alert("Profil başarıyla güncellendi!");
            } else {
                alert("Hata oluştu: " + (response.message || 'Bilinmeyen bir hata'));
            }
        }
    };
    xhr.send(formData);
});
</script>

</body>
</html>
