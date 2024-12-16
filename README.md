# AlemEdu - منصة تعليمية متعددة المواقع

منصة تعليمية متكاملة مبنية باستخدام Laravel لإدارة المحتوى التعليمي عبر مواقع متعددة.

## المميزات الرئيسية

- إدارة متعددة المواقع (Multi-tenancy)
- نظام إدارة المستخدمين والصلاحيات
- إدارة المحتوى التعليمي
- نظام التقارير والتحليلات
- واجهة مستخدم حديثة وسهلة الاستخدام
- نظام تتبع الأداء والأمان

## المتطلبات التقنية

- PHP >= 8.1
- Laravel >= 10.x
- MySQL >= 5.7 أو MariaDB >= 10.3
- Node.js >= 14.x
- Composer

## التثبيت

1. استنساخ المشروع:
```bash
git clone https://github.com/tuhmaz/alemedu.git
cd alemedu
```

2. تثبيت التبعيات:
```bash
composer install
npm install
```

3. إعداد ملف البيئة:
```bash
cp .env.example .env
php artisan key:generate
```

4. تهيئة قاعدة البيانات:
```bash
php artisan migrate
php artisan db:seed
```

5. بناء الأصول:
```bash
npm run build
```

6. ربط مجلد التخزين:
```bash
php artisan storage:link
```

## المساهمة

نرحب بمساهماتكم في تطوير المشروع. يرجى اتباع الخطوات التالية:

1. Fork المشروع
2. إنشاء فرع للميزة الجديدة
3. تقديم Pull Request

## الترخيص

جميع الحقوق محفوظة © 2024
