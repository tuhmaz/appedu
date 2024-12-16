<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnalyticsController extends Controller
{
    public function dashboard()
    {
        // Get current online visitors (active in last 5 minutes)
        $onlineVisitors = Visitor::where('last_activity', '>=', now()->subMinutes(5))
            ->select('session_id')
            ->groupBy('session_id')
            ->get()
            ->count();

        // Get today's unique visitors
        $todayVisitors = DB::table('visitors')
            ->select('ip_address')
            ->whereDate('created_at', today())
            ->groupBy('ip_address')
            ->get()
            ->count();

        // Get server performance metrics
        $averageResponseTime = Visitor::whereNotNull('response_time')
            ->where('created_at', '>=', now()->subHours(1))
            ->avg('response_time');

        // Get visitor locations for last 7 days
        $visitorLocations = DB::table('visitors')
            ->select('country', DB::raw('COUNT(DISTINCT ip_address) as total'))
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('country')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Get device type distribution
        $deviceTypes = DB::table('visitors')
            ->select('device_type', DB::raw('COUNT(DISTINCT ip_address) as total'))
            ->whereNotNull('device_type')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('device_type')
            ->get();

        // Get hourly visitor trend for today
        $hourlyTrend = DB::table('visitors')
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(DISTINCT ip_address) as total'))
            ->whereDate('created_at', today())
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        Log::info('Analytics Data:', [
            'online_visitors' => $onlineVisitors,
            'today_visitors' => $todayVisitors,
            'locations' => $visitorLocations
        ]);

        return view('analytics.dashboard', compact(
            'onlineVisitors',
            'todayVisitors',
            'averageResponseTime',
            'visitorLocations',
            'deviceTypes',
            'hourlyTrend'
        ));
    }
}
