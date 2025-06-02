<?php
include 'includes/config.php';

$artistId = (int)($_GET['id'] ?? 0);

try {
    // Получаем данные исполнителя
    $stmt = $pdo->prepare("SELECT * FROM artists WHERE id = ?");
    $stmt->execute([$artistId]);
    $artist = $stmt->fetch();
    
    if(!$artist) {
        header("HTTP/1.0 404 Not Found");
        die("Исполнитель не найден");
    }

    // Получаем альбомы исполнителя
    $stmt = $pdo->prepare("SELECT * FROM albums WHERE artist_id = ? ORDER BY release_date DESC");
    $stmt->execute([$artistId]);
    $albums = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Ошибка базы данных: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($artist['name']) ?> | MusicSpot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .artist-avatar {
            width: 250px;
            height: 250px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #333;
        }
    </style>
</head>
<body class="spotify-theme">
    <?php include 'includes/header.php'; ?>
    
    <div class="artist-header bg-dark py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4 text-center">
                    <img src="<?= $artist['avatar_path'] ?>" 
                         class="artist-avatar shadow-lg" 
                         alt="<?= htmlspecialchars($artist['name']) ?>">
                </div>
                <div class="col-md-8 text-white mt-4 mt-md-0">
                    <h1 class="display-4 fw-bold"><?= htmlspecialchars($artist['name']) ?></h1>
                    <?php if(!empty($artist['bio'])): ?>
                        <p class="lead mt-3"><?= nl2br(htmlspecialchars($artist['bio'])) ?></p>
                    <?php endif; ?>
                    
                    <!-- Кнопка удаления (только для админа) -->
                    <?php if(isset($_SESSION['is_admin'])): ?>
                    <div class="mt-4">
                        <button class="btn btn-danger" 
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteModal"
                                data-type="artist"
                                data-id="<?= $artist['id'] ?>">
                            Удалить исполнителя
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <main class="container mt-5">
        <h3 class="text-white mb-4">Альбомы</h3>
        <div class="row g-4">
            <?php if(count($albums) > 0): ?>
                <?php foreach($albums as $album): ?>
                <div class="col-md-3">
                    <a href="album.php?id=<?= $album['id'] ?>" class="text-decoration-none">
                        <div class="card bg-dark hover-effect">
                            <img src="<?= $album['cover_path'] ?>" 
                                 class="card-img-top" 
                                 alt="<?= htmlspecialchars($album['title']) ?>">
                            <div class="card-body">
                                <h6 class="card-title text-white text-truncate">
                                    <?= htmlspecialchars($album['title']) ?>
                                </h6>
                                <small class="text-muted">
                                    <?= date('Y', strtotime($album['release_date'])) ?>
                                </small>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">Нет доступных альбомов</div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
   
</body>
</html>