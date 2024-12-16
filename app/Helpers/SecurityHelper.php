<?php

namespace App\Helpers;

class SecurityHelper
{
    /**
     * تنظيف النص من أي محتوى خطير
     *
     * @param string $input
     * @return string
     */
    public static function clean($input)
    {
        if (is_array($input)) {
            return array_map([self::class, 'clean'], $input);
        }
        
        // إزالة الأكواد الضارة المحتملة
        $input = strip_tags($input);
        
        // تحويل الرموز الخاصة إلى كيانات HTML
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        
        // إزالة الـ NULL bytes
        $input = str_replace(chr(0), '', $input);
        
        // تنظيف محتمل لـ SQL injection
        $input = str_replace(['\\', '"', "'", "\r", "\n"], ['\\\\', '\\"', "\\'", '\\r', '\\n'], $input);
        
        return $input;
    }

    /**
     * تنظيف مصفوفة من المدخلات
     *
     * @param array $input
     * @return array
     */
    public static function cleanArray(array $input)
    {
        return array_map([self::class, 'clean'], $input);
    }

    /**
     * التحقق من صحة URL
     *
     * @param string $url
     * @return bool
     */
    public static function isValidUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * تنظيف وتحقق من صحة عنوان البريد الإلكتروني
     *
     * @param string $email
     * @return string|bool
     */
    public static function sanitizeEmail($email)
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : false;
    }
}
