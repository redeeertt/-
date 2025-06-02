<?php 
include 'includes/config.php';
$title = "Главная | ГОФ";
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="spotify-theme">
    <?php include 'includes/header.php'; ?>
    
    <main class="container-fluid px-4 py-5">
        <h1 class="text-white mb-4">Все исполнители</h1>
        
        <div class="row g-4">
            <?php
            $stmt = $pdo->query("SELECT * FROM artists ORDER BY name ASC");
            while ($artist = $stmt->fetch()):
            ?>
            <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                <a href="artist.php?id=<?= $artist['id'] ?>" class="text-decoration-none">
                    <div class="card artist-card bg-dark hover-effect">
                        <img src="<?= $artist['avatar_path'] ?>" 
                             class="card-img-top artist-avatar" 
                             alt="<?= htmlspecialchars($artist['name']) ?>">
                        <div class="card-body text-center">
                            <h5 class="card-title text-white mb-0">
                                <?= htmlspecialchars($artist['name']) ?>
                            </h5>
                        </div>
                    </div>
                </a>
            </div>
            <?php endwhile; ?>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/player_ui.php'; ?>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/player.js"></script>
</body>
</html>