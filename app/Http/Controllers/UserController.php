<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Inquiry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $totalUsers = User::count();
        // return response()->json(["total_users" => $totalUsers]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function getUserCount()
    {
        $userCount = User::count(); // Fetch total users

        return response()->json([
            "message" => "Total registered users",
            "userCount" => $userCount
        ]);
    }

    public function getUserDashboardData()
    {
        $userId = Auth::id(); // Get authenticated user ID

        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Count total listed products by the user (with caching)
        $totalListedProducts = Cache::remember("user_{$userId}_products_count", 60, function () use ($userId) {
            return Product::where('user_id', $userId)->count();
        });

        // Count total inquiries for the user's products (with caching)
        $totalInquiries = Cache::remember("user_{$userId}_inquiries_count", 60, function () use ($userId) {
            return Inquiry::where('user_id', $userId)->count();
        });

        return response()->json([
            'auth_user_id' => $userId,
            'total_listed_products' => $totalListedProducts,
            'total_inquiries' => $totalInquiries,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
