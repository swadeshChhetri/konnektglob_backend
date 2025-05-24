<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Seller;
use Illuminate\Validation\ValidationException;

use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function store(Request $request)
    {
        try {

            $user = Auth::user();

            // Ensure user is logged in
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $validated = $request->validate([
                'company_name' => 'required|string|max:255',
                'contact_name' => 'required|string|max:255',
                'contact_email' => 'required|email|unique:sellers,contact_email',
                'contact_phone' => 'nullable|string|max:15',
            ]);

            // Add user_id automatically
            $seller = Seller::create([
                'user_id' => $user->id, // Assign authenticated user's ID
                'company_name' => $validated['company_name'],
                'contact_name' => $validated['contact_name'],
                'contact_email' => $validated['contact_email'],
                'contact_phone' => $validated['contact_phone'] ?? null,
            ]);

            return response()->json(['success' => true, 'seller' => $seller], 201);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }
    }

    public function show()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $seller = Seller::where('user_id', $user->id)->first();

        if (!$seller) {
            return response()->json(['success' => false, 'message' => 'Seller profile not found'], 404);
        }

        return response()->json(['success' => true, 'seller' => $seller], 200);
    }
}
