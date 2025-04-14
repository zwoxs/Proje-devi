<?php
session_start();
$user_logged_in = isset($_SESSION['username']);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Box PVP - Wiki</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background-color: #181818;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #000;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar .logo {
            font-size: 26px;
            color: #ff9900;
            font-weight: bold;
        }

        .navbar .nav-links a {
            color: #fff;
            text-decoration: none;
            margin-left: 20px;
            padding: 10px 15px;
            background-color: #333;
            border-radius: 6px;
            transition: background-color 0.3s;
        }

        .navbar .nav-links a:hover {
            background-color: #555;
        }

        .wiki-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
        }

        .wiki-controls {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 10px;
        }

        .wiki-controls input,
        .wiki-controls select {
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            border: none;
            outline: none;
            background-color: #2e2e2e;
            color: #fff;
            flex: 1 1 48%;
        }

        .wiki-section {
            background-color: #2a2a2a;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
            transition: all 0.3s ease;
        }

        .wiki-section h2 {
            color: #ff9900;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .wiki-section p {
            color: #ccc;
            line-height: 1.6;
            margin-top: 10px;
            display: none;
        }

        .wiki-section.active p {
            display: block;
        }

        footer {
            text-align: center;
            padding: 30px;
            background-color: #111;
            color: #aaa;
            margin-top: 50px;
        }

        .feedback {
            margin-top: 50px;
            background-color: #222;
            padding: 20px;
            border-radius: 10px;
        }

        .feedback textarea {
            width: 100%;
            height: 100px;
            border-radius: 8px;
            border: none;
            padding: 10px;
            resize: none;
            background-color: #2a2a2a;
            color: #fff;
            font-size: 14px;
        }

        .feedback button {
            margin-top: 10px;
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
        }

        .feedback button:hover {
            background-color: #218838;
        }

        .no-result {
            text-align: center;
            color: #bbb;
            margin-top: 20px;
            display: none;
        }

        .theme-toggle {
            cursor: pointer;
            background-color: #444;
            padding: 5px 15px;
            border-radius: 6px;
            color: #fff;
            font-size: 16px;
        }

        @media (max-width: 600px) {
            .wiki-controls input,
            .wiki-controls select {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">Box PVP</div>
    <div class="nav-links">
        <a href="index.php">Ana Sayfa</a>
        <a href="sepet.php">Sepet</a>
        <?php if ($user_logged_in): ?>
            <a href="profile.php">Profil</a>
            <a href="logout.php">Çıkış</a>
        <?php else: ?>
            <a href="login.php">Giriş</a>
        <?php endif; ?>
        <a href="wiki.php" style="background-color:#28a745;">Wiki</a>
    </div>
    <div class="theme-toggle" onclick="toggleTheme()">Tema Değiştir</div>
</div>

<div class="wiki-container">
    <div class="wiki-controls">
        <input type="text" id="searchBox" placeholder="Başlık veya içerik ara...">
        <select id="categoryFilter">
            <option value="all">Tüm Kategoriler</option>
            <option value="pvp">PvP</option>
            <option value="kredi">Kredi</option>
            <option value="modlar">Modlar</option>
            <option value="kurallar">Kurallar</option>
        </select>
    </div>

    <div id="wiki-sections">
        <div class="wiki-section" data-category="pvp">
            <h2>🎮 PvP Nedir?<span>▼</span></h2>
            <p>PvP (Player vs Player), oyuncuların birbirleriyle savaşmasına dayalı bir oyun modudur. Box PVP sunucumuzda hızlı refleksler ve doğru ekipman ile rakiplerini alt etmen gerekiyor.</p>
        </div>

        <div class="wiki-section" data-category="kredi">
            <h2>💳 Kredi Sistemi<span>▼</span></h2>
            <p>Kredi, mağazamızda ürün satın almak için kullanılır. Kredileri Papara ile satın alabilir, bu kredileri kılıç, zırh veya kozmetik ürünlerde harcayabilirsin.</p>
        </div>

        <div class="wiki-section" data-category="modlar">
            <h2>🧱 Oyun Modları<span>▼</span></h2>
            <p>Sunucumuzda klasik Box PvP'nin yanı sıra, 1v1 ve Redro gibi modlar da bulunur. Her modun kendine özel arenaları ve kuralları vardır.</p>
        </div>

        <div class="wiki-section" data-category="kurallar">
            <h2>🔒 Güvenlik & Kurallar<span>▼</span></h2>
            <p>Hile kesinlikle yasaktır. Adil oyun ortamı sağlamak için tüm oyuncuların kurallara uyması zorunludur. Hile tespiti durumunda kalıcı ban uygulanır.</p>
        </div>
        
        <!-- Sıkça Sorulan Sorular -->
        <div class="wiki-section" data-category="sss">
            <h2>❓ Sıkça Sorulan Sorular<span>▼</span></h2>
            <p><strong>S: Kredi nasıl kazanırım?</strong><br>A: Krediyi mağazadan Papara ile alabilir ya da etkinliklerle kazanabilirsin.</p>
        </div>
    </div>

    <div class="no-result" id="noResult">Hiçbir sonuç bulunamadı.</div>

    <!-- Popüler Konular -->
    <div style="margin-top: 30px;">
        <h3 style="color:#ff9900;">🔥 Popüler Wiki Konuları</h3>
        <ul style="padding-left: 20px; color: #ddd;">
            <li>PvP Nedir?</li>
            <li>Kredi Nasıl Kullanılır?</li>
            <li>Redro Modu Hakkında</li>
        </ul>
    </div>

    <!-- Geri Bildirim -->
    <div class="feedback">
        <h3>💬 Geri Bildirim</h3>
        <form method="POST" action="feedback-handler.php">
            <input type="hidden" name="username" value="<?php echo $user_logged_in ? $_SESSION['username'] : 'Anonim'; ?>">
            <textarea name="feedback" placeholder="Görüşlerinizi bizimle paylaşın..."></textarea>
            <select name="rating">
                <option value="5">⭐⭐⭐⭐⭐</option>
                <option value="4">⭐⭐⭐⭐</option>
                <option value="3">⭐⭐⭐</option>
                <option value="2">⭐⭐</option>
                <option value="1">⭐</option>
            </select>
            <button type="submit">Gönder</button>
        </form>
    </div>
</div>

<footer>
    © 2025 Box PVP - Wiki Sayfası | Tüm hakları saklıdır.
</footer>

<script>
    const sections = document.querySelectorAll('.wiki-section');
    const searchBox = document.getElementById('searchBox');
    const categoryFilter = document.getElementById('categoryFilter');
    const noResultMessage = document.getElementById('noResult');

    sections.forEach(section => {
        section.querySelector('h2').addEventListener('click', () => {
            section.classList.toggle('active');
        });
    });

    searchBox.addEventListener('input', filterWiki);
    categoryFilter.addEventListener('change', filterWiki);

    function filterWiki() {
        const searchTerm = searchBox.value.toLowerCase();
        const selectedCategory = categoryFilter.value;
        let results = 0;

        sections.forEach(section => {
            const title = section.querySelector('h2').textContent.toLowerCase();
            const content = section.querySelector('p').textContent.toLowerCase();
            const category = section.getAttribute('data-category');

            if ((title.includes(searchTerm) || content.includes(searchTerm)) && (selectedCategory === 'all' || category === selectedCategory)) {
                section.style.display = 'block';
                results++;
            } else {
                section.style.display = 'none';
            }
        });

        noResultMessage.style.display = results === 0 ? 'block' : 'none';
    }

    function toggleTheme() {
        const currentTheme = document.body.style.backgroundColor;
        if (currentTheme === 'rgb(24, 24, 24)') {
            document.body.style.backgroundColor = '#fff';
            document.body.style.color = '#000';
        } else {
            document.body.style.backgroundColor = '#181818';
            document.body.style.color = '#fff';
        }
    }
</script>

</body>
</html>
