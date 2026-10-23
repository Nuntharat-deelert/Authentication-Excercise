<?php

require_once 'auth.php';



// ตรวจสอบว่ามี user_id ใน Session หรือยัง ถ้าไม่มีให้ Redirect ไปหน้า login.php

if (empty($_SESSION['user_id'])) {

    header('Location: login.php');

    exit;

}



require_once 'config/db.php';



// Student profile (starter table)

$profile = null;

$sql = "SELECT * FROM profile LIMIT 1";

$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {

    $profile = mysqli_fetch_assoc($result);

}



// Education (starter table)

$educations = [];

$sql = "SELECT * FROM education ORDER BY start_year DESC";

$result = mysqli_query($conn, $sql);

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $educations[] = $row;

    }

}



// ==========================================

// ส่วนที่แก้ไข: ดึงข้อมูลทักษะ (Skills) จากฐานข้อมูล

// ==========================================

$skills = [];

$sql_skill = "SELECT * FROM skill WHERE profile_id = 1";

$result_skill = mysqli_query($conn, $sql_skill);

if ($result_skill) {

    while ($row = mysqli_fetch_assoc($result_skill)) {

        $skills[] = $row;

    }

}



// ==========================================

// ส่วนที่แก้ไข: ดึงข้อมูลการอบรม (Training) จากฐานข้อมูล

// ==========================================

$trainings = [];

$sql_training = "SELECT * FROM training WHERE profile_id = 1 ORDER BY training_year DESC";

$result_training = mysqli_query($conn, $sql_training);

if ($result_training) {

    while ($row = mysqli_fetch_assoc($result_training)) {

        $trainings[] = $row;

    }

}

?>

<!doctype html>

<html lang="th">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo $profile ? htmlspecialchars($profile['full_name']) : 'My Resume'; ?></title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

<div class="container">

    <!-- บล็อกแสดงข้อมูลผู้ใช้ ปุ่ม Manage Skills และ Logout -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 10px 15px; background-color: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6;">
        <div>
            <strong>Logged in as:</strong> <?= auth_escape($_SESSION['email'] ?? '') ?>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="skills.php" style="background-color: #0d6efd; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 14px;">Manage Skills</a>
            <form action="logout.php" method="post" style="margin: 0;">
                <input type="hidden" name="auth_csrf" value="<?= auth_escape(auth_token()) ?>">
                <button type="submit" style="background-color: #dc3545; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 14px;">
                    Logout
                </button>
            </form>
        </div>
    </div>

    <header class="hero card">

        <img class="avatar" src="assets/images/<?php echo $profile ? htmlspecialchars($profile['photo']) : 'student.jpg'; ?>" alt="Profile Photo">

        <div>

            <h1><?php echo $profile ? htmlspecialchars($profile['full_name']) : 'Your Name'; ?></h1>

            <p class="headline"><?php echo $profile ? htmlspecialchars($profile['headline']) : 'Student Resume Website'; ?></p>

            <?php if ($profile): ?>

                <p>📧 <?php echo htmlspecialchars($profile['email']); ?></p>

                <p>📱 <?php echo htmlspecialchars($profile['phone']); ?></p>

                <p>📍 <?php echo htmlspecialchars($profile['address']); ?></p>

            <?php endif; ?>

        </div>

    </header>



    <section class="card">

        <h2>About Me</h2>

        <p><?php echo $profile ? nl2br(htmlspecialchars($profile['about_me'])) : 'Add your profile data in phpMyAdmin.'; ?></p>

    </section>



    <section class="card">

        <h2>Education</h2>

        <?php if (count($educations) > 0): ?>

            <?php foreach ($educations as $edu): ?>

                <div class="item">

                    <h3><?php echo htmlspecialchars($edu['degree']); ?></h3>

                    <p><?php echo htmlspecialchars($edu['institution']); ?></p>

                    <p><?php echo htmlspecialchars($edu['start_year']); ?> - <?php echo htmlspecialchars($edu['end_year']); ?></p>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <p>No education data found.</p>

        <?php endif; ?>

    </section>



    <section class="card">

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h2 style="margin: 0;">Skills</h2>
            <a href="skill_create.php" style="background-color: #198754; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 13px;">+ Add Skill</a>
        </div>

        <div class="skills">

            <?php if (count($skills) > 0): ?>

                <?php foreach ($skills as $skill): ?>

                    <div class="skill-badge">

                        <strong><?php echo htmlspecialchars($skill['skill_name']); ?></strong>

                        <span><?php echo htmlspecialchars($skill['skill_level']); ?></span>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <p>No skills data found.</p>

            <?php endif; ?>

        </div>

    </section>



    <section class="card">

        <h2>Training / Certificates</h2>

        <?php if (count($trainings) > 0): ?>

            <?php foreach ($trainings as $training): ?>

                <div class="item">

                    <h3><?php echo htmlspecialchars($training['course_name']); ?></h3>

                    <p><?php echo htmlspecialchars($training['organization']); ?>, <?php echo htmlspecialchars($training['training_year']); ?></p>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <p>No training data found.</p>

        <?php endif; ?>

    </section>



    <footer>

        Database Lab: Personal Resume Website with PHP + MySQL

    </footer>

</div>

</body>

</html>

<?php mysqli_close($conn); ?>