<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Seller;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query(); // Start query builder

        // If user_id is provided, filter products by user_id
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $products = $query->get()->map(function($product) {
            $product->banner_image = $product->banner_image ?: null;
            return $product;
        }); // Get the products

        return response()->json(['products' => $products]);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        // try {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'price' => 'required|numeric|min:0',
            'min_order' => 'required|integer|min:1',
            'unit' => 'required|string|in:Piece,Kg,Meter,Ton,Box',
            'category_id' => 'required|exists:categories,id',
            'city' => 'required|string|max:255',
            'banner_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // Add this validation
        ]);

        // Get the authenticated user
        $user = auth()->user(); // Ensure user is authenticated
        $validatedData['user_id'] = $user->id;

        // Handle Image Upload
        if ($request->hasFile("banner_image")) {
            $validatedData["banner_image"] = $request->file("banner_image")->store("products", "public");
        }


        // Create Product
        $product = Product::create($validatedData);

        // Convert user to seller if not already
        if (!$user->is_seller) {
            $user->update(['is_seller' => true]);
        }

        // Check if user is already a seller in `sellers` table
        // if (!Seller::where('user_id', $user->id)->exists()) {
        //     Seller::create(['user_id' => $user->id]);
        // }



        return response()->json(['message' => 'Product posted successfully', 'product' => $product], 201);
        // } catch (ValidationException $e) {
        //     return response()->json([
        //         'message' => 'Validation Error',
        //         'errors' => $e->errors(),
        //     ], 422);
        // }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json([
            'product' => $product
        ]);
    }

    public function getProductWithSeller($productId)
    {
        $product = Product::with(['user', 'seller'])->find($productId);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json([
            'product' => $product,
            'seller' => $product->seller // Getting seller details from the sellers table
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function myProducts()
    {
        $user = auth()->user(); // Get the authenticated user

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $products = Product::where('user_id', $user->id)->get()->map(function($product) {
            $product->banner_image = $product->banner_image ?: null;
            return $product;
        }); 

        return response()->json(['products' => $products]);
    }

    public function productDetails($id)
    {
        // Validate ID (Ensure it's a numeric value)
        if (!is_numeric($id)) {
            return response()->json(['message' => 'Invalid product ID'], 400);
        }

        // Fetch product by ID
        $product = Product::find($id);

        // If product doesn't exist, return 404
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json(['product' => $product], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find the product by ID
        $product = Product::find($id);

        // Check if the product exists
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Optional: Check if the authenticated user owns this product
        if ($product->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $product->delete(); // Delete the product
            return response()->json(['message' => 'Product deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete product', 'error' => $e->getMessage()], 500);
        }
    }
}
