<?php
include 'includes/config.php';

// Получаем поисковый запрос
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

// Если запрос пустой - перенаправляем на главную
if (empty($query)) {
    header('Location: index.php');
    exit;
}

// Инициализируем результаты
$artists = [];
$albums = [];
$tracks = [];

try {
    // Поиск исполнителей
    $stmt = $pdo->prepare("SELECT * FROM artists WHERE name LIKE :query ORDER BY name ASC");
    $stmt->execute([':query' => "%$query%"]);
    $artists = $stmt->fetchAll();

    // Поиск альбомов
    $stmt = $pdo->prepare("SELECT albums.*, artists.name AS artist_name 
                          FROM albums 
                          JOIN artists ON albums.artist_id = artists.id 
                          WHERE albums.title LIKE :query 
                          ORDER BY albums.release_date DESC");
    $stmt->execute([':query' => "%$query%"]);
    $albums = $stmt->fetchAll();

    // Поиск треков
    $stmt = $pdo->prepare("SELECT tracks.*, albums.title AS album_title, artists.name AS artist_name 
                          FROM tracks 
                          JOIN albums ON tracks.album_id = albums.id 
                          JOIN artists ON albums.artist_id = artists.id 
                          WHERE tracks.title LIKE :query 
                          ORDER BY tracks.title ASC");
    $stmt->execute([':query' => "%$query%"]);
    $tracks = $stmt->fetchAll();

} catch(PDOException $e) {
    die("Ошибка поиска: " . $e->getMessage());
}

$title = "Результаты поиска: " . htmlspecialchars($query) . " | MusicSpot";
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .search-section {
            margin-bottom: 2rem;
        }
        .search-category {
            margin-top: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #444;
        }
    </style>
</head>
<body class="spotify-theme">
    <?php include 'includes/header.php'; ?>
    
    <main class="container mt-4">
        <h2 class="mb-4">Результаты поиска: "<?= htmlspecialchars($query) ?>"</h2>
        
        <!-- Исполнители -->
        <?php if (!empty($artists)): ?>
            <div class="search-section">
                <h4 class="search-category">Исполнители</h4>
                <div class="row g-3">
                    <?php foreach($artists as $artist): ?>
                    <div class="col-md-3 col-sm-6">
                        <a href="artist.php?id=<?= $artist['id'] ?>" class="text-decoration-none">
                            <div class="card bg-dark hover-effect">
                                <img src="<?= $artist['avatar_path'] ?>" 
                                    class="card-img-top artist-avatar"
                                    alt="<?= htmlspecialchars($artist['name']) ?>">
                                <div class="card-body">
                                    <h6 class="card-title text-truncate"><?= htmlspecialchars($artist['name']) ?></h6>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Альбомы -->
        <?php if (!empty($albums)): ?>
            <div class="search-section">
                <h4 class="search-category">Альбомы</h4>
                <div class="row g-3">
                    <?php foreach($albums as $album): ?>
                    <div class="col-md-3 col-sm-6">
                        <a href="album.php?id=<?= $album['id'] ?>" class="text-decoration-none">
                            <div class="card bg-dark hover-effect">
                                <img src="<?= $album['cover_path'] ?>" 
                                    class="card-img-top"
                                    alt="<?= htmlspecialchars($album['title']) ?>">
                                <div class="card-body">
                                    <h6 class="card-title text-truncate"><?= htmlspecialchars($album['title']) ?></h6>
                                    <small class="text-muted"><?= htmlspecialchars($album['artist_name']) ?></small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Треки -->
        <?php if (!empty($tracks)): ?>
            <div class="search-section">
                <h4 class="search-category">Треки</h4>
                <div class="list-group">
                    <?php foreach($tracks as $track): ?>
                    <div class="list-group-item bg-transparent text-white border-secondary d-flex justify-content-between align-items-center"
                         onclick="player.playTrack('<?= $track['file_path'] ?>', '<?= addslashes($track['title']) ?>')">
                        <div>
                            <h6 class="mb-1"><?= htmlspecialchars($track['title']) ?></h6>
                            <small class="text-muted">
                                <?= htmlspecialchars($track['artist_name']) ?> • <?= htmlspecialchars($track['album_title']) ?>
                            </small>
                        </div>
                        <span class="badge bg-secondary"><?= $track['duration'] ?? '0:00' ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Если результатов нет -->
        <?php if (empty($artists) && empty($albums) && empty($tracks)): ?>
            <div class="alert alert-info">
                По вашему запросу ничего не найдено. Попробуйте изменить запрос.
            </div>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/player_ui.php'; ?>
    
    <script src="assets/js/player.js"></script>
</body>
</html>