<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ============================================
// Authentication APIs
// ============================================
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ============================================
// Test & Health Check APIs
// ============================================
Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

Route::get('/test-geocode', function() {
    return response()->json(['message' => 'Test route working']);
});

Route::get('/map/test', function() {
    return response()->json(['success' => true, 'message' => 'API is working', 'timestamp' => now()]);
});

// ============================================
// Map & Geocoding APIs
// ============================================
Route::post('/map/geocode', [\App\Http\Controllers\MapController::class, 'geocode'])->name('api.map.geocode');
Route::post('/map/route', [\App\Http\Controllers\MapController::class, 'getRoute'])->name('api.map.route');
Route::post('/map/shipping-calculate', [\App\Http\Controllers\MapController::class, 'calculateShippingWithDistance'])->name('api.map.shipping');

// ============================================
// Shipping Fee APIs
// ============================================
Route::post('/calculate-shipping-fee', [\App\Http\Controllers\ShippingFeeController::class, 'calculate']);

// ============================================
// Analytics APIs
// ============================================
Route::get('/analytics/compact', function () {
    $today = \Carbon\Carbon::today();
    $thisMonth = \Carbon\Carbon::now()->startOfMonth();
    // Daily last 7
    $daily = [];
    for ($i=6;$i>=0;$i--) {
        $day = \Carbon\Carbon::now()->subDays($i);
        $revenue = \App\Models\Order::whereBetween('created_at', [$day->copy()->startOfDay(), $day->copy()->endOfDay()])
            ->where('status','!=','cancelled')->sum('total_price');
        $daily[] = ['day'=>$day->format('M d'),'revenue'=>$revenue];
    }
    // Monthly last 6
    $monthly = [];
    for ($i=5;$i>=0;$i--) {
        $m = \Carbon\Carbon::now()->subMonths($i);
        $revenue = \App\Models\Order::whereBetween('created_at', [$m->copy()->startOfMonth(), $m->copy()->endOfMonth()])
            ->where('status','!=','cancelled')->sum('total_price');
        $monthly[] = ['month'=>$m->format('M Y'),'revenue'=>$revenue];
    }
    return response()->json(['daily'=>$daily,'monthly'=>$monthly]);
});

// ============================================
// Geo Optimization APIs
// ============================================
Route::get('/geo/content', [App\Http\Controllers\GeoOptimizationController::class, 'getLocationBasedContent'])->name('geo.content');
Route::post('/geo/location', [App\Http\Controllers\GeoOptimizationController::class, 'updateLocation'])->name('geo.location.update');
Route::get('/geo/homepage-products', [App\Http\Controllers\GeoOptimizationController::class, 'getHomepageProducts'])->name('geo.homepage.products');

// ============================================
// Inventory API endpoints
// ============================================
Route::get('/inventory-items', [\App\Http\Controllers\Clerk\ClerkController::class, 'getInventoryItems']);
