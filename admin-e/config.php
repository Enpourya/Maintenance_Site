<?php
// تنظیمات پایه
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// تنظیم منطقه زمانی
date_default_timezone_set('Asia/Tehran');

// تنظیمات امنیتی - حتماً تغییر دهید!
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', '123456'); // رمز عبور را تغییر دهید

// مسیرهای فایل
define('DATA_FILE', __DIR__ . DIRECTORY_SEPARATOR . 'data.json');
define('UPLOAD_DIR', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'icon' . DIRECTORY_SEPARATOR);

// فعال کردن خطاها فقط در محیط توسعه
// در محیط تولید این خطوط را حذف کنید
ini_set('display_errors', 0);
error_reporting(0);

// تنظیمات CORS برای ارتباط با frontend
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// بررسی و ایجاد پوشه‌های مورد نیاز
if (!file_exists(UPLOAD_DIR)) {
    @mkdir(UPLOAD_DIR, 0755, true);
}

// جلوگیری از دسترسی مستقیم به فایل
if (basename($_SERVER['SCRIPT_FILENAME']) === 'config.php') {
    header('HTTP/1.0 403 Forbidden');
    exit('Access Denied');
}

// بررسی لاگین
function checkLogin() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: login.php');
        exit();
    }
}

// خواندن داده‌ها
function readData() {
    $defaultData = [
        'site_name' => 'گالری اوپال',
        'maintenance_text' => 'سایت در حال بروزرسانی می‌باشد',
        'logo_path' => 'icon/logo.png',
        'social_links' => [
            [
                'name' => 'روبیکا',
                'icon' => 'icon/icon1.jpg',
                'url' => 'https://rubika.ir/gallery_opal',
                'alt' => 'Rubika'
            ],
            [
                'name' => 'ایتا',
                'icon' => 'icon/icon2.jpg',
                'url' => 'https://eitaa.com/opalgallary',
                'alt' => 'Eitaa'
            ],
            [
                'name' => 'بله',
                'icon' => 'icon/icon3.jpg',
                'url' => 'https://ble.ir/opal_shopping',
                'alt' => 'Bale'
            ]
        ],
        'animation_speed' => 15,
        'show_security' => true
    ];
    
    if (file_exists(DATA_FILE)) {
        $json = @file_get_contents(DATA_FILE);
        if ($json !== false) {
            $data = json_decode($json, true);
            if ($data && is_array($data)) {
                // ادغام با مقادیر پیش‌فرض برای اطمینان از وجود همه کلیدها
                return array_merge($defaultData, $data);
            }
        }
    }
    
    // ذخیره داده‌های پیش‌فرض اگر فایل وجود نداشته باشد
    saveData($defaultData);
    
    return $defaultData;
}

// ذخیره داده‌ها
function saveData($data) {
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    if ($json === false) {
        error_log('JSON encoding error: ' . json_last_error_msg());
        return false;
    }
    
    $result = @file_put_contents(DATA_FILE, $json);
    if ($result === false) {
        error_log('Failed to write to data.json');
        return false;
    }
    
    return true;
}

// آپلود فایل
function uploadFile($file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'message' => 'فایل آپلود نشده است'];
    }
    
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'فرمت فایل مجاز نیست'];
    }
    
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'حجم فایل بیش از حد مجاز است'];
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = uniqid() . '.' . $extension;
    $destination = UPLOAD_DIR . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return [
            'success' => true,
            'path' => 'icon/' . $filename,
            'message' => 'فایل با موفقیت آپلود شد'
        ];
    }
    
    return ['success' => false, 'message' => 'خطا در آپلود فایل'];
}
?>