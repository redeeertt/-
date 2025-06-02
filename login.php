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

$title = "  | ГОФ";
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Неверные имя пользователя или пароль!';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Вход</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
    <div class="container mt-5" style="max-width: 400px;">
        <h2 class="mb-4">Вход</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Имя пользователя" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Пароль" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Войти</button>
        </form>
        <div class="mt-3">
            Нет аккаунта? <a href="register.php" class="text-white">Зарегистрироваться</a>
        </div>
    </div>
</body>
</html>