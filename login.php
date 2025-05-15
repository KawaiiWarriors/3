<?php
require_once 'includes/header.php';
require_once 'includes/auth.php';

$pageTitle = "Вход для администратора";

// Если пользователь уже авторизован - перенаправляем в админку
if (isAdminLoggedIn()) {
    header('Location: admin/');
    exit();
}

echo $password;
// Обработка формы входа
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (authenticateAdmin($username, $password)) {
        header('Location: admin/');
        exit();
    } else {
        $error = 'Неверное имя пользователя или пароль';
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Вход для администратора</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Логин</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Войти</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>