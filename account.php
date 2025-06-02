<?php
session_start();
require 'includes/config.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Получаем данные пользователя
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Обработка формы обновления
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bio = trim($_POST['bio']);
    $avatar = $user['avatar'];

    // Загрузка аватарки
    if (!empty($_FILES['avatar']['tmp_name'])) {
        $uploadDir = 'uploads/avatars/';
        $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadDir . $filename)) {
            $avatar = $uploadDir . $filename;
        }
    }

    // Обновляем данные
    $stmt = $pdo->prepare("UPDATE users SET bio = ?, avatar = ? WHERE id = ?");
    $stmt->execute([$bio, $avatar, $_SESSION['user_id']]);
    header("Location: account.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личный кабинет | MusicSpot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-avatar {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #333;
        }
        .profile-card {
            background: #2d2d2d;
            border-radius: 15px;
            padding: 20px;
        }
    </style>
</head>
<body class="spotify-theme">
    <?php include 'includes/header.php'; ?>

    <main class="container my-5">
        <div class="row">
            <div class="col-md-4">
                <div class="profile-card text-center">
                    <img src="<?= $user['avatar'] ?>" class="profile-avatar mb-3" alt="Аватар">
                    <h3><?= htmlspecialchars($user['username']) ?></h3>
                    <p class="text-muted">Зарегистрирован: <?= date('d.m.Y', strtotime($user['registration_date'])) ?></p>
                </div>
            </div>

            <div class="col-md-8">
                <div class="profile-card">
                    <h4>Настройки профиля</h4>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Биография</label>
                            <textarea name="bio" class="form-control bg-dark text-white" rows="3"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Аватар</label>
                            <input type="file" name="avatar" class="form-control bg-dark text-white">
                        </div>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </form>

                    <hr class="my-4 bg-secondary">

                  
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>