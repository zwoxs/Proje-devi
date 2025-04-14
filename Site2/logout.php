<?php
session_start();
session_unset();  // Tüm session verilerini sil
session_destroy(); // Oturumu sonlandır
header("Location: login.php");  // Login sayfasına yönlendir
exit;
?>
