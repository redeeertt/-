<?php
include 'includes/config.php';

$albumId = (int)($_GET['id'] ?? 0);

try {
    // Получаем данные альбома
    $stmt = $pdo->prepare("SELECT albums.*, artists.name AS artist_name 
                          FROM albums 
                          JOIN artists ON albums.artist_id = artists.id 
                          WHERE albums.id = ?");
    $stmt->execute([$albumId]);
    $album = $stmt->fetch();
    
    if(!$album) {
        header("HTTP/1.0 404 Not Found");
        die("Альбом не найден");
    }

    // Получаем треки
    $stmt = $pdo->prepare("SELECT * FROM tracks WHERE album_id = ? ORDER BY id ASC");
    $stmt->execute([$albumId]);
    $tracks = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Ошибка базы данных: " . $e->getMessage());
}

$title = htmlspecialchars($album['title']) . " | MusicSpot";
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .album-header {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7));
            padding: 1rem 0;
        }
        .album-cover {
            max-width: 200px;
            border-radius: 10px;
        }
        .track-item {
            padding: 10px;
            border-bottom: 1px solid #333;
            cursor: pointer;
            transition: background 0.3s;
        }
        .track-item:hover {
            background: rgba(255,255,255,0.1);
        }
        .track-number {
            width: 30px;
            text-align: center;
            margin-right: 10px;
            color: #aaa;
        }
        .track-info {
            flex-grow: 1;
        }
        .track-title {
            font-weight: 500;
            margin-bottom: 3px;
        }
        .track-artist {
            font-size: 0.9rem;
            color: #aaa;
        }
        .track-duration {
            color: #aaa;
            margin-left: 10px;
        }
        @media (max-width: 768px) {
            .album-header {
                text-align: center;
            }
            .album-cover {
                margin: 0 auto 15px;
                max-width: 150px;
            }
            .track-item {
                padding: 8px 5px;
            }
            .track-title {
                font-size: 0.95rem;
            }
            .track-artist {
                font-size: 0.8rem;
            }
            .track-duration {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body class="bg-dark text-white">
    <?php include 'includes/header.php'; ?>
    
    <div class="album-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3 text-center">
                    <img src="<?= $album['cover_path'] ?>" 
                         class="album-cover img-fluid"
                         alt="<?= htmlspecialchars($album['title']) ?>">
                </div>
                <div class="col-md-9 mt-3 mt-md-0">
                    <h1 class="display-5 fw-bold"><?= htmlspecialchars($album['title']) ?></h1>
                    <p class="h5 text-muted"><?= htmlspecialchars($album['artist_name']) ?></p>
                    <div class="mt-2">
                        <span class="badge bg-primary me-2">
                            <?= date('Y', strtotime($album['release_date'])) ?>
                        </span>
                        <span class="badge bg-secondary">
                            <?= count($tracks) ?> треков
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <main class="container mt-4">
        <div class="track-list">
            <?php foreach($tracks as $index => $track): ?>
            <div class="track-item d-flex align-items-center"
                 onclick="playTrack('<?= $track['file_path'] ?>', '<?= addslashes($track['title']) ?>')">
                <div class="track-number"><?= $index + 1 ?></div>
                <div class="track-info">
                    <div class="track-title"><?= htmlspecialchars($track['title']) ?></div>
                    <div class="track-artist"><?= htmlspecialchars($album['artist_name']) ?></div>
                </div>
                <div class="track-duration"><?= $track['duration'] ?? '0:00' ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/player_ui.php'; ?>

    <script>
        // Упрощенный плеер
        class SimplePlayer {
            constructor() {
                this.audio = new Audio();
                this.currentTrack = null;
                
                // Инициализация элементов управления
                this.playBtn = document.getElementById('play-btn');
                this.pauseBtn = document.getElementById('pause-btn');
                this.progressBar = document.querySelector('.progress-bar');
                this.nowPlaying = document.getElementById('now-playing');
                
                if (this.playBtn && this.pauseBtn) {
                    this.playBtn.addEventListener('click', () => this.play());
                    this.pauseBtn.addEventListener('click', () => this.pause());
                }
                
                this.audio.addEventListener('timeupdate', () => this.updateProgress());
            }

            playTrack(filePath, trackName) {
                try {
                    // Проверяем доступность файла
                    fetch(filePath)
                        .then(response => {
                            if (!response.ok) throw new Error('Файл не найден');
                            return response.blob();
                        })
                        .then(blob => {
                            this.audio.src = URL.createObjectURL(blob);
                            this.nowPlaying.textContent = trackName;
                            return this.audio.play();
                        })
                        .catch(error => {
                            console.error('Ошибка загрузки трека:', error);
                            alert('Не удалось загрузить трек: ' + error.message);
                        });
                } catch (error) {
                    console.error('Ошибка воспроизведения:', error);
                    alert('Ошибка: ' + error.message);
                }
            }

            play() {
                if (this.audio.src) {
                    this.audio.play();
                    this.updateControls();
                }
            }

            pause() {
                this.audio.pause();
                this.updateControls();
            }

            updateControls() {
                if (this.playBtn && this.pauseBtn) {
                    this.playBtn.style.display = this.audio.paused ? 'inline-block' : 'none';
                    this.pauseBtn.style.display = !this.audio.paused ? 'inline-block' : 'none';
                }
            }

            updateProgress() {
                if (this.progressBar) {
                    const progress = (this.audio.currentTime / this.audio.duration) * 100 || 0;
                    this.progressBar.style.width = `${progress}%`;
                }
            }
        }

        // Инициализация плеера
        document.addEventListener('DOMContentLoaded', () => {
            window.player = new SimplePlayer();
        });
        
        // Функция для воспроизведения трека
        function playTrack(filePath, trackName) {
            if (window.player) {
                window.player.playTrack(filePath, trackName);
            } else {
                console.error('Плеер не инициализирован');
            }
        }
    </script>
</body>
</html>