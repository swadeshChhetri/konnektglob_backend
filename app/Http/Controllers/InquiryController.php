<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\User;
use App\Mail\InquiryMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;


class InquiryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id'         => 'nullable|exists:products,id',
            'user_name'          => 'required|string',
            'user_email'         => 'required|email',
            'message'            => 'required|string',
            'quantity'           => 'required|integer',
            'approx_order_value' => 'required|numeric',
        ]);

        $validated['user_id'] = auth()->id();

        $inquiry = Inquiry::create($validated);

        // Optionally, trigger additional notifications or events here

        return response()->json([
            'message' => 'Inquiry submitted successfully',
            'inquiry' => $inquiry,
        ], 200);
    }

    // Retrieve inquiries for the logged-in seller (assuming authentication)
    public function index(Request $request)
    {
        // If using authentication, you can get the seller's id like:
        // $sellerId = $request->user()->id;
        // Otherwise, you might pass the seller_id as a parameter.
        $sellerId = $request->input('seller_id');

        $inquiries = Inquiry::where('seller_id', $sellerId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'inquiries' => $inquiries
        ]);
    }

    public function showInquiries()
    {
        // Get the authenticated seller's user_id
        $sellerUserId = auth()->user()->id;

        // Fetch inquiries for products listed by this seller
        $inquiries = DB::table('inquiries')
            ->join('products', 'inquiries.product_id', '=', 'products.id')
            ->where('products.user_id', $sellerUserId) // Match user_id
            ->select('inquiries.*', 'products.name as product_name')
            ->get();

            return response()->json($inquiries);
    }
}
