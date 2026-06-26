(function() {
    'use strict';

    // ==================== داده‌های پیش‌فرض (در صورت عدم دسترسی به سرور) ====================
    const defaultConfig = {
        site_name: 'گالری اوپال',
        maintenance_text: 'سایت در حال بروزرسانی می‌باشد',
        logo_path: 'icon/logo.png',
        social_links: [
            { 
                name: 'روبیکا', 
                icon: 'icon/icon1.jpg', 
                url: 'https://rubika.ir/gallery_opal',
                alt: 'Rubika'
            },
            { 
                name: 'ایتا', 
                icon: 'icon/icon2.jpg', 
                url: 'https://eitaa.com/opalgallary',
                alt: 'Eitaa'
            },
            { 
                name: 'بله', 
                icon: 'icon/icon3.jpg', 
                url: 'https://ble.ir/opal_shopping',
                alt: 'Bale'
            }
        ],
        animation_speed: 15,
        show_security: true
    };

    // ==================== بارگذاری تنظیمات از سرور ====================
    async function loadConfig() {
        try {
            const response = await fetch('admin-e/get-config.php');
            if (response.ok) {
                const config = await response.json();
                console.log('✅ تنظیمات از سرور بارگذاری شد');
                return config;
            }
        } catch (error) {
            console.warn('⚠️ خطا در بارگذاری تنظیمات از سرور، استفاده از تنظیمات پیش‌فرض');
        }
        return defaultConfig;
    }

    // ==================== تنظیمات امنیتی ====================
    function setupSecurity(showSecurity) {
        if (!showSecurity) {
            document.body.classList.add('security-disabled');
            return;
        }
        
        // فقط در محیط production فعال شود
        const isProduction = window.location.hostname !== 'localhost' && 
                            window.location.hostname !== '127.0.0.1';
        
        if (isProduction) {
            // جلوگیری از کلیک راست
            document.addEventListener('contextmenu', (e) => {
                e.preventDefault();
                return false;
            });

            // جلوگیری از کلیدهای توسعه‌دهنده
            document.addEventListener('keydown', (e) => {
                // F12
                if (e.key === 'F12') {
                    e.preventDefault();
                    return false;
                }
                
                // Ctrl+Shift+I / Ctrl+Shift+J / Ctrl+U
                if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C')) {
                    e.preventDefault();
                    return false;
                }
                
                // Ctrl+U
                if (e.ctrlKey && e.key === 'U') {
                    e.preventDefault();
                    return false;
                }
            });

            // جلوگیری از درگ کردن تصاویر
            document.addEventListener('dragstart', (e) => {
                if (e.target.tagName === 'IMG') {
                    e.preventDefault();
                    return false;
                }
            });

            // غیرفعال کردن انتخاب متن (پشتیبان CSS)
            document.addEventListener('selectstart', (e) => {
                e.preventDefault();
                return false;
            });
        }
    }

    // ==================== ساخت لینک‌های اجتماعی ====================
    function createSocialLinks(socialLinks) {
        const container = document.getElementById('socialLinks');
        
        // امنیت: پاکسازی کامل container
        while (container.firstChild) {
            container.removeChild(container.firstChild);
        }
        
        // DocumentFragment برای بهبود performance
        const fragment = document.createDocumentFragment();
        
        socialLinks.forEach((social, index) => {
            // ایجاد عنصر لینک
            const link = document.createElement('a');
            link.href = social.url;
            link.target = '_blank';
            link.rel = 'noopener noreferrer nofollow'; // امنیت: جلوگیری از tabnabbing
            link.className = 'social-item';
            link.setAttribute('aria-label', `لینک ${social.name}`);
            link.style.animationDelay = `${1.2 + (index * 0.2)}s`;
            
            // ایجاد تصویر آیکون
            const img = document.createElement('img');
            img.src = social.icon;
            img.alt = social.alt || social.name;
            img.className = 'social-icon';
            img.loading = 'lazy'; // بهینه‌سازی performance
            
            // مدیریت خطای بارگذاری تصویر
            img.onerror = function() {
                // جایگزینی با SVG پیش‌فرض
                const svgString = `<svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 60 60">
                    <rect fill="#800080" width="60" height="60" rx="30"/>
                    <text fill="white" font-size="14" font-weight="bold" x="50%" y="50%" dominant-baseline="middle" text-anchor="middle">${social.name.charAt(0)}</text>
                </svg>`;
                
                this.src = 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svgString);
                this.alt = `آیکون پیش‌فرض ${social.name}`;
                this.classList.add('icon-fallback');
                
                console.warn(`خطا در بارگذاری آیکون: ${social.icon}`);
            };
            
            // مدیریت موفقیت‌آمیز بودن بارگذاری
            img.onload = function() {
                this.classList.add('icon-loaded');
            };
            
            // ایجاد نام شبکه اجتماعی
            const nameSpan = document.createElement('span');
            nameSpan.className = 'social-name';
            nameSpan.textContent = social.name;
            
            // اضافه کردن به لینک
            link.appendChild(img);
            link.appendChild(nameSpan);
            
            // اضافه کردن به fragment
            fragment.appendChild(link);
        });
        
        // اضافه کردن همه به container
        container.appendChild(fragment);
    }

    // ==================== آپدیت محتوای صفحه ====================
    function updatePageContent(config) {
        // آپدیت عنوان صفحه
        document.title = `${config.maintenance_text} - ${config.site_name}`;
        
        // آپدیت نام سایت
        const siteNameElement = document.querySelector('.site-name');
        if (siteNameElement) {
            siteNameElement.textContent = config.site_name;
        }
        
        // آپدیت متن در حال بروزرسانی
        const maintenanceTextElement = document.querySelector('.maintenance-text');
        if (maintenanceTextElement) {
            maintenanceTextElement.textContent = config.maintenance_text;
        }
        
        // آپدیت لوگو
        const logo = document.getElementById('siteLogo');
        if (logo && config.logo_path) {
            logo.src = config.logo_path;
        }
        
        // آپدیت سرعت انیمیشن
        if (config.animation_speed) {
            document.body.style.animationDuration = config.animation_speed + 's';
        }
        
        // ساخت لینک‌های اجتماعی
        if (config.social_links && config.social_links.length > 0) {
            createSocialLinks(config.social_links);
        }
    }

    // ==================== مدیریت لوگو ====================
    function handleLogo() {
        const logo = document.getElementById('siteLogo');
        
        if (!logo) return;
        
        // مدیریت خطای بارگذاری لوگو
        logo.onerror = function() {
            const container = this.parentElement;
            
            // ایجاد لوگوی پیش‌فرض
            const fallback = document.createElement('div');
            fallback.className = 'logo-error';
            fallback.innerHTML = '🏠';
            fallback.setAttribute('aria-label', 'لوگوی پیش‌فرض');
            
            // جایگزینی
            container.replaceChild(fallback, this);
            
            console.warn('خطا در بارگذاری لوگو');
        };
        
        // افکت کلیک روی لوگو
        logo.onclick = function() {
            this.style.transform = 'scale(0.9) rotate(-10deg)';
            setTimeout(() => {
                this.style.transform = 'scale(1.1) rotate(5deg)';
                setTimeout(() => {
                    this.style.transform = 'scale(1) rotate(0deg)';
                }, 200);
            }, 150);
        };
    }

    // ==================== راه‌اندازی ====================
    async function init() {
        try {
            // بارگذاری تنظیمات
            const config = await loadConfig();
            
            // آپدیت محتوای صفحه
            updatePageContent(config);
            
            // تنظیمات امنیتی
            setupSecurity(config.show_security);
            
            // مدیریت لوگو
            handleLogo();
            
            // اضافه کردن کلاس برای انیمیشن‌های اولیه
            document.body.classList.add('loaded');
            
            // لاگ در محیط توسعه
            if (window.location.hostname === 'localhost') {
                console.log('🚀 صفحه با موفقیت بارگذاری شد');
                console.log('📱 تنظیمات:', config);
                console.log('🛡️ وضعیت امنیتی:', config.show_security ? 'فعال' : 'غیرفعال');
            }
        } catch (error) {
            console.error('❌ خطا در راه‌اندازی:', error);
            
            // استفاده از تنظیمات پیش‌فرض در صورت خطا
            updatePageContent(defaultConfig);
            setupSecurity(true);
            handleLogo();
            document.body.classList.add('loaded');
        }
    }

    // اجرا با توجه به وضعیت بارگذاری DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();