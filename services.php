<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

$pageTitle = "Услуги";
?>

<h2 class="mb-4">Наши услуги</h2>

<div class="row">
    <?php
    // Получаем услуги из базы данных
    $stmt = $pdo->query("SELECT * FROM services ORDER BY title");
    while ($service = $stmt->fetch()):
    ?>
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h4 class="card-title"><?php echo htmlspecialchars($service['title']); ?></h4>
                <p class="card-text"><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="badge bg-primary rounded-pill"><?php echo $service['duration_minutes']; ?> мин.</span>
                    <span class="h5 mb-0"><?php echo number_format($service['price'], 2, '.', ' '); ?> ₽</span>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <a href="appointment.php?service=<?php echo $service['id']; ?>" class="btn btn-primary w-100">Записаться</a>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<?php require_once 'includes/footer.php'; ?>