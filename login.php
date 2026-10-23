<?php
require 'auth.php'; 
require 'config/db.php';

// จุดที่ 1: ถ้าล็อกอินอยู่แล้ว ให้เปลี่ยนทางไปที่ index.php แทน skills.php
if (!empty($_SESSION['user_id'])) { 
    header('Location: index.php'); 
    exit; 
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_auth_token();
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Enter email and password.';
    } else {
        // TODO 1: Query a single user by email using a prepared statement.
        $sql = 'SELECT id, profile_id, password_hash FROM users WHERE email = ? LIMIT 1';
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // TODO 2: Use mysqli_stmt_bind_param / execute / bind_result / fetch.
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $id, $profile_id, $password_hash);
            
            if (mysqli_stmt_fetch($stmt)) {
                // TODO 3: Verify plaintext password with password_verify($password, $hash).
                if (password_verify($password, $password_hash)) {
                    
                    mysqli_stmt_close($stmt);
                    
                    // TODO 4: On success, session_regenerate_id(true) and set session values
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $id;
                    $_SESSION['profile_id'] = $profile_id;
                    $_SESSION['email'] = $email;
                    
                    // TODO 5: จุดที่ 2 Redirect ไปที่หน้า index.php และ exit
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Invalid email or password.';
                }
            } else {
                $error = 'Invalid email or password.';
            }
            
            mysqli_stmt_close($stmt);
        } else {
            $error = 'Database error occurred.';
        }
    }
}
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/crud.css">
</head>
<body>
<main class="wrap">
    <div class="panel">
        <h1>Resume Lab · Login</h1>
        <p>PHP Session + MySQL Authentication</p>
        <?php if(isset($_GET['registered'])): ?><p class="ok">Registration complete. Please login.</p><?php endif; ?>
        <?php if(isset($_GET['logged_out'])): ?><p class="ok">Logged out.</p><?php endif; ?>
        <?php if($error): ?><p class="err"><?=auth_escape($error)?></p><?php endif; ?>
        <form method="post">
            <input type="hidden" name="auth_csrf" value="<?=auth_escape(auth_token())?>">
            <label>Email</label>
            <input name="email" type="email" required autocomplete="username">
            <label>Password</label>
            <input name="password" type="password" required autocomplete="current-password">
            <p><button>Login</button></p>
        </form>
        <p>New user? <a href="register.php">Register</a></p>
    </div>
</main>
</body>
</html>