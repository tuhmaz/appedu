<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class TrackVisitor
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // التحقق من وجود الجدول
            if (!Schema::hasTable('visitors')) {
                Log::warning('Visitors table does not exist in the current database');
                return $next($request);
            }

            $start = microtime(true);
            $response = $next($request);
            $responseTime = (microtime(true) - $start) * 1000;
            
            if (!Session::isStarted()) {
                Session::start();
            }
            
            $ipAddress = $request->ip();
            if ($ipAddress === '127.0.0.1' || $ipAddress === '::1') {
                $ipAddress = '8.8.8.8';
            }
            
            $sessionId = Session::getId();
            
            try {
                // Try to get cached location data first
                $locationData = Cache::remember("visitor_location_{$ipAddress}", 3600, function () use ($ipAddress) {
                    try {
                        $response = Http::timeout(1)->get("http://ip-api.com/json/{$ipAddress}");
                        if ($response->successful()) {
                            return $response->json();
                        }
                    } catch (\Exception $e) {
                        Log::warning("Primary location lookup failed: {$e->getMessage()}");
                    }
                    
                    // Fallback to a secondary service or return default
                    return [
                        'status' => 'success',
                        'country' => 'Unknown',
                        'city' => 'Unknown'
                    ];
                });
                
                $country = $locationData['country'] ?? 'Unknown';
                $city = $locationData['city'] ?? 'Unknown';
                
                if (isset($locationData['status']) && $locationData['status'] === 'success') {
                    Log::info('Location data retrieved', [
                        'ip' => $ipAddress,
                        'country' => $country,
                        'city' => $city
                    ]);
                }

                // تحسين معالجة URL الطويل
                $pageUrl = $request->fullUrl();
                if (strlen($pageUrl) > 1000) {
                    $pageUrl = substr($pageUrl, 0, 997) . '...';
                }

                // Update or create visitor record
                DB::beginTransaction();
                try {
                    Visitor::updateOrCreate(
                        ['session_id' => $sessionId],
                        [
                            'ip_address' => $ipAddress,
                            'last_activity' => now(),
                            'user_agent' => $request->userAgent(),
                            'page_url' => $pageUrl,
                            'device_type' => $this->getDeviceType($request->userAgent()),
                            'response_time' => $responseTime,
                            'country' => $country,
                            'city' => $city,
                        ]
                    );
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Error saving visitor data: ' . $e->getMessage());
                }

            } catch (\Exception $e) {
                Log::error('Error tracking visitor: ' . $e->getMessage(), [
                    'ip' => $ipAddress,
                    'session_id' => $sessionId
                ]);
            }
            
            return $response;
        } catch (\Exception $e) {
            Log::error('Critical error in TrackVisitor middleware: ' . $e->getMessage());
            return $next($request);
        }
    }

    private function getDeviceType($userAgent)
    {
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($userAgent))) {
            return 'tablet';
        }
        
        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($userAgent))) {
            return 'mobile';
        }
        
        return 'desktop';
    }
}
