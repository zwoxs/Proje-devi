<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feedback = trim($_POST['feedback'] ?? '');

    if ($feedback !== '') {
        $file = 'back.json';

        // Eski verileri oku (varsa)
        $existing = [];
        if (file_exists($file)) {
            $json = file_get_contents($file);
            $existing = json_decode($json, true) ?? [];
        }

        // Yeni geri bildirimi ekle
        $existing[] = [
            'feedback' => $feedback,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        // JSON olarak geri yaz
        file_put_contents($file, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}

header('Location: wiki.php'); // formdan sonra tekrar wiki'ye yönlendir
exit;
