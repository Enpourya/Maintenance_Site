<?php
// شروع session اگر قبلاً شروع نشده
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// پاک کردن تمام session ها
session_destroy();

// هدایت به صفحه ورود
header('Location: login.php?logout=success');
exit();
?>