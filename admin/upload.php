<?php
require '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Обработка исполнителя
        if (!empty($_POST['new_artist'])) {
            // Создание нового исполнителя
            $artistName = htmlspecialchars(trim($_POST['new_artist']));
            $avatarFile = $_FILES['artist_avatar'];
            
            // Валидация файла
            $allowedTypes = ['image/jpeg', 'image/png'];
            if (!in_array($avatarFile['type'], $allowedTypes)) {
                throw new Exception("Только JPG/PNG файлы разрешены");
            }

            $avatarPath = 'uploads/artists/' . uniqid() . '.' . pathinfo($avatarFile['name'], PATHINFO_EXTENSION);
            move_uploaded_file($avatarFile['tmp_name'], '../' . $avatarPath);
            
            $stmt = $pdo->prepare("INSERT INTO artists (name, avatar_path) VALUES (?, ?)");
            $stmt->execute([$artistName, $avatarPath]);
            $artistId = $pdo->lastInsertId();
        } else {
            // Использование существующего исполнителя
            $artistId = (int)$_POST['artist_id'];
            if ($artistId <= 0) throw new Exception("Выберите исполнителя");
        }

        // Загрузка альбома
        $albumTitle = htmlspecialchars(trim($_POST['album_title']));
        $coverFile = $_FILES['cover'];
        
        // Валидация обложки
        if ($coverFile['size'] > 5 * 1024 * 1024) {
            throw new Exception("Файл обложки слишком большой (макс. 5MB)");
        }

        $coverPath = 'uploads/covers/' . uniqid() . '.' . pathinfo($coverFile['name'], PATHINFO_EXTENSION);
        move_uploaded_file($coverFile['tmp_name'], '../' . $coverPath);

        // Сохранение альбома
        $stmt = $pdo->prepare("INSERT INTO albums (title, artist_id, cover_path) VALUES (?, ?, ?)");
        $stmt->execute([$albumTitle, $artistId, $coverPath]);
        $albumId = $pdo->lastInsertId();

        // Обработка треков
        foreach ($_FILES['tracks']['tmp_name'] as $key => $tmpName) {
            if (empty($tmpName)) continue;

            $trackTitle = htmlspecialchars(trim($_POST['track_names'][$key]));
            $audioFile = $_FILES['tracks'];
            
            // Валидация аудио
            $allowedAudio = ['audio/mpeg', 'audio/mp3'];
            if (!in_array($audioFile['type'][$key], $allowedAudio)) {
                throw new Exception("Только MP3 файлы разрешены");
            }

            $trackPath = 'uploads/music/' . uniqid() . '.mp3';
            move_uploaded_file($tmpName, '../' . $trackPath);

            $stmt = $pdo->prepare("INSERT INTO tracks (album_id, title, file_path) VALUES (?, ?, ?)");
            $stmt->execute([$albumId, $trackTitle, $trackPath]);
        }

        header("Location: ../index.php");
        exit;

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Загрузка контента | MusicSpot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="spotify-theme">
    <?php include '../includes/header.php'; ?>
    
    <div class="container py-5">
        <div class="upload-form bg-dark rounded p-4">
            <h2 class="text-white mb-4">Загрузка нового альбома</h2>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <!-- Секция исполнителя -->
                <div class="mb-4">
                    <label class="form-label text-white">Исполнитель</label>
                    <select name="artist_id" class="form-select bg-secondary text-white">
                        <option value="">Выберите существующего</option>
                        <?php
                        $stmt = $pdo->query("SELECT id, name FROM artists ORDER BY name ASC");
                        while ($row = $stmt->fetch()):
                        ?>
                            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                    <div class="text-center my-3 text-white-50">ИЛИ</div>
                    <input type="text" name="new_artist" class="form-control mb-2" 
                           placeholder="Новый исполнитель">
                    <input type="file" name="artist_avatar" class="form-control" 
                           accept="image/*">
                </div>

                <!-- Основные данные альбома -->
                <div class="mb-4">
                    <label class="form-label text-white">Название альбома</label>
                    <input type="text" name="album_title" class="form-control" required>
                </div>

                <div class="mb-4">
                    <label class="form-label text-white">Обложка альбома</label>
                    <input type="file" name="cover" class="form-control" accept="image/*" required>
                </div>

                <!-- Секция треков -->
                <div class="mb-4">
                    <label class="form-label text-white">Треки</label>
                    <div id="tracks-container">
                        <div class="track-field mb-3">
                            <input type="text" name="track_names[]" 
                                   class="form-control mb-2" 
                                   placeholder="Название трека" 
                                   required>
                            <input type="file" name="tracks[]" 
                                   class="form-control" 
                                   accept="audio/mpeg" 
                                   required>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-light" onclick="addTrackField()">
                        + Добавить трек
                    </button>
                </div>

                <button type="submit" class="btn btn-success w-100">Загрузить</button>
            </form>
        </div>
    </div>

    <script>
        function addTrackField() {
            const container = document.getElementById('tracks-container');
            const div = document.createElement('div');
            div.className = 'track-field mb-3';
            div.innerHTML = `
                <input type="text" name="track_names[]" 
                       class="form-control mb-2" 
                       placeholder="Название трека" 
                       required>
                <input type="file" name="tracks[]" 
                       class="form-control" 
                       accept="audio/mpeg" 
                       required>
            `;
            container.appendChild(div);
        }
    </script>
</body>
</html>