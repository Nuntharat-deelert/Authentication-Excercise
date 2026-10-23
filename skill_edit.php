<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'auth.php';

// ตรวจสอบว่ามี user_id ใน Session หรือยัง ถ้าไม่มีให้ Redirect ไปหน้า login.php
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/db.php';
require 'crud_bootstrap.php';

$error = '';
$id = intval($_GET['id'] ?? 0);
$pid = profile_id($conn);

// ดึงข้อมูล Skill เดิมมาแสดงในฟอร์ม (ตรวจสอบว่าเป็นของ profile_id ตนเอง)
$skill = null;
if ($id > 0 && $pid > 0) {
    $stmt = mysqli_prepare($conn, "SELECT id, skill_name, skill_level FROM skill WHERE id = ? AND profile_id = ? LIMIT 1");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ii', $id, $pid);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($res && $row = mysqli_fetch_assoc($res)) {
            $skill = $row;
        }
        mysqli_stmt_close($stmt);
    }
}

// ถ้าไม่พบข้อมูล หรือไม่ใช่เจ้าของข้อมูล ให้ส่งกลับหน้า skills.php
if (!$skill) {
    header('Location: skills.php');
    exit;
}

$name = $skill['skill_name'];
$level = $skill['skill_level'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim((string)($_POST['skill_name'] ?? ''));
    $level = trim((string)($_POST['skill_level'] ?? ''));

    if ($name === '' || mb_strlen($name) > 120 || !in_array($level, ['Beginner', 'Intermediate', 'Advanced'], true)) {
        $error = 'Please enter a valid skill and level.';
    } else {
        // คำสั่ง SQL UPDATE ข้อมูลโดยล็อกเฉพาะ ID และ profile_id ของผู้ใช้คนนั้น
        $sql = "UPDATE skill SET skill_name = ?, skill_level = ? WHERE id = ? AND profile_id = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            $error = 'SQL preparation failed.';
        } else {
            mysqli_stmt_bind_param($stmt, 'ssii', $name, $level, $id, $pid);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header('Location: skills.php?success=updated');
                exit;
            }
            $error = 'Update failed.';
            mysqli_stmt_close($stmt);
        }
    }
}

page_start('EDIT | Update Skill');
if ($error) echo '<p class="err">' . h($error) . '</p>';
?>

<form method="post">
    <?php csrf_field(); ?>
    <label for="skill">Skill name</label>
    <input id="skill" name="skill_name" required maxlength="120" value="<?= h($name) ?>">

    <label for="level">Skill level</label>
    <select id="level" name="skill_level">
        <?php foreach (['Beginner', 'Intermediate', 'Advanced'] as $choice): ?>
            <option <?= ($choice === $level ? 'selected' : '') ?>><?= h($choice) ?></option>
        <?php endforeach; ?>
    </select>

    <p>
        <button type="submit">Save changes</button>
        <a href="skills.php" style="margin-left: 10px; text-decoration: none;">Cancel</a>
    </p>
</form>

<?php page_end(); ?>