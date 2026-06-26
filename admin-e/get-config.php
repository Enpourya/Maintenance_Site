<?php
// غیرفعال کردن نمایش خطاها در خروجی
ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, must-revalidate');

require_once 'config.php';

try {
    $data = readData();
    
    // حذف اطلاعات حساس قبل از ارسال
    unset($data['background_colors']);
    
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'خطا در بارگذاری تنظیمات'
    ]);
}
?>