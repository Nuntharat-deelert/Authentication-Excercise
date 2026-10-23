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

// ดึงข้อมูล Skill มาตรวจสอบสิทธิ์ก่อนว่าเป็นของ profile_id ตัวเองหรือไม่
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

if (!$skill) {
    header('Location: skills.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    // คำสั่ง SQL DELETE โดยต้องระบุทั้ง id และ profile_id เพื่อความปลอดภัย (Owner-scoped CRUD)
    $sql = "DELETE FROM skill WHERE id = ? AND profile_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        $error = 'SQL delete preparation failed.';
    } else {
        mysqli_stmt_bind_param($stmt, 'ii', $id, $pid);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header('Location: skills.php?success=deleted');
            exit;
        }
        $error = 'Delete failed.';
        mysqli_stmt_close($stmt);
    }
}

page_start('DELETE | Remove Skill');
if ($error) echo '<p class="err">' . h($error) . '</p>';
?>

<div class="panel">
    <p>คุณต้องการลบทักษะ <strong><?= h($skill['skill_name']) ?></strong> (<?= h($skill['skill_level']) ?>) ใช่หรือไม่?</p>
    <form method="post">
        <?php csrf_field(); ?>
        <p>
            <button type="submit" style="background-color: #dc3545; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">
                ยืนยันการลบ (Delete)
            </button>
            <a href="skills.php" style="margin-left: 10px; text-decoration: none;">ยกเลิก</a>
        </p>
    </form>
</div>

<?php page_end(); ?>