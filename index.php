<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$pageTitle = "Главная";
?>

<div class="row">
    <div class="col-md-8">
        <h2 class="mb-4">Добро пожаловать в психологическую службу Колледжа АлтГУ</h2>
        <p>Наша служба предоставляет профессиональную психологическую помощь студентам колледжа. Мы помогаем справляться с учебными нагрузками, межличностными отношениями, стрессом и другими психологическими трудностями.</p>
        <p>Наши психологи - квалифицированные специалисты с большим опытом работы в образовательной сфере. Мы гарантируем конфиденциальность и индивидуальный подход к каждому клиенту.</p>
        
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Как записаться на консультацию?</h5>
                <ol>
                    <li>Выберите услугу на странице "Услуги"</li>
                    <li>Перейдите в раздел "Запись на консультацию"</li>
                    <li>Заполните форму и выберите удобное время</li>
                    <li>Дождитесь подтверждения записи</li>
                </ol>
                <a href="appointment.php" class="btn btn-primary">Записаться онлайн</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Экстренная помощь</h5>
            </div>
            <div class="card-body">
                <p>Если вам срочно нужна психологическая помощь:</p>
                <ul>
                    <li>Телефон доверия: 8-800-XXX-XX-XX</li>
                    <li>Электронная почта: emergency@college.altgu.ru</li>
                </ul>
                <p>Мы доступны 24/7 для экстренных случаев.</p>
            </div>
        </div>
    </div>
</div>

<h3 class="mt-5 mb-4">Новости и события</h3>

<div class="row">
    <?php
    // Получаем новости из базы данных
    $stmt = $pdo->query("SELECT * FROM news ORDER BY published_at DESC LIMIT 3");
    while ($news = $stmt->fetch()):
        // Получаем изображения для новости
        $imagesStmt = $pdo->prepare("SELECT * FROM news_images WHERE news_id = ? LIMIT 1");
        $imagesStmt->execute([$news['id']]);
        $image = $imagesStmt->fetch();
    ?>
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <?php if ($image): ?>
            <img src="<?php echo "assets/images/uploads/" . $image['image_path']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($news['title']); ?>">
            <?php endif; ?>
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($news['title']); ?></h5>
                <p class="card-text"><?php echo mb_substr(htmlspecialchars($news['description']), 0, 150) . ''; ?></p>
            </div>
            <div class="card-footer text-muted">
                Опубликовано: <?php echo date('d.m.Y', strtotime($news['published_at'])); ?>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<?php require_once 'includes/footer.php'; ?>