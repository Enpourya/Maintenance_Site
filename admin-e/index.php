<?php
require_once 'config.php';
checkLogin();

$data = readData();

// ذخیره تغییرات
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_settings'])) {
        // بروزرسانی تنظیمات اصلی
        $data['site_name'] = $_POST['site_name'] ?? $data['site_name'];
        $data['maintenance_text'] = $_POST['maintenance_text'] ?? $data['maintenance_text'];
        $data['logo_path'] = $_POST['logo_path'] ?? $data['logo_path'];
        $data['animation_speed'] = intval($_POST['animation_speed'] ?? $data['animation_speed']);
        $data['show_security'] = isset($_POST['show_security']);
        
        // بروزرسانی شبکه‌های اجتماعی
        if (isset($_POST['social_name'])) {
            $data['social_links'] = [];
            foreach ($_POST['social_name'] as $key => $name) {
                if (!empty(trim($name))) {
                    $data['social_links'][] = [
                        'name' => trim($name),
                        'icon' => $_POST['social_icon'][$key] ?? '',
                        'url' => $_POST['social_url'][$key] ?? '',
                        'alt' => $_POST['social_alt'][$key] ?? trim($name)
                    ];
                }
            }
        }
        
        if (saveData($data)) {
            $message = '✅ تنظیمات با موفقیت ذخیره شد';
            $messageType = 'success';
        } else {
            $message = '❌ خطا در ذخیره تنظیمات';
            $messageType = 'error';
        }
    }
}
?>
<!-- بقیه HTML صفحه مدیریت -->
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل مدیریت - <?php echo htmlspecialchars($data['site_name']); ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Vazirmatn', Tahoma, sans-serif;
            background: #f0f2f5;
            direction: rtl;
            line-height: 1.6;
        }
        
        /* هدر */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header h1 {
            font-size: 22px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .header-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-family: inherit;
        }
        
        .btn-primary {
            background: #4CAF50;
            color: white;
        }
        
        .btn-primary:hover {
            background: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.4);
        }
        
        .btn-secondary {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
        }
        
        .btn-secondary:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        /* کانتینر اصلی */
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        /* کارت‌ها */
        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            transition: box-shadow 0.3s;
        }
        
        .card:hover {
            box-shadow: 0 5px 25px rgba(0,0,0,0.12);
        }
        
        .card h2 {
            color: #333;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #667eea;
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* فرم‌ها */
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
            font-size: 14px;
        }
        
        input[type="text"],
        input[type="url"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s;
            background: #fafafa;
        }
        
        input:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        /* پیش‌نمایش لوگو */
        .logo-preview {
            width: 120px;
            height: 120px;
            object-fit: contain;
            border: 3px solid #e0e0e0;
            border-radius: 15px;
            padding: 10px;
            background: #fafafa;
            margin-bottom: 15px;
        }
        
        /* شبکه‌های اجتماعی */
        .social-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 15px;
            border: 1px solid #e0e0e0;
            position: relative;
            transition: all 0.3s;
        }
        
        .social-item:hover {
            background: #f0f2f5;
            border-color: #667eea;
        }
        
        .social-item .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .social-item .remove-btn {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        
        .social-item .remove-btn:hover {
            background: #c82333;
            transform: scale(1.1);
        }
        
        /* پیام‌ها */
        .message {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-weight: 500;
            animation: slideIn 0.5s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* چک باکس */
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        
        .checkbox-group label {
            margin: 0;
            cursor: pointer;
            user-select: none;
        }
        
        /* فوتر */
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 14px;
        }
        
        /* رسپانسیو */
        @media (max-width: 768px) {
            .header {
                padding: 15px;
                flex-direction: column;
                gap: 10px;
            }
            
            .header-actions {
                width: 100%;
                justify-content: center;
            }
            
            .container {
                padding: 0 10px;
            }
            
            .card {
                padding: 20px;
            }
            
            .social-item .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>
            <span>🎨</span>
            پنل مدیریت - <?php echo htmlspecialchars($data['site_name']); ?>
        </h1>
        <div class="header-actions">
            <a href="../index.html" target="_blank" class="btn btn-secondary">👁️ پیش‌نمایش سایت</a>
            <a href="logout.php" class="btn btn-danger">🚪 خروج</a>
        </div>
    </div>
    
    <div class="container">
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <!-- تنظیمات اصلی -->
            <div class="card">
                <h2>📝 تنظیمات اصلی</h2>
                
                <div class="form-group">
                    <label>🏷️ نام سایت:</label>
                    <input type="text" name="site_name" value="<?php echo htmlspecialchars($data['site_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>📢 متن در حال بروزرسانی:</label>
                    <input type="text" name="maintenance_text" value="<?php echo htmlspecialchars($data['maintenance_text']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>🖼️ لوگوی سایت:</label>
                    <img src="../<?php echo htmlspecialchars($data['logo_path']); ?>" alt="پیش‌نمایش لوگو" class="logo-preview" id="logoPreview">
                    <input type="text" name="logo_path" value="<?php echo htmlspecialchars($data['logo_path']); ?>" id="logoPath" onchange="updateLogoPreview()">
                    <small style="color: #666; display: block; margin-top: 5px;">📁 مسیر فایل نسبت به پوشه اصلی (مثال: icon/logo.png)</small>
                </div>
            </div>
            
            <!-- شبکه‌های اجتماعی -->
            <div class="card">
                <h2>🔗 شبکه‌های اجتماعی</h2>
                <div id="socialLinksContainer">
                    <?php foreach ($data['social_links'] as $index => $social): ?>
                    <div class="social-item">
                        <button type="button" class="remove-btn" onclick="removeSocialLink(this)" title="حذف">×</button>
                        <div class="form-row">
                            <div class="form-group">
                                <label>📱 نام شبکه:</label>
                                <input type="text" name="social_name[]" value="<?php echo htmlspecialchars($social['name']); ?>" placeholder="مثال: تلگرام">
                            </div>
                            <div class="form-group">
                                <label>🖼️ آدرس آیکون:</label>
                                <input type="text" name="social_icon[]" value="<?php echo htmlspecialchars($social['icon']); ?>" placeholder="icon/telegram.jpg">
                            </div>
                            <div class="form-group">
                                <label>🔗 لینک:</label>
                                <input type="url" name="social_url[]" value="<?php echo htmlspecialchars($social['url']); ?>" placeholder="https://t.me/...">
                            </div>
                            <div class="form-group">
                                <label>📝 متن جایگزین:</label>
                                <input type="text" name="social_alt[]" value="<?php echo htmlspecialchars($social['alt']); ?>" placeholder="Telegram">
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn btn-secondary" onclick="addSocialLink()" style="margin-top: 15px;">
                    ➕ افزودن شبکه اجتماعی جدید
                </button>
            </div>
            
            <!-- تنظیمات ظاهری -->
            <div class="card">
                <h2>🎨 تنظیمات ظاهری</h2>
                
                <div class="form-group">
                    <label>⏱️ سرعت انیمیشن (ثانیه):</label>
                    <input type="number" name="animation_speed" value="<?php echo htmlspecialchars($data['animation_speed'] ?? 15); ?>" min="5" max="30">
                    <small style="color: #666; display: block; margin-top: 5px;">⏰ بین 5 تا 30 ثانیه (پیش‌فرض: 15)</small>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" name="show_security" id="showSecurity" <?php echo ($data['show_security'] ?? true) ? 'checked' : ''; ?>>
                        <label for="showSecurity">🛡️ فعال کردن محدودیت‌های امنیتی</label>
                    </div>
                    <small style="color: #666; display: block; margin-top: 5px;">
                        ⚠️ غیرفعال کردن کلیک راست، F12 و انتخاب متن
                    </small>
                </div>
            </div>
            
            <!-- دکمه‌های ذخیره -->
            <div style="display: flex; gap: 15px; justify-content: center; margin-bottom: 30px;">
                <button type="submit" name="save_settings" class="btn btn-primary" style="font-size: 18px; padding: 15px 40px;">
                    💾 ذخیره تمام تغییرات
                </button>
                <button type="reset" class="btn btn-secondary" style="font-size: 18px; padding: 15px 40px; background: #6c757d; color: white;">
                    🔄 بازنشانی فرم
                </button>
            </div>
        </form>
        
        <div class="footer">
            <p>🎨 پنل مدیریت گالری اوپال | نسخه 1.0</p>
            <p style="margin-top: 5px; font-size: 12px;">تمامی تغییرات در فایل data.json ذخیره می‌شود</p>
        </div>
    </div>
    
    <script>
        function updateLogoPreview() {
            const logoPath = document.getElementById('logoPath').value;
            const preview = document.getElementById('logoPreview');
            preview.src = '../' + logoPath;
        }
        
        function addSocialLink() {
            const container = document.getElementById('socialLinksContainer');
            const newItem = document.createElement('div');
            newItem.className = 'social-item';
            newItem.innerHTML = `
                <button type="button" class="remove-btn" onclick="removeSocialLink(this)" title="حذف">×</button>
                <div class="form-row">
                    <div class="form-group">
                        <label>📱 نام شبکه:</label>
                        <input type="text" name="social_name[]" placeholder="مثال: تلگرام">
                    </div>
                    <div class="form-group">
                        <label>🖼️ آدرس آیکون:</label>
                        <input type="text" name="social_icon[]" placeholder="icon/telegram.jpg">
                    </div>
                    <div class="form-group">
                        <label>🔗 لینک:</label>
                        <input type="url" name="social_url[]" placeholder="https://t.me/...">
                    </div>
                    <div class="form-group">
                        <label>📝 متن جایگزین:</label>
                        <input type="text" name="social_alt[]" placeholder="Telegram">
                    </div>
                </div>
            `;
            container.appendChild(newItem);
            
            // اسکرول به آیتم جدید
            newItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        function removeSocialLink(button) {
            if (confirm('آیا از حذف این شبکه اجتماعی اطمینان دارید؟')) {
                const socialItem = button.parentElement;
                socialItem.style.opacity = '0';
                socialItem.style.transform = 'scale(0.9)';
                setTimeout(() => {
                    socialItem.remove();
                }, 300);
            }
        }
    </script>
</body>
</html>