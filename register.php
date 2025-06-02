<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">MusicSpot</a>
        
        <!-- Кнопка бургер-меню -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>
          <!-- Меню пользователя -->
        <?php if (isset($_SESSION['user_id'])): ?>
    <div class="btn-group">
        <a href="account.php" class="btn btn-outline-light me-2">Профиль</a>
        <a href="admin/upload.php" class="btn btn-outline-light me-2">Загрузить</a>
             <a href="index.php" class="btn btn-outline-light me-2">Главная</a>
        <a href="logout.php" class="btn btn-outline-danger">Выйти</a>
    </div>
<?php else: ?>
    <div class="btn-group">
        <a href="login.php" class="btn btn-outline-light me-2">Войти</a>
        <a href="index.php" class="btn btn-outline-light me-2">Главная</a>
        <a href="register.php" class="btn btn-primary">Регистрация</a>
    </div>
<?php endif; ?>

        <div class="collapse navbar-collapse" id="navbarContent">
            <!-- Поиск -->
            <form class="d-flex mt-2 mt-lg-0 mx-lg-3 flex-grow-1" action="search.php" method="GET">
                <input type="search" name="q" class="form-control bg-secondary text-white" 
                       placeholder="Поиск...">
            </form>
            
          
        </div>
    </div>
</nav>
<?php
session_start();
include 'includes/config.php';

$title = "Регистрация | ГОФ";

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);

    // Проверка заполнения полей
    if (empty($username) || empty($password) || empty($email)) {
        $error = 'Все поля обязательны для заполнения!';
    } else {
        // Проверка существования пользователя
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->rowCount() > 0) {
            $error = 'Пользователь с таким именем или email уже существует!';
        } else {
            // Регистрация
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hashed_password, $email]);
            
            $_SESSION['user_id'] = $pdo->lastInsertId();
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Регистрация</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
    <div class="container mt-5" style="max-width: 400px;">
        <h2 class="mb-4">Регистрация</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Имя пользователя" required>
            </div>
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Пароль" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Зарегистрироваться</button>
        </form>
        <div class="mt-3">
            Уже есть аккаунт? <a href="login.php" class="text-white">Войти</a>
        </div>
    </div>
</body>
</html>