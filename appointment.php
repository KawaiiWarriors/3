<?php
require_once 'includes/header.php';

$pageTitle = "Запись на консультацию";

// Получаем список услуг для выпадающего списка
$services = $pdo->query("SELECT * FROM services ORDER BY title")->fetchAll();

// Обработка формы записи
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    
    // Валидация данных
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $serviceId = $_POST['service'] ?? 0;
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    
    if (empty($name)) $errors[] = 'Укажите ваше имя';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Укажите корректный email';
    if (empty($phone)) $errors[] = 'Укажите ваш телефон';
    if (empty($serviceId)) $errors[] = 'Выберите услугу';
    if (empty($date) || empty($time)) $errors[] = 'Выберите дату и время';
    
    // Проверка доступности времени
    if (empty($errors)) {
        $datetime = $date . ' ' . $time;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE appointment_date = ? AND appointment_time = ? AND status != 'canceled'");
        $stmt->execute([$date, $time]);
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            $errors[] = 'Выбранное время уже занято. Пожалуйста, выберите другое время.';
        }
    }
    
    // Если ошибок нет - сохраняем запись
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO appointments (client_name, client_email, client_phone, service_id, appointment_date, appointment_time) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $serviceId, $date, $time]);
        
        $success = true;
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <h2 class="mb-4">Запись на консультацию</h2>
        
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <?php if (isset($success) && $success): ?>
        <div class="alert alert-success">
            <h4 class="alert-heading">Запись успешно создана!</h4>
            <p>Мы свяжемся с вами для подтверждения записи. Если у вас есть вопросы, вы можете обратиться по телефону: +7 (XXX) XXX-XX-XX</p>
            <hr>
            <p class="mb-0">Детали записи:</p>
            <ul>
                <li>Имя: <?php echo htmlspecialchars($name); ?></li>
                <li>Услуга: <?php echo htmlspecialchars($services[array_search($serviceId, array_column($services, 'id'))]['title']); ?></li>
                <li>Дата: <?php echo date('d.m.Y', strtotime($date)); ?></li>
                <li>Время: <?php echo $time; ?></li>
            </ul>
        </div>
        <?php else: ?>
        
        <form method="post" id="appointmentForm">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Ваше имя *</label>
                    <input type="text" class="form-control" id="name" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="mb-3">
                <label for="phone" class="form-label">Телефон *</label>
                <input type="tel" class="form-control" id="phone" name="phone" required value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
            </div>
            
            <div class="mb-3">
                <label for="service" class="form-label">Услуга *</label>
                <select class="form-select" id="service" name="service" required>
                    <option value="">-- Выберите услугу --</option>
                    <?php foreach ($services as $service): ?>
                    <option value="<?php echo $service['id']; ?>" <?php if (($_POST['service'] ?? '') == $service['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($service['title']); ?> (<?php echo number_format($service['price'], 2, '.', ' '); ?> ₽)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="date" class="form-label">Дата *</label>
                    <input type="date" class="form-control" id="date" name="date" required min="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars($_POST['date'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="time" class="form-label">Время *</label>
                    <select class="form-select" id="time" name="time" required>
                        <option value="">-- Выберите время --</option>
                        <?php
                        // Генерация временных слотов (каждые 30 минут с 9:00 до 18:00)
                        for ($hour = 9; $hour < 18; $hour++) {
                            for ($minute = 0; $minute < 60; $minute += 30) {
                                $timeValue = sprintf("%02d:%02d", $hour, $minute);
                                echo '<option value="' . $timeValue . '"';
                                if (($_POST['time'] ?? '') == $timeValue) echo ' selected';
                                echo '>' . $timeValue . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            
            <div class="mb-3">
                <button type="submit" class="btn btn-primary btn-lg w-100">Записаться</button>
            </div>
            
            <div class="text-muted">
                <small>* Поля, обязательные для заполнения</small>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>

<script>
// Проверка доступности времени при изменении даты
document.getElementById('date').addEventListener('change', function() {
    const date = this.value;
    const timeSelect = document.getElementById('time');
    
    if (!date) return;
    
    // Отправляем запрос на сервер для проверки занятых времен
    fetch('check_time.php?date=' + date)
        .then(response => response.json())
        .then(data => {
            // Помечаем занятые времена как disabled
            Array.from(timeSelect.options).forEach(option => {
                if (option.value && data.includes(option.value)) {
                    option.disabled = true;
                    if (option.selected) option.selected = false;
                } else {
                    option.disabled = false;
                }
            });
        });
});
</script>

<?php require_once 'includes/footer.php'; ?>