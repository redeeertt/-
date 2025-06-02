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