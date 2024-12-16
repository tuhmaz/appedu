<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\LoginAttempt;
use App\Jobs\CleanupLoginAttempts;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LoginAttemptsController extends Controller
{
    public function __construct()
    {
        // فحص وتنظيف المحاولات الناجحة عند كل طلب
        $this->checkAndCleanupAttempts();
    }

    private function checkAndCleanupAttempts()
    {
        $successfulCount = LoginAttempt::where('successful', true)->count();
        if ($successfulCount > 1000) {
            CleanupLoginAttempts::dispatch();
        }
    }

    public function index()
    {
        $attempts = LoginAttempt::latest()
            ->paginate(15);

        $statistics = [
            'total' => LoginAttempt::count(),
            'successful' => LoginAttempt::where('successful', true)->count(),
            'failed' => LoginAttempt::where('successful', false)->count(),
            'recent_failed' => LoginAttempt::where('successful', false)
                ->where('created_at', '>=', now()->subHours(24))
                ->count(),
        ];

        return view('dashboard.security.login-attempts.index', compact('attempts', 'statistics'));
    }

    public function show(LoginAttempt $attempt)
    {
        return view('dashboard.security.login-attempts.show', compact('attempt'));
    }

    public function clearOld()
    {
        // حذف المحاولات القديمة (أكثر من 30 يوم)
        LoginAttempt::where('created_at', '<', now()->subDays(30))->delete();

        return redirect()->route('login-attempts.index')
            ->with('success', 'تم حذف المحاولات القديمة بنجاح');
    }

    public function deleteSelected(Request $request)
    {
        $selectedIds = $request->input('selected_attempts', []);
        
        if (empty($selectedIds)) {
            return redirect()->route('login-attempts.index')
                ->with('error', 'الرجاء تحديد محاولات للحذف');
        }

        LoginAttempt::whereIn('id', $selectedIds)->delete();

        return redirect()->route('login-attempts.index')
            ->with('success', 'تم حذف المحاولات المحددة بنجاح');
    }

    public function securityReport()
    {
        // تقرير المحاولات المشبوهة في آخر 24 ساعة
        $suspiciousAttempts = LoginAttempt::where('successful', false)
            ->where('created_at', '>=', now()->subHours(24))
            ->select('ip_address', DB::raw('count(*) as attempts_count'))
            ->groupBy('ip_address')
            ->having('attempts_count', '>', 5)
            ->orderBy('attempts_count', 'desc')
            ->get();

        // تقرير المحاولات حسب الوقت
        $timeBasedAttempts = LoginAttempt::where('created_at', '>=', now()->subDays(7))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total_attempts'),
                DB::raw('sum(case when successful = 0 then 1 else 0 end) as failed_attempts')
            )
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // تقرير أكثر عناوين IP استخداماً
        $topIpAddresses = LoginAttempt::where('created_at', '>=', now()->subDays(7))
            ->select('ip_address', 
                DB::raw('count(*) as total_attempts'),
                DB::raw('sum(case when successful = 0 then 1 else 0 end) as failed_attempts'),
                DB::raw('sum(case when successful = 1 then 1 else 0 end) as successful_attempts')
            )
            ->groupBy('ip_address')
            ->orderBy('failed_attempts', 'desc')
            ->limit(10)
            ->get();

        // تقرير أنماط محاولات الاختراق
        $commonPatterns = LoginAttempt::where('successful', false)
            ->where('created_at', '>=', now()->subDays(7))
            ->select('email', 
                DB::raw('count(*) as attempts_count'),
                DB::raw('GROUP_CONCAT(DISTINCT failure_reason) as failure_reasons')
            )
            ->groupBy('email')
            ->having('attempts_count', '>', 3)
            ->orderBy('attempts_count', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.security.login-attempts.security-report', compact(
            'suspiciousAttempts',
            'timeBasedAttempts',
            'topIpAddresses',
            'commonPatterns'
        ));
    }

    public function exportSecurityReport()
    {
        $fileName = 'security_report_' . now()->format('Y_m_d_His') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $suspiciousIPs = LoginAttempt::where('successful', false)
            ->where('created_at', '>=', now()->subHours(24))
            ->select('ip_address', DB::raw('count(*) as attempts_count'))
            ->groupBy('ip_address')
            ->having('attempts_count', '>', 5)
            ->orderBy('attempts_count', 'desc')
            ->get();

        $callback = function() use ($suspiciousIPs) {
            $file = fopen('php://output', 'w');
            
            // عناوين الأعمدة
            fputcsv($file, ['IP العنوان', 'عدد المحاولات', 'آخر محاولة', 'أسباب الفشل']);

            foreach ($suspiciousIPs as $ip) {
                $lastAttempt = LoginAttempt::where('ip_address', $ip->ip_address)
                    ->latest()
                    ->first();

                $failureReasons = LoginAttempt::where('ip_address', $ip->ip_address)
                    ->where('successful', false)
                    ->pluck('failure_reason')
                    ->unique()
                    ->filter()
                    ->implode(', ');

                fputcsv($file, [
                    $ip->ip_address,
                    $ip->attempts_count,
                    $lastAttempt->created_at->format('Y-m-d H:i:s'),
                    $failureReasons ?: 'غير محدد'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
