<?php

namespace App\Http\Controllers;

use App\Models\Keyword;
use Illuminate\Http\Request;

class KeywordController extends Controller
{
    // عرض جميع الكلمات الدلالية
    public function index()
    {
        // الحصول على جميع الكلمات الدلالية مع تقسيم الصفحات (Pagination)
        $keywords = Keyword::paginate(20); // يمكنك تغيير العدد حسب الحاجة

        // تمرير الكلمات إلى العرض
        return view('frontend.keywords.index', compact('keywords'));
    }

    









}
