<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class TestRedisController extends Controller
{
    public function testRedis()
    {
        try {
            Redis::set('test_key', 'Redis is working!');
            $value = Redis::get('test_key');
            
            return response()->json([
                'status' => 'success',
                'message' => 'Redis is working properly',
                'test_value' => $value
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Redis error: ' . $e->getMessage()
            ], 500);
        }
    }
}
