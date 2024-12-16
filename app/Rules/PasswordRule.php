<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PasswordRule implements Rule
{
    public function passes($attribute, $value)
    {
        // يجب أن تحتوي كلمة المرور على:
        // - 8 أحرف على الأقل
        // - حرف كبير واحد على الأقل
        // - حرف صغير واحد على الأقل
        // - رقم واحد على الأقل
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/', $value);
    }

    public function message()
    {
        return 'يجب أن تحتوي كلمة المرور على 8 أحرف على الأقل، وتتضمن حروفاً كبيرة وصغيرة وأرقاماً.';
    }
}
